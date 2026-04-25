<?php

namespace App\Providers;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\TenantObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->loadMigrationsFrom(database_path('migrations/landlord'));
        $this->configureAuthorization();
        $this->registerObservers();
    }

    /**
     * Register Eloquent model observers.
     */
    protected function registerObservers(): void
    {
        Tenant::observe(TenantObserver::class);
    }

    /**
     * Configure Gate-level authorization rules.
     */
    protected function configureAuthorization(): void
    {
        // Super-admin bypass: accountAdmins role always passes every check.
        // Spatie's PermissionRegistrar registers its own Gate::before that resolves
        // permission checks, so no Gate::define() loops are needed here.
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole(RolesEnum::ACCOUNT_ADMINS->value) ? true : null;
        });

        // Reports access: any user with the reports.VIEW permission within the current tenant.
        // Manager-scope filtering (community/building restriction) is applied at the query
        // level in each report controller method via ->forManager($user) — not here.
        Gate::define('reports.VIEW', function (User $user): bool {
            $permission = PermissionSubject::Reports->value.'.'.PermissionAction::VIEW->value;

            return $user->can($permission);
        });

        Gate::define('manage-user-role-assignments', function (User $authUser, User $targetUser): bool {
            $tenant = Tenant::current();

            if ($tenant === null) {
                return false;
            }

            if (! $authUser->hasAnyRole([RolesEnum::ACCOUNT_ADMINS->value, RolesEnum::ADMINS->value])) {
                return false;
            }

            return AccountMembership::where('user_id', $targetUser->id)
                ->where('account_tenant_id', $tenant->id)
                ->exists();
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
