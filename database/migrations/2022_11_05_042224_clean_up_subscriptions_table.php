<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CleanUpSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('subscriptions')->whereNull('customer_id')->delete();
        DB::table('subscriptions')->whereNull('plan_id')->delete();

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('customer_id')->unsigned()->nullable(false)->change();
            $table->integer('plan_id')->unsigned()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
}
