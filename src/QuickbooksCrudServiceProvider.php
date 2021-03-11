<?php

namespace Aissaio\QuickBooksCrud;

use Illuminate\Support\ServiceProvider;

class QuickbooksCrudServiceProvider extends ServiceProvider {



    public function register()
    {
          $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }


    public function boot(){
        // publish 
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations')
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