<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Contract;
use App\Models\MaterialReservation;
use App\Models\Message;
use App\Models\MutualizationContribution;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectQuestion;
use App\Models\User;
use App\Observers\ProjectObserver;
use App\Policies\ContractPolicy;
use App\Policies\ContributionPolicy;
use App\Policies\MaterialReservationPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ProjectQuestionPolicy;
use App\Services\Payments\MockPaymentGateway;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(ProjectQuestion::class, ProjectQuestionPolicy::class);

        Gate::define('access-backoffice', fn (User $user): bool => $user->canAccessBackOffice());

        Project::observe(ProjectObserver::class);

        // The public-facing shell (layouts.app / layouts.guest) relies on
        // Alpine for the sidebar, modals and dropdowns on every page - but
        // Livewire only auto-injects its Alpine-bundling script when a
        // Livewire component actually renders. Plain Blade views (the
        // landing page, the public contract-verification page) would
        // otherwise ship with no Alpine at all.
        Livewire::forceAssetInjection();

        // Belt-and-suspenders alongside bootstrap/app.php's trustProxies():
        // hosts like Railway terminate TLS at the edge and proxy plain HTTP
        // internally, so force https:// on generated URLs/assets regardless
        // of whether the X-Forwarded-Proto header is correctly relayed.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
