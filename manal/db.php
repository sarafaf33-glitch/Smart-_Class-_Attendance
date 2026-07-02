<?php
include 'db.php';

$sql = "SELECT * FROM attendance";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)){
    echo "<tr>";
    echo "<td>".$row['student_email']."</td>";
    echo "<td>".$row['subject']."</td>";
    echo "<td>".$row['date']."</td>";
    echo "<td>".$row['status']."</td>";
    echo "</tr>";
}
?>