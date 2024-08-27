<?php 
    //include config file
    include 'config.php';

 
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Retrieve the hashed password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($pass, $hashedPassword)) {
            header("Location: index.php");
            // Start session and store user information
            session_start();
            $_SESSION['username'] = $user;
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .card-custom {
            border: 2px solid rgba(0, 0, 0, 0.1);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
        }
        .bg-custom-gradient {
            background: linear-gradient(135deg, #f5f5f5 40%, #e0e0e0 60%);
        }
        .btn-custom {
            background-color: #2575fc;
            border: none;
        }
        .btn-custom:hover {
            background-color: #1d63d9;
        }
        .text-custom {
            color: #2575fc;
        }
    </style>
</head>
<body>
    <div class="bg-custom-gradient d-flex justify-content-center align-items-center vh-100">
        <div class="card-custom rounded-4 bg-white card text-center mb-3" style="">
            <div class="card-body">
                <h5 class="card-title">Login</h5>
                <hr style="border: 2px solid #2575fc;">
                <form method="POST">
                    <div class="text-start">
                        <div class="mb-3 mx-4">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control form-control-custom" id="username" name="username" placeholder="Enter Your Username" required>

                        </div>
                        <div class="mb-3 mx-4">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control form-control-custom" id="password" name="password" placeholder="Enter Your Password" required>
                        </div>
                        
                    <div class="mx-4 mt-4 text-center">
                        <button class="btn btn-custom col-sm-10 text-white waves-effect waves-light" id="update" name="update" type="submit">Log In</button>
                    </div>
                    <div class="mx-2 mt-4 text-center text-dark">
                        <p>Don't Have An Account? <a class="text-custom" href="register.php">Register Here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
