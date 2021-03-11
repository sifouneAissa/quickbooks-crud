<?php

namespace Aissaio\QuickBooksCrud;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class QuickbooksCrudServiceProvider extends ServiceProvider {



    public function register()
    {
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }


    public function boot(){
        // publish 
        $this->publishes([
            __DIR__.'/../database/migrations/2020_06_07_080742_create_quick_book_configs_table.php' => database_path('migrations/2020_06_07_080742_create_quick_book_configs_table.php')
        ],
        'quickbook-crud-migrations'
        );


        $this->publishes([
            __DIR__.'./Stubs/QuickBookConfigStub.php' => app_path('Models/QuickBookConfig.php')
        ],
        'quickbook-crud-models'
        );

        
    }


}