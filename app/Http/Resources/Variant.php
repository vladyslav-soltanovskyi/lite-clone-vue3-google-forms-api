<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Variant extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'answer' => $this->answer,
            'correct' => $this->correct,
            'question_id' => $this->question_id
        ];
    }
}
