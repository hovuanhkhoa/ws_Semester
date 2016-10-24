<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{


    //
    public function flightsDeparture(){
        return $this->hasMany('App\Flight','Departure_airport','Code');
    }

}
