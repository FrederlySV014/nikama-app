<?php

namespace App\Providers;

use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if (app()->environment('testing')) {
            Event::listen(
                ConnectionEstablished::class,
                function (ConnectionEstablished $event) {
                    $connection = $event->connection;
                    if ($connection instanceof SQLiteConnection) {
                        $connection->getPdo()->sqliteCreateFunction('gen_random_uuid', function () {
                            return (string) Str::uuid();
                        });
                    }
                }
            );
        }
    }
}
