<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = ['Code', 'Departure_airport', 'Arrival_airport', 'Date','Time','Class','Fare_type','Number_of_seats','Fare'];
    protected $primaryKey = ['Code','Date','Class','Fare_type'];
    public $incrementing = false;
    public $timestamps = false;

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
