<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'state',
        'city',
        'residence_address',
        'phone_number',
        'email_address',
    ];
}
