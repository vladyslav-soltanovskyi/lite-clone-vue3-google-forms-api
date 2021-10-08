<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Quiz as QuizResource;

class ResponseResource extends JsonResource
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
            'questions' => $this->questions,
            'score' => $this->score,
            'totalScore' => $this->totalScore,
            'quiz' => new QuizResource($this->quiz),
            'user' => new UserResource($this->user)
        ];
    }
}
