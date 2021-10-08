<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;
use App\Http\Resources\Variant as VariantResource;
use App\Http\Resources\VariantCollection;
use Validator;

class VariantController extends Controller
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

        $request_data = $request->only(['answer', 'correct', 'question_id']);
        $variant = Variant::create($request_data);

        if(!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Variant not store'
            ])->setStatusCode(404, 'Variant not store');
        }

        return response()->json([
            'status' => true,
            'message' => 'Variant not store',
            'variant' => new VariantResource($variant)
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
        $request_data = $request->only(['answer', 'correct', 'question_id']);
        $variant = Variant::find($id);

        if(!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Variant not found'
            ])->setStatusCode(404, 'Variant not found');
        }

        foreach($request_data as $key => $data) {
            $variant->$key = $data;
        }

        $variant->save();

        return response()->json([
            'status' => true,
            'message' => 'Variant is updated'
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
        $variant = Variant::find($id);

        if(!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Variant not found'
            ])->setStatusCode(404, 'Variant not found');
        }

        $variant->delete();

        return response()->json([
            'status' => true,
            'message' => 'Variant is deleted'
        ])->setStatusCode(200);
    }
}
