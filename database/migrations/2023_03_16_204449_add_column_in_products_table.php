<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInProductsTable extends Migration
{
    private $table_names = ['products_ins', 'products_aes', 'products_sas'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     
        foreach($this->table_names as $table_name) {

            Schema::table($table_name, function (Blueprint $table) {
                $table->tinyInteger("current_availability")->default(0)->comment("Current Store Availability - 0 = Unprocessed, 1 = active, 2 = Inactive");
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
        foreach($this->table_names as $table_name) {

            Schema::table($table_name, function (Blueprint $table) {
                $table->dropColumn('current_availability');
            });

        }
    }
}
