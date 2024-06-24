<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Acelle\Model\Subscription;
use Acelle\Model\Invoice;

class CleanUpSubscriptionsInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Invoice::query()->delete();
        Subscription::where('status', '<>', Subscription::STATUS_ACTIVE)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
