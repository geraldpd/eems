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
            $table->string('code')->unique()->nullable()->comment('unique identifier to be used in invitation links');
            $table->foreignId('organizer_id')->constrained('users');
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->text('location');
            $table->text('documents')->nullable();
            $table->dateTime('schedule_start');
            $table->dateTime('schedule_end');
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
