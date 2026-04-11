<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Company;
use App\Models\InfoItem;
use App\Models\Person;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\Auth\ResetPasswordNotification;

final class AppServiceProvider extends ServiceProvider
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
        $this->configureEvents();
        $this->configureEmails();
    }

    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // DB::prohibitDestructiveCommands(
        //     app()->isProduction(),
        // );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null
        );

        Model::unguard();
        // Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();

        Relation::enforceMorphMap([
            'person' => Person::class,
            'company' => Company::class,
            'info_item' => InfoItem::class,
        ]);
    }

    private function configureEvents(): void
    {
        Event::listen(Login::class, function ($event): void {
            $event->user->update(['last_login_at' => now()]);
        });
    }

    private function configureEmails(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        // Tell the User model to use your custom notification
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new ResetPasswordNotification($token))->toMail($notifiable);
        });
    }
}
