<?php

namespace App\Providers;

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
        // Spatie's PermissionRegistrar registers its own Gate::before that resolves
        // permission checks, so no Gate::define() loops are needed here.
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole(RolesEnum::ACCOUNT_ADMINS->value) ? true : null;
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
