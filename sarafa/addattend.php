<?php
session_start();
include("db.php");

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

if(isset($_POST['mark_attendance'])){

    $subject = "Web Development";
    $date = date("Y-m-d");

    $check = mysqli_query($conn,
        "SELECT * FROM attendance
         WHERE student_email='$email'
         AND subject='$subject'
         AND attendance_date='$date'");

    if(mysqli_num_rows($check) == 0){

        mysqli_query($conn,
        "INSERT INTO attendance
        (student_email,subject,total_classes,attended_classes,status,attendance_date)
        VALUES
        ('$email','$subject',1,1,'Present','$date')");

        echo "<script>alert('Attendance Marked Successfully');</script>";
    }
}
?>

<!DOCTYPE html>
<html >
<head>
    <title>QR Scanner</title>

    <style>

        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* BACKGROUND */
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");
            background-size: cover;
            background-position: center;
            color: white;
        }

        /* BOX */
        .container {
            width: 400px;
            padding: 25px;
            border-radius: 15px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        /* TITLE */
        h1 {
            color: #00d4ff;
            margin-bottom: 15px;
        }

        /* CAMERA BOX */
        video {
            width: 100%;
            border-radius: 10px;
            border: 2px solid #00d4ff;
        }

        /* RESULT */
        #result {
            margin-top: 15px;
            padding: 10px;
            background: rgba(0,0,0,0.4);
            border-radius: 10px;
        }

        /* BUTTON */
        .btn {
            margin-top: 15px;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(45deg,#00d4ff,#7b2ff7);
            color: white;
        }
    </style>

</head>

<body>

    <form method="POST">
    <input type="hidden"
           name="mark_attendance"
           value="1">

    <button type="submit" class="btn">
        Mark Attendance
    </button>
</form>

    <div class="container">

        <h1>QR Scanner</h1>

        <!-- CAMERA -->
        <video id="video" autoplay></video>

        <!-- RESULT -->
        <div id="result">
            Scan a QR Code...
        </div>

        <button class="btn" onclick="stopCamera()">
            Stop Camera
        </button>

    </div>

    <!-- QR LIBRARY -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>

        function fakeScan(){

    resultBox.innerHTML =
    "✅ QR Scanned Successfully";

    document.forms[0].submit();
}

/* SESSION CHECK */
fetch('backend/check_session.php')
    .then(r => r.json())
    .then(data => {
        if (!data.logged_in) {
            window.location.href = 'login.html';
        }
    });

let video = document.getElementById("video");
let resultBox = document.getElementById("result");
let stream;

// START CAMERA
navigator.mediaDevices.getUserMedia({ video: true })
.then(function(s){
    stream = s;
    video.srcObject = stream;
})
.catch(function(err){
    alert("Camera access denied!");
});

// SIMPLE QR SIMULATION (DEMO)
function fakeScan(){
    resultBox.innerHTML = "✅ Attendance Marked Successfully!";
}

// simulate scan every 5 sec (demo purpose)
setInterval(fakeScan, 5000);

// STOP CAMERA
function stopCamera(){
    if(stream){
        stream.getTracks().forEach(track => track.stop());
        alert("Camera Stopped");
    }
}

    </script>

</body>
</html>