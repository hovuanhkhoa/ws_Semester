<!DOCTYPE html>
<html lang="en">
    <head>
    </head>

    <body>
        <a href="api/departure-airports"><b>Get departure airport</b></a><br>
        <a href="api/arrival-airports?departure-airport=SGN"><b>Get arrival airport</b></a><br>
        <a href="api/bookings"><b>Get all bookings</b></a><br>
        <a href="api/bookings/S00001"><b>Get booking  with ID = S00001</b></a><br>
        <a href="api/bookings/s00001/flight-details"><b>Get flights detail of booking ID = S00001</b></a><br>
        <a href="api/bookings/s00001/passengers"><b>Get passengers detail of booking ID = S00001</b></a><br>
        <a href="api/flights?departure-airport=SGN&arrival-airport=TBB&depart-day=2016-10-05&num-passenger=20"><b>Search fligths</b></a><br>
        <b>API POST, PUT need a manual query</b>

    </body>
</html>