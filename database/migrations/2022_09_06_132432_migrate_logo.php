<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateLogo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Acelle\Model\Setting::get('site_logo_small')) {
            \Acelle\Model\Setting::set('site_logo_light', \Acelle\Model\Setting::get('site_logo_small'));
            \Acelle\Model\Setting::set('site_logo_dark', \Acelle\Model\Setting::get('site_logo_small'));
        }
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
