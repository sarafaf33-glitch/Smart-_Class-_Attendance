<?php
include "db.php";

$alert_msg = ""; // அலர்ட் செய்திகளைச் சேமிக்க

/* ==========================================================================
   1. ADD SUBJECT (INSERT)
   ========================================================================== */
if(isset($_POST['add_subject'])){
    $name = mysqli_real_escape_string($conn, trim($_POST['subject_name']));
    $code = strtoupper(mysqli_real_escape_string($conn, trim($_POST['subject_code']))); // IT1101 என மாற்றும்
    $lecturer = mysqli_real_escape_string($conn, trim($_POST['lecturer']));

    // 1. அதே சப்ஜெக்ட் கோடு ஏற்கனவே இருக்கிறதா என்று சரிபார்க்கிறோம்
    $check = mysqli_query($conn, "SELECT id FROM subjects WHERE subject_code = '$code'");
    
    if(mysqli_num_rows($check) > 0){
        $alert_msg = "<div class='alert error'>⚠️ Error: Subject Code '$code' already exists!</div>";
    } else {
        // 2. டேட்டாபேஸில் ஏற்கனவே காலம் இருப்பதால், லெக்சரர் பெயருடன் சேர்த்துச் சேமிக்கிறோம்
        $sql = "INSERT INTO subjects (subject_name, subject_code, lecturer) VALUES ('$name', '$code', '$lecturer')";
        
        if(mysqli_query($conn, $sql)){
            header("Location: subject.php"); 
            exit();
        } else {
            $alert_msg = "<div class='alert error'>⚠️ Database Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

/* ==========================================================================
   2. DELETE SUBJECT
   ========================================================================== */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    if(mysqli_query($conn, "DELETE FROM subjects WHERE id=$id")){
        header("Location: subject.php"); 
        exit();
    } else {
        $alert_msg = "<div class='alert error'>⚠️ Delete Error: " . mysqli_error($conn) . "</div>";
    }
}

/* ==========================================================================
   3. GET DATA & COUNT
   ========================================================================== */
$result = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id DESC");

// பாதுகாப்பான முறையில் கவுண்ட் எடுக்கிறோம்
$count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM subjects");
$count = 0;
if($count_query) {
    $count_data = mysqli_fetch_assoc($count_query);
    $count = $count_data['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Subjects</title>
<style>
body{
    font-family: Arial, sans-serif;
    background: linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)),
    url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");
    background-size: cover;
    background-position: center;
    color: white;
    margin: 0;
}
.container { padding: 40px; }
.card {
    background: rgba(255,255,255,0.08);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    backdrop-filter: blur(5px);
}
input {
    width: 100%; padding: 10px; margin: 8px 0;
    border: none; border-radius: 5px; box-sizing: border-box; color: black;
}
.btn {
    padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer;
    background: linear-gradient(45deg,#00d4ff,#7b2ff7); color: white; font-weight: bold;
}
.delete {
    background: #ff4d4d; padding: 6px 12px; border-radius: 5px;
    color: white; text-decoration: none; font-size: 14px; font-weight: bold;
}
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { padding: 12px; text-align: center; }
th { background: rgba(0,212,255,0.3); color: #00d4ff; }
tr:nth-child(even) { background: rgba(255,255,255,0.05); }

.alert {
    padding: 12px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;
}
.error {
    background: rgba(255, 77, 77, 0.2); border: 1px solid #ff4d4d; color: #ff9999;
}
</style>
</head>
<body>

<div class="container">
<a href="admin.php" style="color: #00d4ff; text-decoration: none; font-weight: bold; font-size: 16px;">← Back to Dashboard</a>
<br><br>
<h2>📚 Manage Subjects</h2>

<?php echo $alert_msg; ?>

<div class="card">
<h3>➕ Add Subject</h3>
<form method="POST">
    <input type="text" name="subject_name" placeholder="Subject Name (e.g., Web Development)" required>
    <input type="text" name="subject_code" placeholder="Subject Code (e.g., IT1101)" required>
    <input type="text" name="lecturer" placeholder="Lecturer Name" required>
    <button class="btn" type="submit" name="add_subject">Add Subject</button>
</form>
</div>

<div class="card">
<h3>Subject List</h3>
<table>
<tr>
    <th>Subject</th>
    <th>Code</th>
    <th>Lecturer</th> <th>Action</th>
</tr>

<?php if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
        <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
        <td><?php echo htmlspecialchars($row['lecturer'] ?? 'N/A'); ?></td> <td>
            <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete subject?')">Delete</a>
        </td>
    </tr>
    <?php } 
} else { ?>
    <tr>
        <td colspan="4">No subjects found!</td>
    </tr>
<?php } ?>
</table>
</div>

<div class="card" style="text-align:center; font-size:20px; color:#00d4ff; font-weight:bold;">
    Total Subjects: <?php echo $count; ?>
</div>
</div>

</body>
</html>