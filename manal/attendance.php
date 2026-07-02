<?php
include "db.php";

/* Total students */
$totalStudents = 0;
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM students");
if ($total_query) {
    $total_data = mysqli_fetch_assoc($total_query);
    $totalStudents = $total_data['total'];
}

/* Present today */
$present = 0;

// டேட்டாபேஸ் பத்திகளைச் சரிபார்க்கிறோம் (Error வராமல் தடுக்க)
$check_date = mysqli_query($conn, "SHOW COLUMNS FROM attendance LIKE 'date'");
$check_percentage = mysqli_query($conn, "SHOW COLUMNS FROM attendance LIKE 'percentage'");
$check_subject = mysqli_query($conn, "SHOW COLUMNS FROM attendance LIKE 'subject'");

$has_date = mysqli_num_rows($check_date) > 0;
$has_percentage = mysqli_num_rows($check_percentage) > 0;
$has_subject = mysqli_num_rows($check_subject) > 0;

if ($has_date && $has_percentage) {
    $present_query = mysqli_query($conn, "
        SELECT COUNT(DISTINCT student_id) as total
        FROM attendance
        WHERE date = CURDATE() AND percentage >= 50
    ");
} elseif ($has_date) {
    $present_query = mysqli_query($conn, "
        SELECT COUNT(DISTINCT student_id) as total
        FROM attendance
        WHERE date = CURDATE()
    ");
} else {
    $present_query = mysqli_query($conn, "SELECT COUNT(DISTINCT student_id) as total FROM attendance");
}

if ($present_query) {
    $present_data = mysqli_fetch_assoc($present_query);
    $present = $present_data['total'];
}

if ($present > $totalStudents) {
    $present = $totalStudents;
}

$absent = $totalStudents - $present;

/* Attendance report */
$pct_field = $has_percentage ? "a.percentage" : "100";

// 'subject' காலம் இருந்தால் சப்ஜெக்ட் வாரியாக பிரிக்கும், இல்லையென்றால் எரர் வராமல் 0 என்று காட்டும்
if ($has_subject) {
    $sql = "
    SELECT 
        s.name,
        s.reg_no,
        MAX(CASE WHEN LOWER(a.subject) LIKE '%java%' THEN $pct_field ELSE 0 END) AS java,
        MAX(CASE WHEN LOWER(a.subject) LIKE '%web%' THEN $pct_field ELSE 0 END) AS web,
        MAX(CASE WHEN LOWER(a.subject) LIKE '%dbms%' THEN $pct_field ELSE 0 END) AS dbms
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id
    GROUP BY s.id, s.name, s.reg_no
    ";
} else {
    // 'subject' காலம் இல்லாத போது எரரைத் தவிர்க்கும் பாதுகாப்பான குவறி
    $sql = "
    SELECT 
        s.name,
        s.reg_no,
        0 AS java,
        0 AS web,
        0 AS dbms
    FROM students s
    GROUP BY s.id, s.name, s.reg_no
    ";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Reports</title>

<style>
body{
    font-family: Arial, sans-serif;
    margin:0;
    color:white;
    background: linear-gradient(rgba(0,0,0,0.75),rgba(0,0,0,0.75)),
    url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");
    background-size: cover;
    background-attachment: fixed;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 25px;
    background: rgba(0,0,0,0.7);
    position: sticky;
    top:0;
    z-index:1000;
}

.logo{
    font-size:20px;
    font-weight:bold;
}

.back-btn{
    padding:8px 15px;
    border:none;
    border-radius:6px;
    background:#00d4ff;
    color: black;
    font-weight: bold;
    cursor:pointer;
}

/* CONTAINER */
.container{
    padding:30px;
}

/* SUMMARY */
.summary{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap:15px;
    margin-bottom:20px;
}

.box{
    background: rgba(255,255,255,0.08);
    padding:20px;
    border-radius:10px;
    text-align:center;
    backdrop-filter: blur(5px);
}

/* CARD */
.card{
    background: rgba(255,255,255,0.08);
    padding:20px;
    border-radius:10px;
    backdrop-filter: blur(5px);
    overflow-x:auto;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    min-width:700px;
}

th,td{
    padding:12px;
    text-align:center;
    white-space:nowrap;
}

th{
    background: rgba(0,212,255,0.3);
    color: #00d4ff;
}

tr:nth-child(even){
    background: rgba(255,255,255,0.05);
}

/* STATUS */
.good{ color:#00ff99; font-weight:bold; }
.avg{ color:#ffcc00; font-weight:bold; }
.low{ color:#ff4d4d; font-weight:bold; }

/* MOBILE */
@media(max-width:768px){
    .container{ padding:15px; }
}
</style>

</head>

<body>

<div class="navbar">
    <div class="logo">Attendance Reports</div>
    <button class="back-btn" onclick="goBack()">Back</button>
</div>

<div class="container">

<div class="summary">

    <div class="box">
        <h3>Total Students</h3>
        <p><?php echo $totalStudents; ?></p>
    </div>

    <div class="box">
        <h3>Present Today</h3>
        <p><?php echo $present; ?></p>
    </div>

    <div class="box">
        <h3>Absent Today</h3>
        <p><?php echo $absent; ?></p>
    </div>

    <div class="box">
        <h3>Attendance Rate</h3>
        <p>
        <?php 
        echo ($totalStudents > 0)
            ? round(($present/$totalStudents)*100) . "%"
            : "0%";
        ?>
        </p>
    </div>

</div>

<div class="card">

<h2>📊 Student Attendance Report</h2>

<table>

<tr>
    <th>Student</th>
    <th>Reg No</th>
    <th>Java</th>
    <th>Web</th>
    <th>DBMS</th>
    <th>Total</th>
    <th>Status</th>
</tr>

<?php 
if($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {

        $java_val = isset($row['java']) ? (int)$row['java'] : 0;
        $web_val = isset($row['web']) ? (int)$row['web'] : 0;
        $dbms_val = isset($row['dbms']) ? (int)$row['dbms'] : 0;

        // சராசரி கணக்கிடுதல்
        $total = round(($java_val + $web_val + $dbms_val) / 3);

        if($total >= 75){
            $status = "Good";
            $class = "good";
        }
        elseif($total >= 50){
            $status = "Average";
            $class = "avg";
        }
        else{
            $status = "Low";
            $class = "low";
        }
        ?>

        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['reg_no']); ?></td>
            <td><?php echo $java_val; ?>%</td>
            <td><?php echo $web_val; ?>%</td>
            <td><?php echo $dbms_val; ?>%</td>
            <td><?php echo $total; ?>%</td>
            <td class="<?php echo $class; ?>">
                <?php echo $status; ?>
            </td>
        </tr>

        <?php 
    } 
} else { ?>
    <tr>
        <td colspan="7">No records found!</td>
    </tr>
<?php } ?>

</table>

</div>

</div>

<script>
function goBack(){
    window.location.href = "admin.php";
}
</script>

</body>
</html>