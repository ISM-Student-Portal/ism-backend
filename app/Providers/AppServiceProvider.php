<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Classroom\ClassroomRepository;
use App\Repositories\Classroom\ClassroomRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\ClassroomService;
use App\Services\UserService;
use Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use RateLimiter;

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
        Gate::define('create-user', function (User $user) {
            return $user->is_admin;
        });
        RateLimiter::for('emails', function (object $job) {
            return Limit::perMinute(1);
        });
    }
}
