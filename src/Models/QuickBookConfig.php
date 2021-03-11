<?php

namespace Aissaio\QuickBooksCrud\Models;

use Illuminate\Database\Eloquent\Model;

class QuickBookConfig extends Model
{
    //
    protected $table = 'quick_book_configs';

    protected $columns = [
        'authorizationRequestUrl',
        'tokenEndPointUrl',
        'client_id',
        'client_secret',
        'oauth_scope',
        'oauth_redirect_uri',
        'QBORealmID',
        'refreshTokenKey',
    ];


    public function getColumns(){
        return $this->columns;
    }

    

 }
