<?php
// MySQL Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "smart_ams";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST['fullname'];
    $regnumber = $_POST['regnumber'];
    $email = $_POST['email'];
    $userpassword = $_POST['password'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users(fullname, regnumber, email, password, role)
            VALUES('$fullname', '$regnumber', '$email', '$userpassword', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Account Created Successfully!');
                window.location.href='login.php';
              </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;

            background:
            linear-gradient(rgba(0,0,0,0.6),
            rgba(0,0,0,0.6)),
            url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");

            background-size: cover;
            background-position: center;
        }

        .signup-container {
            width: 420px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            color: white;
        }

        .signup-container h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .signup-container p {
            text-align: center;
            margin-bottom: 30px;
            color: #ddd;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.15);
            color: white;
            font-size: 16px;
            outline: none;
        }

        .input-group input::placeholder {
            color: #ddd;
        }

        select option {
            color: black;
        }

        .signup-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(45deg,#6a11cb,#2575fc);
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .signup-btn:hover {
            transform: scale(1.05);
        }

        .extra {
            text-align: center;
            margin-top: 20px;
        }

        .extra a {
            text-decoration: none;
            color: #00d4ff;
            font-weight: bold;
        }

    </style>
</head>

<body>

    <div class="signup-container">

        <h1>Create Account</h1>
        <p>Register to Smart AMS</p>

        <form method="POST">

            <div class="input-group">
                <label>Full Name</label>
                <input type="text"
                       name="fullname"
                       placeholder="Enter your full name"
                       required>
            </div>

            <div class="input-group">
                <label>Register Number</label>
                <input type="text"
                       name="regnumber"
                       placeholder="Enter register number"
                       required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       placeholder="Enter your email"
                       required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password"
                       name="password"
                       placeholder="Enter password"
                       required>
            </div>

            <div class="input-group">
                <label>Select Role</label>

                <select name="role" required>
                    <option value="">Choose Role</option>
                    <option>Student</option>
                    <option>Lecturer</option>
                    <option>Admin</option>
                </select>
            </div>

            <button type="submit" class="signup-btn">
                Sign Up
            </button>

        </form>

        <div class="extra">
            <p>
                Already have an account?
                <a href="login.php">Login</a>
            </p>
        </div>

    </div>

</body>
</html>