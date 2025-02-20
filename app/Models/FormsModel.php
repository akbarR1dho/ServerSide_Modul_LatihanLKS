<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class FormsModel extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $table = 'forms';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'slug', 'description', 'limit_one_response', 'creator_id'];

    public function allowed_domains()
    {
        return $this->hasMany(AllowedDomainsModel::class, 'form_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionsModel::class, 'form_id', 'id');
    }

    public function responses()
    {
        return $this->hasMany(ResponsesModel::class, 'form_id', 'id');
    }

    
}
