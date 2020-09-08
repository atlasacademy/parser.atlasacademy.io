<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_id');
            $table->enum('type', ['simple', 'full']);
            $table->text('image');
            $table->string('submitter');
            $table->unsignedSmallInteger('status')->default(0);
            $table->text('parse')->nullable();
            $table->unsignedBigInteger('export_id')->nullable();
            $table->unsignedSmallInteger('export_order')->nullable();
            $table->timestamps();

            $table->index('node_id');
            $table->index('submitter');
            $table->index('status');
            $table->index(['export_id', 'export_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
    }
}
