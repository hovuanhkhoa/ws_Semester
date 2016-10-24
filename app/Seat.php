<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{


    protected $fillable = ['Code', 'Flight_code', 'Date', 'Class', 'Fare_type'];
    protected $primaryKey = ['Code','Flight_code', 'Date'];
    public $incrementing = false;
}
