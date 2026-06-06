<?php
session_start();

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$student_name = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial,sans-serif;
        }

        body{
            background:linear-gradient(rgba(0,0,0,0.7),
            rgba(0,0,0,0.7)),
            url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");
            background-size:cover;
            background-position:center;
            color:white;
            min-height:100vh;
        }

        .navbar{
            width:100%;
            padding:20px 50px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            background:rgba(255,255,255,0.08);
            backdrop-filter:blur(10px);
        }

        .logo{
            font-size:24px;
            font-weight:bold;
            color:#00d4ff;
        }

        .logout-btn{
            padding:10px 18px;
            border:none;
            border-radius:8px;
            background:#ff4d4d;
            color:white;
            cursor:pointer;
        }

        .container{
            padding:50px;
        }

        .card{
            background:rgba(255,255,255,0.08);
            backdrop-filter:blur(10px);
            padding:30px;
            border-radius:15px;
            margin-bottom:30px;
        }

        .card h2{
            color:#00d4ff;
            margin-bottom:10px;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
        }

        .box{
            background:rgba(255,255,255,0.08);
            padding:25px;
            border-radius:15px;
            text-align:center;
            transition:0.3s;
        }

        .box:hover{
            transform:translateY(-8px);
            background:rgba(255,255,255,0.15);
        }

        .box h3{
            color:#00d4ff;
            margin-bottom:10px;
        }

        .btn{
            margin-top:10px;
            padding:10px 15px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            background:linear-gradient(45deg,#00d4ff,#7b2ff7);
            color:white;
        }

    </style>
</head>

<body>

<div class="navbar">

    <div class="logo">
        Student Dashboard
    </div>

    <button class="logout-btn" onclick="logout()">
        Logout
    </button>

</div>

<div class="container">

    <div class="card">
        <h2>Welcome <?php echo htmlspecialchars($student_name); ?> 👋</h2>
        <p>Manage your attendance and view records</p>
    </div>

    <div class="grid">

        <div class="box">
            <h3>📷 Mark Attendance</h3>
            <p>Scan QR code to mark attendance</p>
            <button class="btn" onclick="openQR()">Open</button>
        </div>

        <div class="box">
            <h3>📊 My Attendance</h3>
            <p>View your attendance report</p>
            <button class="btn" onclick="openReport()">View</button>
        </div>

        <div class="box">
            <h3>📍 Location Check</h3>
            <p>Verify your attendance location</p>
            <button class="btn" onclick="openLocation()">Check</button>
        </div>

        <div class="box">
            <h3>📅 Schedule</h3>
            <p>Check class timetable</p>
            <button class="btn" onclick="openSchedule()">Open</button>
        </div>

    </div>

</div>

<script>

function openQR(){
    window.location.href = "addattend.php";
}

function openReport(){
    window.location.href = "Attend.php";
}

function openLocation(){
    window.location.href = "location.php";
}

function openSchedule(){
    window.location.href = "Schedule.php";
}

function logout(){
    window.location.href = "logout.php";
}

</script>

</body>
</html>