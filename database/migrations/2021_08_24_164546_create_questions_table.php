<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question')->nullable();
            $table->integer('score')->default(0);
            $table->string('type')->default('radio');
            $table->integer('position')->default(0);
            // $table->unsignedBigInteger('quiz_id');

            $table->foreignId('quiz_id')
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
        Schema::dropIfExists('questions');
    }
}
/*
{
            id: 4,
            question: "What year was JavaScript launched?",
            variants: [
                {
                    id: 1,
                    answer: "1996", 
                },
                {
                    id: 2,
                    answer: "1995", 
                },
                {
                    id: 3,
                    answer: "1994", 
                },
                {
                    id: 4,
                    answer: "none of the above", 
                }
            ],
            correct: 2,
            score: 1,
            type: 'radio'
        },
        */