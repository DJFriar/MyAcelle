<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixFieldUid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Acelle\Model\Field::all() as $field) {
            foreach (Acelle\Model\Field::where('id', '!=', $field->id)->where('uid', '=', $field->uid)->get() as $field2) {
                $field2->uid = uniqid();
                $field2->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            //
        });
    }
}
