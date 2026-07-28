<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Contract\Database as DatabaseContract;
use Kreait\Firebase\Contract\Auth as AuthContract;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Factory::class, function () {
            return (new Factory)
                ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        });

        // Daftarin DUA-DUANYA: class konkret & contract interface-nya,
        // supaya controller boleh type-hint yang mana aja tanpa error.
        $this->app->singleton(Database::class, function ($app) {
            return $app->make(Factory::class)->createDatabase();
        });
        $this->app->singleton(DatabaseContract::class, function ($app) {
            return $app->make(Database::class);
        });

        $this->app->singleton(FirebaseAuth::class, function ($app) {
            return $app->make(Factory::class)->createAuth();
        });
        $this->app->singleton(AuthContract::class, function ($app) {
            return $app->make(FirebaseAuth::class);
        });
    }
}
