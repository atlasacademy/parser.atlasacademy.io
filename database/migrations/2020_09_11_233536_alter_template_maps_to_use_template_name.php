<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTemplateMapsToUseTemplateName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('template_maps')->truncate();
        Schema::table('template_maps', function (Blueprint $table) {
            $table->increments('id')->change();
            $table->string('name')->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('template_maps')->truncate();
        Schema::table('template_maps', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
