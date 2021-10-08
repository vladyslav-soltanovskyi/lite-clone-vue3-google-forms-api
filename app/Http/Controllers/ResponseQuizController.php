<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResponseQuiz;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;

class ResponseQuizController extends Controller
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
    public function getAll($id) {
        $responses = ResponseQuiz::where('quiz_id', $id)->orderBy('created_at', 'asc')->get();

        if(!$responses) {
            return response()->json([
                'status' => false,
                'message' => 'Response not found'
            ])->setStatusCode(404, 'Response not found');
        }

        return response()->json([
            'status' => true,
            'message' => 'Response not found',
            'responses' => new ResponseCollection($responses)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_data = $request->only(['questions', 'user_id', 'quiz_id', 'score', 'totalScore']);
        $response = ResponseQuiz::create($request_data);

        if(!$response) {
            return response()->json([
                'status' => false,
                'message' => 'Response not store'
            ])->setStatusCode(404, 'Variant not store');
        }

        return response()->json([
            'status' => true,
            'message' => 'Response not store',
            'response' => new ResponseResource($response)
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
        $response = ResponseQuiz::find($id);

        if(!$response) {
            return response()->json([
                'status' => false,
                'message' => 'Response not found'
            ])->setStatusCode(404, 'Response not found');
        }

        return response()->json([
            'status' => true,
            'message' => 'Response found',
            'response' => new ResponseResource($response)
        ]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}