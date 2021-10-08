<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'score', 'type', 'quiz_id', 'position'];

    public function variants()
    {
        return $this->hasMany(Variant::class, 'question_id', 'id');
    }
}
