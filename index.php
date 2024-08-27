<?php
include "config.php";
session_start();


// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch items
$result = $conn->query("SELECT * FROM posts");

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Application</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
<div class="container mt-4">
<div class="d-flex justify-content-between align-items-center">
    <h2 class="mb-8">CRUD Application</h2>
    <button class="btn btn-primary" onclick="window.location.href='logout.php'"><i class="bi bi-box-arrow-right"></i></button>
</div>

        <!-- Create Form -->
        

        <!-- Update Form (Modal Trigger) -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <a href="add_post.php"><i class="bi bi-pencil-square"></i>
                Create Post</a>
            </div>
                <div class="card-body">
                    <thead>
                            <table class="table table-striped">
                            <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Content</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                            <td><?php echo $row['id'] ; ?></td>
                                                        <td><?php echo $row['title'] ; ?></td>
                                                        <td><?php echo $row['content'] ; ?></td>
                                                        <td><?php echo $row['created_at'] ; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm editbtn" 
                                    onclick="editDetails(
                                                                '<?php echo $row['id'] ; ?>',
                                                                '<?php echo $row['title'] ; ?>',
                                                                '<?php echo $row['content'] ; ?>',
                                                                '<?php echo $row['created_at'] ; ?>')">
                                                                <i class="bi bi-pencil-square"></i></button>
                                    <form action="" method="post" >
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    This is a centrally aligned modal box.
                </div>
                <div class="modal-footer mx-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script>
    function editDetails (id, title, content, created_at) {
        $('#editmodal').modal('show');
        $('#id').val(id);
        $('#title').val(title);
        $('#content').val(content);
        $('#created_at').val(created_at);
    }
</script>
</script>
</body>
</html>