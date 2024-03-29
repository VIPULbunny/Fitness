<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness";

// Password complexity requirements
$uppercase_required = false;
$lowercase_required = false;
$number_required = false;
$symbol_required = false;
$min_length = 8;

// Regular expressions for checking password complexity
$uppercase_regex = "/[A-Z]/";
$lowercase_regex = "/[a-z]/";
$number_regex = "/[0-9]/";
$symbol_regex = "/[\W_]/"; // Matches any non-word character (excluding underscore)

// Error message variables
$error_message = "";
$error_user = "";
$error_email = "";
$error_phone = "";
$success_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        // Check if all form fields are set
        if (isset($_POST['username'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];

            // Check if username already exists
            $check_username_stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
            $check_username_stmt->bind_param("s", $username);
            $check_username_stmt->execute();
            $check_result = $check_username_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error_user = "Username already exists.";
            } else {
                // Check if email already exists
                $check_email_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
                $check_email_stmt->bind_param("s", $email);
                $check_email_stmt->execute();
                $check_email_result = $check_email_stmt->get_result();

                if ($check_email_result->num_rows > 0) {
                    $error_email = "Email already exists.";
                } else {
                    // Check email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error_email = "Invalid email format.";
                    } else {
                        // Check phone number format
                        if (!preg_match("/^\d{10}$/", $phone) || !preg_match("/^[0-9]+$/", $phone)) {
                            $error_phone = "Invalid phone number format. It must be 10 digits and contain only numbers.";
                        } else {
                            // Check password complexity
                            if (strlen($password) < $min_length ||
                                !preg_match($uppercase_regex, $password) ||
                                !preg_match($lowercase_regex, $password) ||
                                !preg_match($number_regex, $password) ||
                                !preg_match($symbol_regex, $password)) {
                                $error_message = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one symbol.";
                            } else {
                                // Hash the password
                                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                                // Prepare and bind parameters for SQL insertion
                                $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
                                $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);
                                $execval = $stmt->execute();

                                if ($execval) {
                                    $success_message = "Registration successful...";

                                    // Retrieve the user ID of the newly inserted user
                                    $user_id = $stmt->insert_id;

                                    // Insert login details into login table
                                    $insert_stmt = $conn->prepare("INSERT INTO login (user_id) VALUES (?)");
                                    $insert_stmt->bind_param("i", $user_id);
                                    $insert_stmt->execute();
                                } else {
                                    echo "Error: " . $conn->error;
                                }

                                // Close statement
                                $stmt->close();
                            }
                        }
                    }
                }
                // Close email check statement
                $check_email_stmt->close();
            }
            // Close username check statement
            $check_username_stmt->close();
        } else {
            echo "Fill All The Fields";
        }
        // Close connection
        $conn->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="style2.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
      <!-- JavaScript for togglePasswordVisibility function -->
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

    /* Style for message boxes */
    .message {
            padding: 10px;
            border-radius: 5px;
            position: fixed; /* Fixed positioning */
            bottom: 60px; /* Adjust top position as needed */
            left: 50%; /* Center horizontally */
            height: 100px;
            width: 400px;
            transform: translateX(-50%); /* Center horizontally */
            z-index: 9999; /* Ensure it's above other content */
        }
        .message1 {
            padding: 10px;
            border-radius: 5px;
            position: fixed; /* Fixed positioning */
            bottom: 110px; /* Adjust top position as needed */
            left: 50%; /* Center horizontally */
            height: 50px;
            width: 400px;
            transform: translateX(-50%); /* Center horizontally */
            z-index: 9999; /* Ensure it's above other content */
        }
        .message2 {
            padding: 10px;
            border-radius: 5px;
            position: fixed; /* Fixed positioning */
            bottom: 110px; /* Adjust top position as needed */
            left: 50%; /* Center horizontally */
            height: 50px;
            width: 400px;
            transform: translateX(-50%); /* Center horizontally */
            z-index: 9999; /* Ensure it's above other content */
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .error-user {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .error-email {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .close-button {
    position: absolute;
    top: -10px;
    right: 2px;
    cursor: pointer;
    font-size: 30px;
    color: #666;
}

.close-button:hover {
    color: #000;
}


    </style>

<script>
        // JavaScript function to hide error message box
        function hideErrorMessage() {
            document.getElementById('error-message').style.display = 'none';
        }
         // JavaScript function to hide message box
         function hideMessage(messageId) {
            document.getElementById(messageId).style.display = 'none';
        }
    </script>

   </head>
   <body>
    <!-- Display success message if exists -->
    <?php if (!empty($success_message)) { ?>
            <div id="success-message" class="message1 success-message">
                <?php echo $success_message; ?>
                <span class="close-button" onclick="hideMessage('error-message')">&times;</span>
            </div>
        <?php } ?>

  <!-- Display error message if exists -->
        <?php if (!empty($error_message)) { ?>
            <div id="error-message" class="message error-message">
                <?php echo $error_message; ?>
                <span class="close-button" onclick="hideMessage('error-message')">&times;</span>
            </div>
        <?php } ?>

        <?php if (!empty($error_user)) { ?>
            <div id="error-user" class="message1 error-user">
                <?php echo $error_user; ?>
                <span class="close-button" onclick="hideMessage('error-user')">&times;</span>
            </div>
        <?php } ?>

        <?php if (!empty($error_email)) { ?>
            <div id="error-email" class="message2 error-email">
                <?php echo $error_email; ?>
                <span class="close-button" onclick="hideMessage('error-email')">&times;</span>
            </div>
        <?php } ?>


   <div class="navbar">
  <a href="../homepage/homepage.html"><img src="../homepage/logo_asthetic_regime_-removebg-preview.png" style="height: 250px;" alt="Logo"></a>
   </div>
      <div class="content">
         <div class="text">
            Registration Form
         </div>
         <form action="index.php" method="post">
            <div class="field">
               <input type="text" id="username" name="username" required>
               <span class="fas fa-user"></span>
               <label>Username</label>
            </div>
            <div class="field">
                <input type="text" id="email" name="email" required>
                <span class="fas fa-pen"></span>
                <label>Email</label>
             </div>
             <div class="field">
               <input type="text" id="phone" name="phone" required>
               <span class="fas fa-phone"></span>
               <label>Phone</label>
            </div>
            <div class="field">
                <input type="password" id="password" name="password" required>
                <span class="fas fa-lock"></span>
                <span class="toggle-password fas fa-eye" onclick="togglePasswordVisibility()"></span>
                <label>Password</label>
             </div>
            <button type="submit">Register</button>
            <div class="sign-up">
               Already a member?
               <a href="../login/login.php">Login now</a>
            </div>
         </form>
      </div>
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <!-- Form fields -->
    </form>
   </body>
</html>
