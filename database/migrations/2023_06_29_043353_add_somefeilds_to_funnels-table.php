<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomefeildsToFunnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('funnels', 'file')) {
            Schema::table('funnels', function (Blueprint $table) {
                $table->string('file')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->myDropColumnIfExists();
    }

    public function myDropColumnIfExists()
    {
        $column = 'file';
        $myTable = 'funnels';
        if (Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->dropColumn('file');
            });
        }

    }

}
