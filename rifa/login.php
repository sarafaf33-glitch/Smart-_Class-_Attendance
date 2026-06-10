<?php
session_start();
include("db.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users
            WHERE email='$email'
            AND password='$password'
            AND role='$role'";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['fullname'] = $row['fullname'];

        if($row['role'] == "Student"){
            header("Location: studentdashboard.php");
            exit();
        }

        if($row['role'] == "Lecturer"){
            header("Location: lecturerdashboard.html");
            exit();
        }

        if($row['role'] == "Admin"){
            header("Location: admindashboard.html");
            exit();
        }

    }else{
        echo "<script>alert('Invalid Email, Password or Role');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>Login Page</title>

    <style>

        /* RESET */
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',
            Tahoma,
            Geneva,
            Verdana,
            sans-serif;
        }

        /* BACKGROUND */
        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;

            background:
            linear-gradient(rgba(0,0,0,0.6),
            rgba(0,0,0,0.6)),
            url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d");

            background-size:cover;
            background-position:center;
        }

        /* LOGIN BOX */
        .login-container{
            width:400px;
            padding:40px;
            border-radius:20px;
            background:rgba(255,255,255,0.12);
            backdrop-filter:blur(15px);
            box-shadow:0 8px 32px rgba(0,0,0,0.37);
            color:white;
            border:1px solid rgba(255,255,255,0.1);
        }

        .login-container h1{
            text-align:center;
            margin-bottom:5px;
            font-size:2.2rem;
        }

        .login-container p.subtitle{
            text-align:center;
            margin-bottom:30px;
            color:#ddd;
            font-size:0.9rem;
        }

        /* INPUT GROUP */
        .input-group{
            margin-bottom:18px;
        }

        .input-group label{
            display:block;
            margin-bottom:8px;
            font-weight:600;
            color:#efefef;
        }

        /* INPUT STYLE */
        .input-field{
            width:100%;
            padding:12px 15px;
            border-radius:10px;
            border:1.5px solid rgba(255,255,255,0.3);
            background:rgba(255,255,255,0.1);
            color:white;
            font-size:16px;
            outline:none;
            transition:0.3s;
        }

        .input-field:focus{
            border-color:#2575fc;
            background:rgba(255,255,255,0.2);
        }

        .input-field option{
            background-color:#333;
            color:white;
        }

        ::placeholder{
            color:#ccc;
        }

        /* BUTTON */
        .login-btn{
            width:100%;
            padding:15px;
            border:none;
            border-radius:10px;

            background:
            linear-gradient(45deg,
            #6a11cb,#2575fc);

            color:white;
            font-size:18px;
            font-weight:bold;
            cursor:pointer;
            transition:0.3s;
            margin-top:10px;
        }

        .login-btn:hover{
            transform:translateY(-2px);

            box-shadow:
            0 5px 15px
            rgba(37,117,252,0.4);
        }

        /* EXTRA */
        .extra{
            text-align:center;
            margin-top:20px;
            font-size:0.9rem;
        }

        .extra a{
            text-decoration:none;
            color:#00d2ff;
            font-weight:bold;
        }

    </style>
</head>

<body>

    <div class="login-container">

        <h1>Smart AMS</h1>

        <p class="subtitle">
            Login to Continue
        </p>

        <form method="POST">

            <!-- EMAIL -->
            <div class="input-group">

                <label>Email</label>

                <input type="email"
                       name="email"
                       class="input-field"
                       placeholder="Enter your email"
                       required>

            </div>

            <!-- PASSWORD -->
            <div class="input-group">

                <label>Password</label>

                <input type="password"
                       name="password"
                       class="input-field"
                       placeholder="Enter your password"
                       required>

            </div>

            <!-- ROLE -->
            <div class="input-group">

                <label>Select Role</label>

                <select name="role"
                        class="input-field"
                        required>

                    <option value="">
                        Choose Role
                    </option>

                    <option value="Student">
                        Student
                    </option>

                    <option value="Lecturer">
                        Lecturer
                    </option>

                    <option value="Admin">
                        Admin
                    </option>

                </select>

            </div>

            <button type="submit"
                    class="login-btn">

                Login

            </button>

        </form>

        <!-- EXTRA -->
        <div class="extra">

            <p>
                Forgot Password?
                <a href="reset.html">
                    Reset
                </a>
            </p>

        </div>

    </div>

</body>
</html>
