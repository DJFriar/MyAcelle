<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProdutcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $myTable    = 'products';
        $column     =   'category_id';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->bigInteger($column)->unsigned()->nullable();
                $table->foreign($column)->references('id')->on('categories')->onDelete('set null');
            });
        }
        $column     =   'unit';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->text($column)->nullable();
            });
        }
        $column     =   'stock';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->integer($column)->nullable();
            });
        }
        $column     =   'name';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->string($column)->nullable();
            });
        }
        $column     =   'curency';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->string($column)->nullable();
            });
        }
        $column     =   'file';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->text($column)->nullable();
            });
        }
        $column     =   'pack';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->string($column)->nullable();
            });
        }
        $column     =   'status';
        if (!Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->string($column)->nullable();
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
        $myTable    = 'products';
        $columns    = [
            'pack',  'unit' , 'stock', 'curency', 'file', 'status', 'name'
        ];
        foreach($columns as $column) {
            $this->myDropColumnIfExists($myTable, $column);
        }

        //-- remove when has relation
        $column = 'category_id';
        Schema::table($myTable, function (Blueprint $table) use ($column) {
            $table->dropForeign([$column]);
        });
        $this->myDropColumnIfExists($myTable, $column);

    }
    // - checck exist column
    public function myDropColumnIfExists($myTable, $column)
    {
        if (Schema::hasColumn($myTable, $column)) {
            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->dropColumn($column); //drop it
            });
        }
    }

}
