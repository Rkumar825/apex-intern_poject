<?php
include "config.php"; // Database configuration
session_start();

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
    <title>CRUD Blog Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .card {
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-15px);
        }
        .modal-content {
            border-radius: 15px;
        }
        .btn-primary {
            background-color: #4e54c8;
            border-color: #4e54c8;
        }
        .btn-primary:hover {
            background-color: #3f44a1;
            border-color: #3f44a1;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">CRUD Blog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-center" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                </ul>
                <div class="d-flex text-center">
                    <button class="btn btn-outline-primary me-2" onclick="window.location.href='logout.php'">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-primary text-white text-center py-5">
        <div class="container">
            <h1 class="display-3">CRUD Blog Application</h1>
            <p class="lead">Manage your posts efficiently with our CRUD application</p>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <?php $role = $_SESSION['role']; ?>
                    <div class="welcome-message">
                        <h4>Welcome, <?php echo ucfirst($role) . " " . $_SESSION['username']; ?>!</h4>
                    </div>
                    <?php if ($role == 'admin'): ?>
                        <button class="btn btn-primary" onclick="window.location.href='add_post.php'"><i class="bi bi-file-earmark-plus"></i> Create Post</button>
                    <?php endif; ?>
                </div>

                <!-- Search Form -->
                <div class="d-flex justify-content-end mb-3">
                    <form method="GET" action="">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" placeholder="Search Posts" name="search" value="<?php echo $_GET['search'] ?? ''; ?>">
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts Cards -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                            <p class="card-text"><?php echo $row['content']; ?></p>
                            <p class="text-muted">Created At: <?php echo $row['created_at']; ?></p>
                            <div class="d-flex justify-content-between">
                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'editor'): ?>
                                    <button class="btn btn-primary btn-sm" onclick="editDetails('<?php echo $row['id']; ?>', '<?php echo $row['title']; ?>', '<?php echo $row['content']; ?>')">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                <?php endif; ?>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <form action="" method="post" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i> Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

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
    </main>

    <!-- Modal Structure -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="modal_post_id">
                        <div class="mb-3">
                            <label for="modal_post_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="modal_post_title" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_post_content" class="form-label">Content</label>
                            <textarea class="form-control" id="modal_post_content" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="d-flex justify-content-between bg-light text-center py-3 mt-3">
        <div class="text-start ms-4">
            <p>&copy; 2024 CRUD Blog Application. All rights reserved.</p>
        </div>
        <div class="text-end me-4">
            <p>Design & Developed By <a href="google.com">Rishav Kumar</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editDetails(id, title, content) {
            document.getElementById('modal_post_id').value = id;
            document.getElementById('modal_post_title').value = title;
            document.getElementById('modal_post_content').value = content;
            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            myModal.show();
        }
    </script>
</body>
</html>
