<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AllowedDomainsModel extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $table = 'allowed_domains';
    protected $primaryKey = 'id';
    protected $fillable = [
        'form_id',
        'domain',
    ];

    public function form()
    {
        return $this->belongsTo(FormsModel::class, 'form_id', 'id');
    }
}
