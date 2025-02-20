<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ResponsesModel extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $table = 'responses';
    protected $primaryKey = 'id';
    protected $fillable = ['form_id', 'user_id'];

    public function form()
    {
        return $this->belongsTo(FormsModel::class, 'form_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(AnswersModel::class, 'response_id', 'id');
    }
}
