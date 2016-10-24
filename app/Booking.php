<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $fillable = ['Seat_code', 'Book_time', 'Total_fare', 'Status'];
    protected $primaryKey = 'Seat_code';
    public $incrementing = false;

    //
}
