<?php

namespace App\Http\Controllers;



use App\Airport;
use App\Booking;
use App\Classes;
use App\Fare_type;
use App\Flight;
use App\Passenger;
use App\Seat;
use Illuminate\Http\Request;
use App\Http\Requests;
use SebastianBergmann\Comparator\Book;

class APIController extends Controller
{
    public function responseJson($data,$httpCode){
        return response()->json($data, $httpCode, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function getNextSeatCode(){
        $newCode = Booking::count('Seat_code') + 1;

        $temp = '';
        for($i = ($newCode == 0) ? 1 : $newCode; $i < 10000; $i *= 10){
            $temp .= '0';
        }

        return 'S'. $temp . $newCode;
    }


    public function indexDeparture($id = null){
        $airport = null;
        $httpCode = 200;
        if($id == null){
            $airport = Airport::all(['Code'=>'code','Name'=>'name']);
        }else{
            $airport = Airport::where('Code','=',$id)->get(['Code'=>'code','Name'=>'name']);
            if(count($airport) == 0) $httpCode=404;
        }
        return $this->responseJson($airport,$httpCode);
    }


    public function indexArrivalAirport(Request $request){
        $result = [];
        $httpCode = 200;
        $departureAirport = $request->get("departure-airport");
        if($departureAirport != null){
            $departureAirport = Airport::where('Code','=', $departureAirport)->get();
            if(count($departureAirport) == 0) $httpCode = 404;
            else{
                $arrivalAirport = $departureAirport->first()->flightsDeparture()->distinct()->get(['Arrival_airport']);
                foreach($arrivalAirport as $airport){
                    array_push($result, Airport::where('Code','=', $airport->Arrival_airport)->first(['Code'=>'code','Name'=>'name']));
                }
            }
        }else{
            $httpCode = 404;
        }
        return $this->responseJson($result,$httpCode);
    }

    public function storeBooking(Request $request){
        $flightDetails = json_decode($request->getContent())->flightDetail;

        if($request == null || $flightDetails == null) return $this->responseJson([],400);
        $newCode = $this->getNextSeatCode();

        Booking::create([
            'Seat_code'=> $newCode
        ]);

        $result = [];

        $result["id"]= $newCode;

        $result["flightDetail"] = $this->updateFlightDetailDatabase($flightDetails,$newCode);

        return $this->responseJson($result,201);
    }


    public function getSeatDetail($id){
        $seats = Seat::where('Code',$id)->get();
        $seatDetail = Booking::select(
            'Seat_code as id',
            'Book_time as bookTime',
            'Total_fare as totalfare',
            'Status as status'
        )->where('Seat_code',$id)->first();

        $passengers = Passenger::select(
            'Title as title',
            'First_name as firstName',
            'Last_Name as lastName'
        )->where('Seat_code',$id)->get();

        $result = $seatDetail;
        if($result == null || count($result) <1) return [];

        $array = [];

        foreach ($seats as $seat) {
            $flight = Flight::select(
                "Code as code",
                "Departure_airport as departureAirport",
                "Arrival_airport as arrivalAirport",
                "Date as date",
                "Time as time",
                "Number_of_seats as numOfSeats",
                "Class as class",
                "Fare_type as fareType"
            )
                ->where('Code',$seat->Flight_code)
                ->where('Date',$seat->Date)
                ->where('Class',$seat->Class)
                ->where('Fare_type',$seat->Fare_type)
                ->first();

            array_push($array,$flight);
        }
        $result["flightDetails"] =$array;
        $result["passengers"] = $passengers;
        return $result;
    }

    public function indexBooking($id = null){
        $result = [];
        if($id == null){
            $seatDetails = Booking::all();
            foreach($seatDetails as $seatDetail){
                array_push($result, $this->getSeatDetail($seatDetail->Seat_code));
            }
        }else{
            $result = $this->getSeatDetail($id);
        }
        if(count($result) >=1)
            return $this->responseJson($result,200);
        else
            return $this->responseJson($result,404);
    }

    public function updateBooking(Request $request){
        $id = $request->get("id");
        $status = $request->get("status");

        if($id == null || !is_int($status)) return $this->responseJson("",400);

        $booking = Booking::where("Seat_code",$id)->first();

        if($booking == null || count($booking) < 1) return $this->responseJson(["message"=> "Booking ID is not found"],404);

        $status = ($status != 0) ? 1: 0;
        $booking->Status = (int)$status;
        $booking->push();

        return $this->responseJson($this->getSeatDetail($id),200);
    }

    public function indexFlightDetail($id = null){
        if($id == null) return $this->responseJson(["message"=> "Booking ID is not found"],404);

        $booking = Booking::where("Seat_code",$id)->first();
        if($booking == null || count($booking) < 1) return $this->responseJson(["message"=> "Booking ID is not found"],404);
        $result = [];
        $seats = Seat::where("Code",$booking->Seat_code)->get();
        foreach ($seats as $seat) {
            $flight = Flight::select(
                "Code as code",
                "Departure_airport as departureAirport",
                "Arrival_airport as arrivalAirport",
                "Date as date",
                "Time as time",
                "Number_of_seats as numOfSeats",
                "Class as class",
                "Fare_type as fareType"
            )->where('Code',$seat->Flight_code)
                ->where('Date',$seat->Date)
                ->where('Class',$seat->Class)
                ->where('Fare_type',$seat->Fare_type)->first();
            array_push($result, $flight);
        }
        return $this->responseJson($result,200);
    }

    public function updateFlightDetailDatabase($arrayFlight, $id){
        $result = [];
        foreach ($arrayFlight as $flightDetail) {
            $flightID = $flightDetail->flightId;
            $class = $flightDetail->class;
            $fareType = $flightDetail->fareType;
            $date = $flightDetail->date;
            if ($flightID == null || $class == null || $fareType == null || $date == null) {
                return null;
            }

            $flight = Flight::select(
                "Code as code",
                "Departure_airport as departureAirport",
                "Arrival_airport as arrivalAirport",
                "Date as date",
                "Time as time",
                "Number_of_seats as numOfSeats",
                "Class as class",
                "Fare_type as fareType"
            )->where('Code', $flightID)
                ->where('Date', $date)
                ->where('Class', $class)
                ->where('Fare_type', $fareType)->first();

            array_push($result,$flight);

            Seat::updateOrCreate([
                'Code' => $id,
                'Flight_code' => $flightID,
                'Date' => $date,
                'Class' => $class,
                'Fare_type' => $fareType
            ]);
        }
        return $result;
    }


    public function updateFlightDetail($id = null, Request $request){
        if($id == null) return $this->responseJson(["message"=> "Booking ID is not found"],404);
        $flightDetails = json_decode($request->getContent());
        $result = $this->updateFlightDetailDatabase($flightDetails,$id);
        if($result != null)
            return $this->responseJson($result,201);
        else
            return $this->responseJson('',400);
    }

    public function  getPassengerDetail($id){
        return Passenger::select(
            'Title as title',
            'First_name as firstName',
            'Last_Name as lastName'
        )->where("Seat_code", $id)->get();
    }



    public function indexPassengerDetail($id = null){
        if($id == null) return $this->responseJson(["message"=> "Booking ID is not found"],404);
        return $this->responseJson($this->getPassengerDetail($id),200);
    }

    public function updateFlightsDatabase(){

    }

    public function updatePassengerDetail($id = null,Request $request){
        if($id == null) return $this->responseJson(["message"=> "Booking ID is not found"],404);

        $seat = Seat::where("Code",$id)->first();

        $fare = Flight::where('Code',$seat->Flight_code)
            ->where('Date',$seat->Date)
            ->where('Class',$seat->Class)
            ->where('Fare_type',$seat->Fare_type)
            ->first()->Fare;

        echo ($fare);

        $array = json_decode( $request->getContent(), true );
        for($i = 0, $n = count($array); $i<$n; $i++){
            if($array[$i]['title'] == null || $array[$i]['lastName']== null
                || $array[$i]['firstName']== null) return $this->responseJson("",400);

            Passenger::updateOrCreate([
                'Seat_code' => $id,
                'id' => $i,
                'Title' => $array[$i]['title'],
                'Last_name' => $array[$i]['lastName'],
                'First_name'=> $array[$i]['firstName']
            ]);
        }

        $booking = Booking::where("Seat_code",$id)->first();
        $booking->Total_fare = $fare * Passenger::where("Seat_code",$id)->count();
        $booking->Book_time = date('Y-m-d H:i:s');
        $booking->save();

        return $this->responseJson($request->all(),201);
    }



    public function indexFlightSearch(Request $request){
        $departureAirport = $request->get("departure-airport");
        $arrivalAirport = $request->get("arrival-airport");
        $departDay = $request->get("depart-day");
        $numPassengers = $request->get("num-passenger");

        if($departureAirport == null || $arrivalAirport == null || $departDay == null || $numPassengers == null)
            return $this->responseJson("",400);

        $flight = Flight::where('Departure_airport', $departureAirport)
            ->where('Date', $departDay)
            ->where('Arrival_airport', $arrivalAirport)
            ->where('Number_of_seats','>=', $numPassengers)->get();

        if(count($flight) <=0)
            return $this->responseJson("",404);
        else
            return $this->responseJson($flight,200);
    }


    public function indexFareType(){
        return $this->responseJson(Fare_type::select(
            'Code as code',
            'Name as name'
        )->get(),200);
    }

    public function indexClass(){
        return $this->responseJson(Classes::select(
            'Code as code',
            'Name as name'
        )->get(),200);
    }


}