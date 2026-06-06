<?php
session_start();
include("db.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$sql = "SELECT * FROM attendance WHERE student_email='$email'";
$result = mysqli_query($conn, $sql);

$total_classes = 0;
$total_attended = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $total_classes += $row['total_classes'];
    $total_attended += $row['attended_classes'];
}

$percentage = 0;

if ($total_classes > 0) {
    $percentage = round(($total_attended / $total_classes) * 100);
}

mysqli_data_seek($result, 0);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Attendance Report</title>

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

        .container{
            padding:50px;
        }

        .header{
            background:rgba(255,255,255,0.08);
            backdrop-filter:blur(10px);
            padding:25px;
            border-radius:15px;
            text-align:center;
            margin-bottom:30px;
        }

        .header h1{
            color:#00d4ff;
        }

        .percentage-box{
            display:flex;
            justify-content:center;
            margin-bottom:30px;
        }

        .circle{
            width:150px;
            height:150px;
            border-radius:50%;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:24px;
            font-weight:bold;
            color:white;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:rgba(255,255,255,0.08);
            backdrop-filter:blur(10px);
            border-radius:15px;
            overflow:hidden;
        }

        th,td{
            padding:15px;
            text-align:center;
        }

        th{
            background:rgba(0,212,255,0.3);
        }

        tr:nth-child(even){
            background:rgba(255,255,255,0.05);
        }

        .present{
            color:lightgreen;
            font-weight:bold;
        }

        .absent{
            color:#ff4d4d;
            font-weight:bold;
        }

        .btn{
            margin-top:20px;
            padding:10px 15px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            background:linear-gradient(45deg,#00d4ff,#7b2ff7);
            color:white;
        }

        .no-data{
            text-align:center;
            padding:20px;
            color:#ddd;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h1>My Attendance Report 📊</h1>
        <p>Student attendance summary</p>
    </div>

    <div class="percentage-box">

        <div class="circle"
        style="background:conic-gradient(
        #00d4ff 0% <?php echo $percentage; ?>%,
        rgba(255,255,255,0.2) <?php echo $percentage; ?>%
        );">

            <?php echo $percentage; ?>%

        </div>

    </div>

    <table>

        <tr>
            <th>Subject</th>
            <th>Total Classes</th>
            <th>Attended</th>
            <th>Status</th>
        </tr>

        <?php
        if(mysqli_num_rows($result) > 0){

            while($row = mysqli_fetch_assoc($result)){
        ?>

        <tr>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>

            <td><?php echo $row['total_classes']; ?></td>

            <td><?php echo $row['attended_classes']; ?></td>

            <td class="<?php echo ($row['status'] == 'Present') ? 'present' : 'absent'; ?>">
                <?php echo htmlspecialchars($row['status']); ?>
            </td>
        </tr>

        <?php
            }

        } else {
        ?>

        <tr>
            <td colspan="4" class="no-data">
                No attendance records found
            </td>
        </tr>

        <?php } ?>

    </table>

    <div style="text-align:center;">
        <button class="btn" onclick="goBack()">
            Back to Dashboard
        </button>
    </div>

</div>

<script>

function goBack(){
    window.location.href = "studentdashboard.php";
}

</script>

</body>
</html>