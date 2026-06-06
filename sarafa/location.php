<?php
session_start();
include("db.php");

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

if(isset($_POST['latitude']) && isset($_POST['longitude'])){

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $status = $_POST['status'];

    $sql = "INSERT INTO location_logs
            (student_email, latitude, longitude, status)
            VALUES
            ('$email','$latitude','$longitude','$status')";

    mysqli_query($conn,$sql);

    echo "saved";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Location Check</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
    linear-gradient(rgba(0,0,0,0.7),
    rgba(0,0,0,0.7)),
    url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");

    background-size:cover;
    background-position:center;
    color:white;
}

.container{
    width:400px;
    padding:30px;
    border-radius:15px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(10px);
    text-align:center;
}

h1{
    color:#00d4ff;
    margin-bottom:15px;
}

p{
    margin-bottom:15px;
    color:#ddd;
}

button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    background:linear-gradient(45deg,#00d4ff,#7b2ff7);
    color:white;
    font-size:16px;
}

#result{
    margin-top:20px;
    padding:12px;
    border-radius:10px;
    background:rgba(0,0,0,0.4);
}

</style>
</head>

<body>

<div class="container">

    <h1>📍 Location Check</h1>

    <p>Verify your attendance location</p>

    <button onclick="checkLocation()">
        Get My Location
    </button>

    <div id="result">
        Waiting for location...
    </div>

</div>

<script>

const collegeLat = 6.9271;
const collegeLon = 79.8612;
const allowedDistance = 0.5;

function checkLocation(){

    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(showPosition);

    }else{

        alert("Geolocation not supported");
    }
}

function showPosition(position){

    let userLat = position.coords.latitude;
    let userLon = position.coords.longitude;

    let distance =
    getDistance(userLat,userLon,collegeLat,collegeLon);

    let resultBox =
    document.getElementById("result");

    let status = "";

    if(distance <= allowedDistance){

        status = "Inside Campus";

        resultBox.innerHTML =
        "✅ You are inside campus.<br>Attendance Allowed!";

        resultBox.style.color = "lightgreen";

    }else{

        status = "Outside Campus";

        resultBox.innerHTML =
        "❌ You are outside campus.<br>Attendance Not Allowed!";

        resultBox.style.color = "red";
    }

    saveLocation(userLat,userLon,status);
}

function saveLocation(lat,lon,status){

    let formData = new FormData();

    formData.append("latitude",lat);
    formData.append("longitude",lon);
    formData.append("status",status);

    fetch("location.php",{
        method:"POST",
        body:formData
    })
    .then(response=>response.text())
    .then(data=>{
        console.log(data);
    });
}

function getDistance(lat1,lon1,lat2,lon2){

    let R = 6371;

    let dLat =
    (lat2-lat1) * Math.PI/180;

    let dLon =
    (lon2-lon1) * Math.PI/180;

    let a =
    Math.sin(dLat/2) *
    Math.sin(dLat/2) +

    Math.cos(lat1*Math.PI/180) *
    Math.cos(lat2*Math.PI/180) *

    Math.sin(dLon/2) *
    Math.sin(dLon/2);

    let c =
    2 * Math.atan2(
    Math.sqrt(a),
    Math.sqrt(1-a));

    return R * c;
}

</script>

</body>
</html>