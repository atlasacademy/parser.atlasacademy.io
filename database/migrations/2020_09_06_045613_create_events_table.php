<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('event_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('uid');
            $table->string('name');
            $table->unsignedInteger('qp')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'uid']);
        });

        Schema::create('event_node_drops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('event_nodes')->onDelete('cascade');
            $table->string('uid');
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->unique(['node_id', 'uid', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_node_drops');
        Schema::dropIfExists('event_nodes');
        Schema::dropIfExists('events');
    }
}
