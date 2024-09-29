<?php
    include 'config.php'; // Include database connection

    // Check if form is submitted via POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // Prepare the SQL statement to fetch the user's hashed password and role
        $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword, $role);
            $stmt->fetch();

            // Verify the password entered against the hashed password from the database
            if (password_verify($pass, $hashedPassword)) {
                session_start();
                $_SESSION['username'] = $user;
                $_SESSION['role'] = $role; // Save role in the session
                header("Location: index.php"); // Redirect on successful login
                exit;
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