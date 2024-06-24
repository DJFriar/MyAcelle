<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Acelle\Model\TrackingDomain;

class AddTrackingDomainsVerificationMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_domains', function (Blueprint $table) {
            $table->string('verification_method');
        });

        TrackingDomain::query()->update(['verification_method' => 'host']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_domains', function (Blueprint $table) {
            //
        });
    }
}
