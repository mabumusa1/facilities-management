<?php

namespace App\Providers;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Enums\RolesEnum;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The non-model permission subjects that are handled via Gate::define().
     *
     * @var array<int, PermissionSubject>
     */
    private const GATE_SUBJECTS = [
        PermissionSubject::Reports,
        PermissionSubject::Settings,
        PermissionSubject::CompanyProfile,
        PermissionSubject::InvoiceSettings,
        PermissionSubject::LeaseSettings,
        PermissionSubject::Directories,
        PermissionSubject::Suggestions,
        PermissionSubject::Complaints,
        PermissionSubject::HomeServices,
        PermissionSubject::NeighbourhoodServices,
        PermissionSubject::VisitorAccess,
    ];

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
    }

    /**
     * Configure Gate-level authorization rules.
     */
    protected function configureAuthorization(): void
    {
        // Super-admin bypass: accountAdmins role always passes every check.
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole(RolesEnum::ACCOUNT_ADMINS->value) ? true : null;
        });

        // Gate::define() for non-model subjects (no Eloquent model to bind a policy to).
        foreach (self::GATE_SUBJECTS as $subject) {
            foreach (PermissionAction::cases() as $action) {
                $ability = $subject->value.'.'.$action->value;
                Gate::define($ability, static function (User $user) use ($ability): bool {
                    return $user->can($ability);
                });
            }
        }
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
