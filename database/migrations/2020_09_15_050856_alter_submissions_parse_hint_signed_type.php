<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubmissionsParseHintSignedType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('drop_count')->nullable()->change();
            $table->bigInteger('qp_total')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedInteger('drop_count')->nullable()->change();
            $table->unsignedBigInteger('qp_total')->nullable()->change();
        });
    }
}
