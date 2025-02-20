<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class QuestionsModel extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'form_id', 'choice_type', 'choices', 'is_required'];

    public function form()
    {
        return $this->belongsTo(FormsModel::class, 'form_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(AnswersModel::class, 'question_id', 'id');
    }
}
