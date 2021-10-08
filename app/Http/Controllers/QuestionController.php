<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Variant;
use Illuminate\Http\Request;
use App\Http\Resources\Question as QuestionResource;
use App\Http\Resources\QuestionCollection;
use Validator;


class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request_data = $request->only(['question', 'score', 'position', 'type', 'quiz_id']);
        $question = Question::create($request_data);

        if(!$question) {
            return response()->json([
                'status' => false,
                'message' => 'Question not store'
            ])->setStatusCode(404, 'Question not store');
        }

        $variant = Variant::create([
            'answer' => 'Вариант 1',
            'correct' => 0,
            'question_id' => $question->id
        ]);
        
        if(!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Variant not store'
            ])->setStatusCode(404, 'Variant not store');
        }

        return response()->json([
            'status' => true,
            'message' => 'Question is store',
            'question' => new QuestionResource($question)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request_data = $request->only(['question', 'score', 'type', 'quiz_id']);
        $question = Question::find($id);
        $types = ['radio', 'checkbox'];

        if(!$question) {
            return response()->json([
                'status' => false,
                'message' => 'Question not found'
            ])->setStatusCode(404, 'Question not found');
        }

        if(!is_null($request['type'])) {
            if(in_array($question->type, $types) && !in_array($request_data['type'], $types)) {
                Variant::where('question_id', $id)->delete();
                Variant::create([
                    'answer' => '',
                    'correct' => 0,
                    'question_id' => $id
                ]);
            }
            elseif(!in_array($question->type, $types) && in_array($request_data['type'], $types)) {
                Variant::where('question_id', $id)->delete();
                Variant::create([
                    'answer' => 'Вариант 1',
                    'correct' => 0,
                    'question_id' => $question->id
                ]);
            }
        }

        foreach($request_data as $key => $data) {
            $question->$key = $data;
        }

        $question->save();

        return response()->json([
            'status' => true,
            'message' => 'Question is updated',
            'question' => new QuestionResource($question)
        ])->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = Question::find($id);

        if(!$question) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not found'
            ])->setStatusCode(404, 'Question not found');
        }

        $question->delete();

        return response()->json([
            'status' => true,
            'message' => 'Question is deleted'
        ])->setStatusCode(200);
    }

    public function moveQuestions(Request $request)
    {

        $request_data = $request->only(['questions']);
        $questions = [];

        if(is_null($request_data)) {
            return response()->json([
                'status' => false,
                'message' => 'Question not store'
            ])->setStatusCode(404, 'Question not store');
        }
        
        foreach ($request_data['questions'] as $index => $data) {
            $question = Question::find($data['id']);
            $question->position = $index;
            $question->save();
            $questions[] = $question;
        }

        return response()->json([
            'status' => true,
            'message' => 'Questions is updated',
            'questions' => new QuestionCollection($questions)
        ]);
    }
}
