<?php 
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the role from the form

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user with their role
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
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
        .form-control-custom {
            height: calc(1.5em + .75rem + 2px); /* Adjusts the height of input fields */
        }
    </style>
</head>
<body>
    <div class="bg-custom-gradient d-flex justify-content-center align-items-center vh-100">
        <div class="card-custom rounded-4 bg-white card text-center mb-3" style="width: 320px;">
            <div class="card-body">
                <h5 class="card-title">Register</h5>
                <hr style="border: 1px solid #2575fc;">
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
                        <div class="mb-3 mx-4">
                            <label for="role" class="form-label">Role:</label>
                            <select id="role" name="role" class="form-control form-control-custom">
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="editor">Editor</option>

                            </select>
                        </div>
                        <div class="mx-5 mt-4 text-center">
                            <button class="btn btn-custom w-100 text-white" id="register" name="register" type="submit">Register</button>
                        </div>
                        <div class="mx-2 mt-4 text-center text-dark">
                            <p>Already have an account? <a class="text-custom" href="login.php">Login Here</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>