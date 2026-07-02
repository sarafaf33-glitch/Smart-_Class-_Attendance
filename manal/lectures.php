<?php
// 1. db.php ஐ இணைக்கிறோம்.
include "db.php"; 

/* ==========================================================================
   1. DELETE LECTURER
   ========================================================================== */
if(isset($_POST['delete'])){
    $id = (int) $_POST['id'];

    // விரிவுரையாளருடன் தொடர்புடைய user_id ஐ எடுக்கிறோம்
    $getUser = mysqli_query($conn, "SELECT user_id FROM lecturers WHERE id=$id");
    $userData = mysqli_fetch_assoc($getUser);
    $associated_user_id = $userData['user_id'] ?? null;

    mysqli_begin_transaction($conn);
    try {
        // 'lecturers' மற்றும் 'users' ஆகிய இரண்டு டேபிள்களிலிருந்தும் நீக்குகிறது
        mysqli_query($conn, "DELETE FROM lecturers WHERE id = $id");
        if ($associated_user_id) {
            mysqli_query($conn, "DELETE FROM users WHERE id = $associated_user_id");
        }
        mysqli_commit($conn);
        header("Location: lectures.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}

/* ==========================================================================
   2. FETCH DATA & COUNT (REGISTERED LECTURERS ONLY)
   ========================================================================== */
/* SQL கோப்பின்படி, users மற்றும் lecturers ஆகிய இரண்டு அட்டவணைகளிலும் 
   முறையாகப் பதிவு செய்யப்பட்டு, role='Lecturer' ஆக இருக்கும் விவரங்கள் மட்டும் எடுக்கப்படுகிறது. */
$query = "SELECT l.id, l.name, l.email, l.phone, l.department 
          FROM lecturers l 
          INNER JOIN users u ON l.user_id = u.id 
          WHERE u.role = 'Lecturer' 
          ORDER BY l.id DESC";

$result = mysqli_query($conn, $query);

// மொத்த பதிவுசெய்த விரிவுரையாளர்களின் எண்ணிக்கையைக் கணக்கிடுதல்
$count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Lecturers</title>

<style>
body{
    font-family:Arial, sans-serif;
    background:linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)),
    url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");
    background-size:cover;
    background-position: center;
    color:white;
    margin: 0;
    min-height: 100vh;
}

.container{
    padding:40px;
}

.card{
    background:rgba(255,255,255,0.08);
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
    backdrop-filter: blur(5px);
}

.delete-btn{
    padding:6px 10px;
    background:#ff4d4d;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-weight: bold;
    transition: 0.3s;
}
.delete-btn:hover { background: #cc3b3b; }

table{
    width:100%;
    border-collapse:collapse;
    margin-top: 15px;
}

th,td{
    padding:12px;
    text-align:center;
}

th{
    background:rgba(0,212,255,0.3);
    color: #00d4ff;
}

tr{ border-bottom: 1px solid rgba(255,255,255,0.05); }
tr:nth-child(even){
    background:rgba(255,255,255,0.02);
}

.msg { 
    padding:12px; 
    border-radius:8px; 
    margin-bottom:15px;
    background:rgba(255, 77, 77, 0.15); 
    color:#ff9999; 
    text-align:center; 
    border: 1px solid rgba(255, 77, 77, 0.3);
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">
<a href="admin.php" style="color: #00d4ff; text-decoration: none; font-weight: bold; font-size: 16px;">← Back to Dashboard</a>
<br><br>
<h2>👨‍🏫 Registered Lecturer List</h2>

<?php
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    echo "<div class='msg'>🗑️ Lecturer Deleted Successfully!</div>";
}
?>

<div class="card">
<h3>Lecturers</h3>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Department</th>
    <th>Action</th>
</tr>

<?php 
if ($count > 0) {
    while($row = mysqli_fetch_assoc($result)) { 
?>
<tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= isset($row['email']) ? htmlspecialchars($row['email']) : 'N/A' ?></td>
    <td><?= isset($row['phone']) ? htmlspecialchars($row['phone']) : 'N/A' ?></td>
    <td><?= htmlspecialchars($row['department']) ?></td>
    <td>    
        <form method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button class="delete-btn" name="delete"
                    onclick="return confirm('Delete this lecturer?')">
                Delete
            </button>
        </form>
    </td>
</tr>
<?php 
    } 
} else {
    echo "<tr><td colspan='6'>No registered lecturers found!</td></tr>";
}
?>

</table>
</div>

<div class="card" style="text-align:center;color:#00d4ff;font-size:20px; font-weight:bold;">
    Total Registered Lecturers: <?= $count ?>
</div>

</div>

</body>
</html>