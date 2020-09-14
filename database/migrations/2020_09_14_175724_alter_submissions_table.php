<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('parse_hash')->nullable()->after('parse');
            $table->unsignedSmallInteger('drop_count')->nullable()->after('parse_hash');
            $table->unsignedBigInteger('qp_total')->nullable()->after('drop_count');

            $table->index('parse_hash');
            $table->index('drop_count');
            $table->index('qp_total');
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
            $table->dropColumn('parse_hash');
            $table->dropColumn('drop_count');
            $table->dropColumn('qp_total');
        });
    }
}
