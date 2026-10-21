<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'creator_id',
        'category_id',
        'description',
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function customers()
    {
        return $this->hasMany(SurveyCustomer::class);
    }

    public function answers()
    {
        return $this->hasManyThrough(SurveyAnswer::class, SurveyCustomer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
