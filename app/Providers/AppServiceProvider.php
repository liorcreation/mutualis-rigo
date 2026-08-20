<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Contract;
use App\Models\MaterialReservation;
use App\Models\MutualizationContribution;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Observers\ProjectObserver;
use App\Policies\ContractPolicy;
use App\Policies\ContributionPolicy;
use App\Policies\MaterialReservationPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProjectPolicy;
use App\Services\Payments\MockPaymentGateway;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, MockPaymentGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(MutualizationContribution::class, ContributionPolicy::class);
        Gate::policy(Contract::class, ContractPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(MaterialReservation::class, MaterialReservationPolicy::class);

        Gate::define('access-backoffice', fn (User $user): bool => $user->canAccessBackOffice());

        Project::observe(ProjectObserver::class);
    }
}
