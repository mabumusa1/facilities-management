<?php

namespace App\Console\Commands;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;

class MigrateAdminRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:migrate-admin-roles
                            {--dry-run : Log what would be inserted without writing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate rf_admins.role values into model_has_roles for all tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        // 1. Resolve all 5 AdminRole system roles up-front into a name→id map.
        $expectedNames = array_column(AdminRole::cases(), 'value');

        /** @var Collection<string, int> $roleMap */
        $roleMap = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->whereIn('name', $expectedNames)
            ->pluck('id', 'name');

        $missing = array_diff($expectedNames, $roleMap->keys()->all());

        if (! empty($missing)) {
            $this->error('The following roles are missing from the database (run RbacSeeder first): '.implode(', ', $missing));

            return self::FAILURE;
        }

        // 2. Resolve morph alias for Admin model once.
        $morphAlias = Relation::getMorphedModel(Admin::class) ?? Admin::class;
        // Reverse lookup: morphMap maps alias→FQCN; we need FQCN→alias.
        $map = Relation::morphMap();
        $morphType = array_search(Admin::class, $map);
        if ($morphType === false) {
            $morphType = Admin::class;
        }

        $totalInserted = 0;
        $totalSkippedExisting = 0;
        $totalSkippedInvalid = 0;

        // 3. Chunk through all admins across all tenants.
        Admin::withoutGlobalScopes()->chunkById(200, function ($admins) use (
            $roleMap,
            $morphType,
            $isDryRun,
            &$totalInserted,
            &$totalSkippedExisting,
            &$totalSkippedInvalid,
        ): void {
            $adminIds = $admins->pluck('id')->all();

            // Pre-fetch existing (model_id, role_id) pairs for this chunk.
            $existing = DB::table('model_has_roles')
                ->where('model_type', $morphType)
                ->whereIn('model_id', $adminIds)
                ->get(['model_id', 'role_id'])
                ->mapToGroups(fn ($row) => [$row->model_id => $row->role_id])
                ->map(fn ($ids) => $ids->all());

            $toInsert = [];

            foreach ($admins as $admin) {
                // Retrieve raw role string from attributes to avoid enum cast errors.
                $rawRole = $admin->getRawOriginal('role');

                if ($rawRole === null) {
                    Log::warning("MigrateAdminRoles: admin {$admin->id} has null role — skipped");
                    $totalSkippedInvalid++;

                    continue;
                }

                if (! $roleMap->has($rawRole)) {
                    Log::warning("MigrateAdminRoles: admin {$admin->id} has unrecognised role '{$rawRole}' — skipped");
                    $totalSkippedInvalid++;

                    continue;
                }

                $roleId = $roleMap->get($rawRole);

                $existingRoleIds = $existing->get($admin->id, []);
                if (in_array($roleId, $existingRoleIds, strict: true)) {
                    $totalSkippedExisting++;

                    continue;
                }

                $toInsert[] = [
                    'role_id' => $roleId,
                    'model_type' => $morphType,
                    'model_id' => $admin->id,
                    'community_id' => null,
                    'building_id' => null,
                    'service_type_id' => null,
                ];
            }

            if (! empty($toInsert) && ! $isDryRun) {
                DB::table('model_has_roles')->insert($toInsert);
            }

            $totalInserted += count($toInsert);
        });

        if (! $isDryRun) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $this->info("MigrateAdminRoles complete: {$totalInserted} inserted, {$totalSkippedExisting} skipped (already existed), {$totalSkippedInvalid} skipped (null/invalid role).");

        if ($isDryRun) {
            $this->warn('Dry-run mode — no rows were written.');
        }

        return self::SUCCESS;
    }
}
