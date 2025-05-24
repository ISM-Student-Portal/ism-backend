<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Classroom\ClassroomRepository;
use App\Repositories\Classroom\ClassroomRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\ClassroomService;
use App\Services\UserService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate as FacadesGate;
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->app->bind(ClassroomRepositoryInterface::class, ClassroomRepository::class);
        $this->app->bind(ClassroomService::class, function ($app) {
            return new ClassroomService($app->make(ClassroomRepositoryInterface::class));
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        FacadesGate::define('create-user', function (User $user) {
            return $user->is_admin;
        });
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        if (config('app.env') === 'testing') {
            $this->app->useDatabasePath('database/testing');
        }
        FacadesRateLimiter::for('emails', function (object $job) {
            return Limit::perMinute(1);
        });
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('International School of Ministry - Registration')
                ->view('emails.verify-email-custom', ['url' => $url, 'user' => $notifiable]);
        });
    }
}
