<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyCustomer extends Model
{
    protected $table = 'surveys_customers';

    protected $fillable = [
        'survey_id',
        'customer_id',
        'assigned_user_id',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }
}
