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
            $table->text('qrcode')->nullable()->comment('qrcode path');

            $table->foreignId('organizer_id')->constrained('users');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('evaluation_id')->nullable()->constrained()->comment('Note: the entries are configurable, so there might not always be a 100% similarity. refer to the evaluation_questions field for the final questions used in the attendee evaluation');

            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->text('location');
            $table->text('venue')->nullable()->comment('required if location field is venue');
            $table->text('online')->nullable()->comment('required if location field is online');
            $table->text('documents')->nullable();
            $table->dateTime('schedule_start');
            $table->dateTime('schedule_end');

            $table->text('evaluation_questions')->nullable()->comment('the final set of evaluation entries(modified or not) used in evaluating this event');

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
