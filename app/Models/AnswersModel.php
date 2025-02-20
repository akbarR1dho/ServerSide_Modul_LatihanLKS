<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AnswersModel extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $table = 'answers';
    protected $primaryKey = 'id';
    protected $fillable = ['value', 'question_id', 'response_id'];

    public function question()
    {
        return $this->belongsTo(QuestionsModel::class, 'question_id', 'id');
    }

    public function response()
    {
        return $this->belongsTo(ResponsesModel::class, 'response_id', 'id');
    }
}
