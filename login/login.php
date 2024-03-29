<?php
session_start(); // Start the session to persist user authentication

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness";

// Error message variable
$error_message = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
if(isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    //Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare a SQL statement to check user credentials
    $stmt = $conn->prepare("SELECT email, password FROM users WHERE (email = ? OR password = ?) LIMIT 1");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows == 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, log in the user
            $_SESSION['email'] = $user['email'];
            $_SESSION['password'] = $user['password'];
           
            // Redirect to a dashboard or welcome page
            header("Location: ../homepage/homeafter.html");
            exit();
        } else {
            // Password is incorrect
            $error_message = "Invalid password.";
        }
    } else {
        // User not found
        $error_message = "User not found";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="style1.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
      <script>
    function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    var icon = document.querySelector(".toggle-password");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

    </script>
<style>
    .toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}
.navbar {
      position: fixed;
      top: 0;
      left: 0;
      background-color: transparent; /* Dark background color */
      padding: 10px 20px; /* Padding for the navbar */
      border-bottom-left-radius: 10px; /* Rounded corners */
    }

    /* Style for navbar links */
    .navbar a {
      color: white; /* White text color */
      text-decoration: none;
      margin-left: 20px; /* Spacing between links */
      font-size: 25px;
    }

    /* Change the color of navbar links on hover */
    .navbar a:hover {
      color: lightgray; /* Light gray text color on hover */
    }
    </style>

   </head>
   <body>
   <div class="navbar">
  <a href="../homepage/homepage.html"><img src="../homepage/logo_asthetic_regime_-removebg-preview.png" style="height: 250px;" alt="Logo"></a>
   </div>
      <div class="content">
         <div class="text">
            Login Form
         </div>
         <form action="login.php" method="post">
            <div class="field">
               <input type="text" name="email" id="email" required>
               <span class="fas fa-user"></span>
               <label for="email">Email:</label>
               
            </div>
            <div class="field">
               <input type="password" name="password" id="password" required>
               <span class="fas fa-lock"></span>
               <span class="toggle-password fas fa-eye" onclick="togglePasswordVisibility()"></span>
               <label for="password">Password:</label>
            </div>
            <?php if(isset($error_message)) { ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php } ?>
           
            <button type="submit">Sign in</button>
            <div class="sign-up">
               Not a member?
               <a href="../sign-up/index.php">Register now</a>
            </div>
         </form>
      </div>
   </body>
</html>
