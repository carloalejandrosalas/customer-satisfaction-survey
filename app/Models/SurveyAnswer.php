<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    //
    protected $fillable = [
        'survey_customer_id',
        'survey_question_id',
        'value',
    ];

    public function surveyCustomer()
    {
        return $this->belongsTo(SurveyCustomer::class);
    }

    public function surveyQuestion()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
