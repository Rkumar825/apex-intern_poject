<?php
include "config.php"; // Database configuration
session_start();

// Check if the user is logged in, and redirect to login page if not


// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create']) && $_SESSION['role'] == 'admin') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'editor')) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete']) && $_SESSION['role'] == 'admin') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Pagination settings
$limit = 3; // Number of posts per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_term = "%" . $search_query . "%";
$sql = "SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $search_term, $search_term, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Get total number of posts for pagination
$sql_total = "SELECT COUNT(*) as total FROM posts WHERE title LIKE ? OR content LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("ss", $search_term, $search_term);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_posts = $total_result->fetch_assoc()['total'];
$stmt_total->close();
$total_pages = ceil($total_posts / $limit);

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
        <h2 class="mb-4">CRUD Blog Application</h2>
        <button class="btn btn-primary" onclick="window.location.href='logout.php'"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>
    <?php
    $role = $_SESSION['role'];
    $username = $_SESSION['username'];
    
    echo "<div class='welcome-message'>";
    if ($role == 'admin') {
        echo "<h4>Welcome, Admin $username!</h4>";
    } elseif ($role == 'editor') {
        echo "<h4>Welcome, Editor $username!</h4>";
    }
    echo "</div>";
    ?>

    <!-- Search Form -->
    <div class="d-flex justify-content-end">
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <button class="btn btn-primary mb-3 ml-2" onclick="window.location.href='add_post.php'"><i class="bi bi-file-earmark-plus"></i> Create Post</button>
        <?php endif; ?>
        <form method="GET" action="">
            <div class="input-group mb-3 ml-4" style="max-width: 300px;">
                <input type="text" class="form-control" placeholder="Search Posts" name="search" value="<?php echo $_GET['search'] ?? ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
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
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['content']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'editor'): ?>
                                    <button class="btn btn-primary btn-sm" onclick="editDetails('<?php echo $row['id']; ?>', '<?php echo $row['title']; ?>', '<?php echo $row['content']; ?>')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <form action="" method="post" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination Links -->
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?search=<?php echo $search_query; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="modal_post_id">
                        <div class="form-group">
                            <label for="modal_title">Title</label>
                            <input type="text" class="form-control" id="modal_title" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_content">Content</label>
                            <textarea class="form-control" id="modal_content" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function editDetails(id, title, content) {
        $('#editModal').modal('show');
        $('#modal_post_id').val(id);
        $('#modal_title').val(title);
        $('#modal_content').val(content);
    }
    </script>
</div>
</body>
</html>