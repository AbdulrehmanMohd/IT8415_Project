<?php
session_start();
include("config/db.php");

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = (int)$_GET['id'];

// Increment views
mysqli_query($conn, "UPDATE dbproj_products SET views = views + 1 WHERE product_id = $id");

// Fetch product
$stmt = mysqli_prepare($conn, "SELECT p.*, c.category_name, u.username AS seller_name
    FROM dbproj_products p
    LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
    LEFT JOIN dbproj_users u ON p.seller_id = u.user_id
    WHERE p.product_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    echo "Product not found.";
    exit();
}

// Handle comment submission
if (isset($_POST['add_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $comment_error = "You must be logged in to comment.";
    } else {
        $comment = trim($_POST['comment']);
        if (strlen($comment) < 2) {
            $comment_error = "Comment is too short.";
        } else {
            $uid = $_SESSION['user_id'];
            $stmt2 = mysqli_prepare($conn, "INSERT INTO dbproj_comments (product_id, user_id, comment) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "iis", $id, $uid, $comment);
            mysqli_stmt_execute($stmt2);
            header("Location: products_details.php?id=$id");
            exit();
        }
    }
}

// Handle rating
if (isset($_POST['rate'])) {
    if (isset($_SESSION['user_id'])) {
        $rating = (int)$_POST['rating'];
        $uid = $_SESSION['user_id'];
        if ($rating >= 1 && $rating <= 5) {
            $stmt3 = mysqli_prepare($conn, "INSERT INTO dbproj_ratings (product_id, user_id, rating)
                VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?");
            mysqli_stmt_bind_param($stmt3, "iiii", $id, $uid, $rating, $rating);
            mysqli_stmt_execute($stmt3);
        }
        header("Location: products_details.php?id=$id");
        exit();
    }
}

// Fetch comments
$comments = mysqli_query($conn, "SELECT c.*, u.username FROM dbproj_comments c
    JOIN dbproj_users u ON c.user_id = u.user_id
    WHERE c.product_id = $id ORDER BY c.created_at DESC");

// Fetch avg rating
$avg_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) AS avg FROM dbproj_ratings WHERE product_id = $id"));
$avg_rating = round($avg_row['avg'] ?? 0, 1);
$stars = round($avg_rating);

include("includes/header.php");
?>

<div class="row">
    <div class="col-md-5">
        <img src="<?php echo htmlspecialchars($product['image'] ?? 'https://placehold.co/500x400?text=No+Image'); ?>"
             class="img-fluid rounded" style="max-height:400px; object-fit:cover; width:100%;" alt="">
    </div>

    <div class="col-md-7">
        <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

        <div class="mb-2">
            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $stars ? '⭐' : '☆'; ?>
            <span class="text-muted">(<?php echo $avg_rating; ?> / 5)</span>
        </div>

        <h3 style="color:#e94560;">$<?php echo number_format($product['price'], 2); ?></h3>
        <p>Stock: <?php echo $product['stock']; ?> | Seller: <?php echo htmlspecialchars($product['seller_name'] ?? 'N/A'); ?></p>
        <p class="text-muted small">Views: <?php echo $product['views']; ?></p>

        <?php if ($product['stock'] > 0) { ?>
            <a href="add_to_cart.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-cart-plus"></i> Add to Cart
            </a>
        <?php } else { ?>
            <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
        <?php } ?>
    </div>
</div>

<hr class="my-4">

<!-- RATING FORM -->
<?php if (isset($_SESSION['user_id'])) { ?>
<div class="card p-3 mb-4">
    <h5>Rate this Product</h5>
    <form method="POST" class="d-flex align-items-center gap-3">
        <select name="rating" class="form-select bg-dark text-white border-secondary w-auto">
            <?php for ($r = 5; $r >= 1; $r--) { ?>
                <option value="<?php echo $r; ?>"><?php echo $r; ?> Star<?php echo $r > 1 ? 's' : ''; ?></option>
            <?php } ?>
        </select>
        <button type="submit" name="rate" class="btn btn-primary">Submit Rating</button>
    </form>
</div>
<?php } ?>

<!-- COMMENTS SECTION -->
<h4>Comments (<?php echo mysqli_num_rows($comments); ?>)</h4>

<?php if (isset($comment_error)) { ?>
    <div class="alert alert-danger"><?php echo $comment_error; ?></div>
<?php } ?>

<?php if (isset($_SESSION['user_id'])) { ?>
<form method="POST" class="mb-4">
    <div class="mb-2">
        <textarea name="comment" class="form-control bg-dark text-white border-secondary"
            rows="3" placeholder="Write a comment..." required></textarea>
    </div>
    <button type="submit" name="add_comment" class="btn btn-primary">Post Comment</button>
</form>
<?php } else { ?>
    <p class="text-muted"><a href="login.php">Login</a> to leave a comment.</p>
<?php } ?>

<div id="comments-list">
<?php while ($c = mysqli_fetch_assoc($comments)) { ?>
    <div class="card p-3 mb-2">
        <strong><?php echo htmlspecialchars($c['username']); ?></strong>
        <span class="text-muted small ms-2"><?php echo $c['created_at']; ?></span>
        <p class="mb-0 mt-1"><?php echo htmlspecialchars($c['comment']); ?></p>
    </div>
<?php } ?>
</div>

<?php include("includes/footer.php"); ?>