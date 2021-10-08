<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Schema;

class CreateResponseQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_quizzes', function (Blueprint $table) {
            $table->id();
            $table->json('questions')->default(new Expression('(JSON_ARRAY())'));
            $table->integer('score')->default(0);
            $table->integer('totalScore')->default(0);

            $table->foreignId('quiz_id')
                  ->constrained()
                  ->onDelete('cascade');
                  
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('response_quizzes');
    }
}
