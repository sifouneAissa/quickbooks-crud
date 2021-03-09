<?php

namespace Aissaio\QuickBooksCrud;

use Illuminate\Support\ServiceProvider;

class QuickbooksCrudServiceProvider extends ServiceProvider {



    public function register()
    {
        
    }


    public function boot(){
        // publish 
        $this->publishes([
            // __DIR__.'/config/phone_verification.php' => config_path('phone_verification.php'),

        ],
        // 'phone-verification-config'
        );

        
    }


}