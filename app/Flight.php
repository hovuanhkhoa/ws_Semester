<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{

    public function departureAirports(){
        return $this->belongsTo('App\Airport');
    }

    public function arrivalAirports(){
        return $this->belongsTo('App\Airport');
    }

    public function classes(){
        return $this->belongsTo('App\Classes');
    }

    public function fareTypes(){
        return $this->belongsTo('App\Fare_type');
    }
}
