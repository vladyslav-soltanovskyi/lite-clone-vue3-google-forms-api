<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Question extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'question' => $this->question,
            'score' => $this->score,
            'type' => $this->type,
            'position' => $this->position,
            'quiz_id' => $this->quiz_id,
            'variants' => new VariantCollection($this->variants)
        ];
    }
}
