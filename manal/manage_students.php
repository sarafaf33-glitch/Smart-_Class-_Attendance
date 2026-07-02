<?php 
include 'db.php'; // உங்கள் அசல் டேட்டாபேஸ் இணைப்பு

// ── DELETE STUDENT ───────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // மாணவருடன் தொடர்புடைய user_id ஐ எடுக்கிறோம்
    $getUser = mysqli_query($conn, "SELECT user_id FROM students WHERE id=$id");
    $userData = mysqli_fetch_assoc($getUser);
    $associated_user_id = $userData['user_id'] ?? null;

    mysqli_begin_transaction($conn);
    try {
        // 'students' மற்றும் 'users' ஆகிய இரண்டு டேபிள்களிலிருந்தும் நீக்குகிறது
        mysqli_query($conn, "DELETE FROM students WHERE id=$id");
        if ($associated_user_id) {
            mysqli_query($conn, "DELETE FROM users WHERE id=$associated_user_id");
        }
        mysqli_commit($conn);
        header("Location: manage_students.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Manage Students</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }

/* பின்னணி எளிமையான டார்க் கிரே நிறம் */
body {
    background: #1e1e24; 
    color: white;
}

/* நேவ்பார் ஸ்டைல் */
.navbar { 
    width:100%; 
    padding:20px 50px; 
    display:flex; 
    justify-content:space-between;
    align-items:center; 
    background: rgba(255, 255, 255, 0.05); 
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo { font-size:25px; font-weight:bold; color:#00d4ff; }

.back-btn { 
    padding:10px 18px; 
    border:none; 
    border-radius:8px;
    background:#ff4d4d; 
    color:white; 
    cursor:pointer; 
    font-weight: bold;
    transition: 0.3s;
}
.back-btn:hover { background: #cc3b3b; }

.container { padding:50px; max-width: 1200px; margin: 0 auto; }

/* அட்டவணை கார்டின் பின்னணி */
.card { 
    background: rgba(255, 255, 255, 0.06); 
    padding:30px; 
    border-radius:15px; 
    margin-bottom:30px; 
    border: 1px solid rgba(255,255,255,0.08);
}
.card h2 { color:#00d4ff; margin-bottom:20px; }

table { width:100%; border-collapse:collapse; margin-top:20px; }
th, td { padding:15px; text-align:center; }
th { background:rgba(0,212,255,0.2); color: #00d4ff; font-weight: bold; }
tr { border-bottom: 1px solid rgba(255,255,255,0.05); }
tr:nth-child(even) { background:rgba(255,255,255,0.02); }

.delete { 
    background:#ff4d4d; 
    border:none; 
    padding:8px 14px;
    border-radius:6px; 
    color:white; 
    cursor:pointer; 
    font-weight: bold;
    transition: 0.3s;
}
.delete:hover { background: #cc3b3b; }

.total-box { 
    margin-top:20px; 
    background:rgba(0,212,255,0.1);
    padding:15px; 
    border-radius:10px; 
    text-align:center;
    font-size:20px; 
    font-weight:bold; 
    color:#00d4ff; 
    border: 1px solid rgba(0,212,255,0.2);
}
.msg { 
    padding:12px; 
    border-radius:8px; 
    margin-bottom:15px;
    background:rgba(0,180,216,0.15); 
    color:#00d4ff; 
    text-align:center; 
    border: 1px solid rgba(0,180,216,0.3);
}
</style>
</head>
<body>

<div class="navbar">
    <div class="logo">Manage Students</div>
    <button class="back-btn" onclick="window.location.href='admin.html.html'">Back</button>
</div>

<div class="container">
    <?php
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == 'deleted') echo "<div class='msg'>🗑️ Student Deleted Successfully!</div>";
    }
    ?>

    <div class="card">
        <h2>👨‍🎓 Registered Student List</h2>
        <table>
            <tr>
                <th>Name</th><th>Reg No</th><th>Course</th><th>Action</th>
            </tr>
            <?php
            /* SQL கோப்பின்படி, users மற்றும் students ஆகிய இரண்டு அட்டவணைகளிலும் 
               முறையாகப் பதிவு செய்யப்பட்ட மாணவர்களை மட்டும் எடுக்க INNER JOIN மற்றும் role='Student' பயன்படுத்தப்பட்டுள்ளது. */
            $query = "SELECT s.id, s.name, s.reg_no, s.course 
                      FROM students s 
                      INNER JOIN users u ON s.user_id = u.id 
                      WHERE u.role = 'Student' 
                      ORDER BY s.id DESC";
                      
            $result = mysqli_query($conn, $query);
            $total  = mysqli_num_rows($result);
            
            if ($total > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['reg_no']) . "</td>
                        <td>" . htmlspecialchars($row['course']) . "</td>
                        <td>
                            <a href='?delete={$row['id']}'
                               onclick=\"return confirm('Delete this student?')\">
                                <button class='delete'>Delete</button>
                            </a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No registered students found!</td></tr>";
            }
            ?>
        </table>

        <div class="total-box">
            Total Registered Students : <span><?php echo $total; ?></span>
        </div>
    </div>

</div>
</body>
</html>