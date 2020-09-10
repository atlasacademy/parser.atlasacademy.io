<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('submitter')->nullable()->change();
            $table->text('filename')->after('image');
        });

        Schema::table('exports', function (Blueprint $table) {
            $table->string('submitter')->nullable()->change();
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
            $table->string('submitter')->change();
            $table->dropColumn('filename');
        });

        Schema::table('exports', function (Blueprint $table) {
            $table->string('submitter')->change();
        });
    }
}
