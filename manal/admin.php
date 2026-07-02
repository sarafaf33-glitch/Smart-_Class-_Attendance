<?php
session_start();
include "db.php";

// அட்மின் லாகின் செய்யாமல் நேரடியாக இந்தப் பக்கத்திற்கு வந்தால் லாகின் பக்கத்திற்கு திருப்பி விடுகிறோம்
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../rifa/login.php");
    exit();
}

$error_msg = ""; // எரர் மெசேஜை சேமிக்க

/* ==========================================================================
   1. INSERT SUBJECT
   ========================================================================== */
if(isset($_POST['add'])){
    $subject_code = strtoupper(trim($_POST['subject_code'])); 
    $subject_name = trim($_POST['subject_name']);

    // அதே சப்ஜெக்ட் கோடு ஏற்கனவே இருக்கிறதா என்று முதலில் சரிபார்க்கிறோம்
    $check_stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_code = ?");
    if ($check_stmt) {
        $check_stmt->bind_param("s", $subject_code);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error_msg = "⚠️ Error: Subject Code '$subject_code' already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("ss", $subject_code, $subject_name);
                $stmt->execute();
                $stmt->close();
                
                header("Location: admin.php");
                exit();
            }
        }
        $check_stmt->close();
    }
}

/* ==========================================================================
   2. DELETE SUBJECT
   ========================================================================== */
if(isset($_POST['delete'])){
    $id = (int) $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin.php");
    exit();
}

/* ==========================================================================
   3. FETCH DATA & COUNT
   ========================================================================== */
$result = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id DESC");
$count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* BODY */
        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                        url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");
            background-size: cover;
            background-position: center;
            color: white;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            width: 100%;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
        }

        .logout-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #ff4d4d;
            color: white;
            font-weight: bold;
        }

        /* CONTAINER */
        .container {
            padding: 50px;
        }

        /* CARD */
        .card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        /* BOX */
        .box {
            background: rgba(255,255,255,0.08);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            transition: 0.3s;
        }

        .box:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.15);
        }

        /* BUTTON */
        .btn {
            margin-top: 15px;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(45deg,#00d4ff,#7b2ff7);
            color: white;
        }

        .alert {
            background: rgba(255, 77, 77, 0.2);
            border: 1px solid #ff4d4d;
            padding: 12px;
            border-radius: 5px;
            color: #ff9999;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="logo" style="font-weight: bold; font-size: 20px;">
            Admin Dashboard
        </div>
        <button class="logout-btn" onclick="logout()">Logout</button>
    </div>

    <div class="container">
        
        <div class="card">
            <h2>Welcome Admin, <?php echo htmlspecialchars($_SESSION['fullname']); ?> 👋</h2> 
            <p>Manage Smart Attendance System</p>
        </div>

        <?php if(!empty($error_msg)): ?>
            <div class="alert"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="box">
                <h3>👨‍🎓 Manage Students</h3>
                <p>Add, Edit and Delete Students</p>
                <button class="btn" onclick="openStudentsPage()">Open</button>
            </div>

            <div class="box">
                <h3>👨‍🏫 Manage Lecturers</h3>
                <p>Add and Manage Lecturers</p>
                <button class="btn" onclick="openLecturersPage()">Open</button>
            </div>

            <div class="box">
                <h3>📚 Manage Subjects</h3>
                <p>Add and Manage Subjects</p>
                <button class="btn" onclick="openSubjectsPage()">Open</button>
            </div>

            <div class="box">
                <h3>📊 Attendance Reports</h3>
                <p>View Attendance Reports</p>
                <button class="btn" onclick="openReportsPage()">Open</button>
            </div>

            
        </div>

    </div>

    <script>
        function logout() {
            alert("Logged out successfully!");
            window.location.href = "../rifa/logout.php"; 
        }

        // மாற்றம் செய்யப்பட்டுள்ள பகுதி: தற்போது manage_students.php பக்கத்திற்கு நேரடியாகச் செல்லும்
        function openStudentsPage() { 
            window.location.href = "manage_students.php"; 
        }
        
        function openLecturersPage() { window.location.href = "lectures.php"; }
        function openSubjectsPage() { window.location.href = "subject.php"; } 
        function openReportsPage() { window.location.href = "attendance.php"; }

        const campusLat = 6.927079;
        const campusLon = 79.861244;

        function getLiveLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showLocation, showError);
            } else {
                alert("GPS not supported");
            }
        }

        function showLocation(position) {
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;

            let distance = calculateDistance(lat, lon, campusLat, campusLon);
            let status = (distance <= 0.5) ? "Inside Campus" : "Outside Campus";

            fetch("save_location.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "latitude=" + lat + "&longitude=" + lon + "&distance=" + distance + "&status=" + status
            })
            .then(response => response.text())
            .then(data => {
                console.log("Database Response: " + data);
            });

            let result = document.getElementById("locResult");
            result.innerHTML = "📍 Lat: " + lat.toFixed(5) + "<br>📍 Lon: " + lon.toFixed(5) + "<br>📏 Distance: " + distance.toFixed(2) + " km <br><br>";

            if (distance <= 0.5) {
                result.innerHTML += "✅ Inside Campus";
                result.style.color = "lightgreen";
            } else {
                result.innerHTML += "❌ Outside Campus";
                result.style.color = "red";
            }
        }

        function showError() {
            alert("Location permission denied!");
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            let R = 6371; 
            let dLat = (lat2 - lat1) * Math.PI / 180;
            let dLon = (lon2 - lon1) * Math.PI / 180;
            let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
            let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }
    </script>
</body>
</html>