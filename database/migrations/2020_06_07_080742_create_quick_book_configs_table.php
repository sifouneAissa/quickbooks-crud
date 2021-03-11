<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickBookConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quick_book_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string("authorizationRequestUrl")->default('https://appcenter.intuit.com/connect/oauth2');
            $table->string('auth')->default('oauth2');
            $table->string("tokenEndPointUrl")->default('https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer');
            $table->string("client_id")->nullable();
            $table->string("client_secret")->nullable();
            $table->string("oauth_scope")->default('com.intuit.quickbooks.accounting openid profile email phone address');
            $table->string("oauth_redirect_uri")->default('http://localhost:8484/wave');
            $table->string("QBORealmID")->nullable();
            $table->string("refreshTokenKey")->nullable();
            $table->enum("baseUrl",['production','development'])->nullable();
            $table->boolean("isCurrent");
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quick_book_configs');
    }
}
