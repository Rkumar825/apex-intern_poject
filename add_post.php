<?php

include "config.php";   // config file

date_default_timezone_set('Asia/Kolkata');

$title = $content = $created_at = "";

$created_at = date("Y-m-d H:i:s");

if (isset($_POST['add'])) {
        
    $title = trim($_POST["title"]);

    $content = trim($_POST["content"]);

            // Check input errors before inserting in database
    

        $sql = "INSERT INTO `posts` (title, content, created_at) 
        VALUES ('$title','$content','$created_at') ";

        $ress= mysqli_query($conn, $sql);

            // Attempt to execute the prepared statement
            if ($ress) {
                // Redirect to dashboard page
                header("location: index.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        
    
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>
    <div class=" d-flex justify-content-center " style="margin-top: 35px;">
        <div class="card-custom rounded-4 bg-white card text-center mb-3" style="">
            <div class="card-body">
                <h5 class="card-title">Create Post</h5>
                <hr style="border: 2px solid #2575fc;">
                <form method="POST">
                    <div class="text-start">
                        <div class="mb-3 mx-2 d-flex align-items-center">
                            <label for="title" class="form-label me-2">Title:</label>
                            <input type="text" class="form-control form-control-custom" id="title" name="title" value="<?php echo $title;?>" placeholder="Enter Your Title" required>
                        </div>

                        <div class="mb-3 mx-2">
                            <label for="content" class="form-label">Content:</label>
                            <textarea class="form-control form-control-custom" id="content" name="content" value="<?php echo $content;?>" placeholder="Enter Your Content" style="height: 200px" required></textarea>
                        </div>
                        
                        <div class="mb-3 mx-2">
                            <label for="created_at" class="form-label">Created At:</label>
                            <input type="datetime-local" class="form-control form-control-sm" id="created_at"  name="created_at" value="<?php echo $created_at;?>" readonly disabled>
                        </div>
                        
                        <div class="mx-4 mt-4 text-center">
                            <button class="btn btn-primary col-sm-10 text-light waves-effect waves-light" id="add" name="add" type="submit">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>