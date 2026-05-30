<?php include("includes/header.php"); ?>
<?php include("config/db.php"); ?>

<h2 class="mb-3"><i class="bi bi-grid"></i> Products</h2>

<!-- SEARCH & FILTER -->
<div class="card p-3 mb-4">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control bg-dark text-white border-secondary"
                placeholder="Search by name..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select bg-dark text-white border-secondary">
                <option value="">All Categories</option>
                <?php
                $cats = mysqli_query($conn, "SELECT * FROM dbproj_categories");
                while ($c = mysqli_fetch_assoc($cats)) {
                    $sel = (isset($_GET['category']) && $_GET['category'] == $c['category_id']) ? 'selected' : '';
                    echo "<option value='{$c['category_id']}' $sel>{$c['category_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select bg-dark text-white border-secondary">
                <option value="newest" <?php echo (($_GET['sort'] ?? '') == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                <option value="price_asc" <?php echo (($_GET['sort'] ?? '') == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo (($_GET['sort'] ?? '') == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="popular" <?php echo (($_GET['sort'] ?? '') == 'popular') ? 'selected' : ''; ?>>Most Popular</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>
</div>

<?php
// PAGINATION SETUP
$per_page = 9;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// BUILD QUERY (safe with prepared statements)
$where = "WHERE 1=1";
$params = [];
$types  = "";

if (!empty($_GET['q'])) {
    $where .= " AND MATCH(p.name, p.description) AGAINST(? IN BOOLEAN MODE)";
    $params[] = $_GET['q'] . '*';
    $types .= "s";
}

if (!empty($_GET['category'])) {
    $where .= " AND p.category_id = ?";
    $params[] = (int)$_GET['category'];
    $types .= "i";
}

$order = "ORDER BY p.created_at DESC";
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':  $order = "ORDER BY p.price ASC"; break;
        case 'price_desc': $order = "ORDER BY p.price DESC"; break;
        case 'popular':    $order = "ORDER BY p.views DESC"; break;
    }
}

// COUNT TOTAL
$count_sql = "SELECT COUNT(*) AS total FROM dbproj_products p $where";
$stmt = mysqli_prepare($conn, $count_sql);
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$total_rows = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
$total_pages = ceil($total_rows / $per_page);

// FETCH PRODUCTS
$sql = "SELECT p.*, c.category_name,
        (SELECT AVG(rating) FROM dbproj_ratings WHERE product_id = p.product_id) AS avg_rating
        FROM dbproj_products p
        LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
        $where $order
        LIMIT ? OFFSET ?";

$all_params = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types = $types . "ii";

$stmt2 = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt2, $all_types, ...$all_params);
mysqli_stmt_execute($stmt2);
$result = mysqli_stmt_get_result($stmt2);
?>

<div class="row">
<?php while ($row = mysqli_fetch_assoc($result)) {
    $stars = round($row['avg_rating'] ?? 0);
?>
<div class="col-md-4 mb-4">
    <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['image'] ?? 'https://placehold.co/300x200?text=No+Image'); ?>"
             class="product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <div class="p-3">
            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h5><?php echo htmlspecialchars($row['name']); ?></h5>
            <p class="text-muted small"><?php echo htmlspecialchars(substr($row['description'], 0, 80)) . '...'; ?></p>
            <div class="mb-1">
                <?php for ($i = 1; $i <= 5; $i++) echo $i <= $stars ? '⭐' : '☆'; ?>
                <small class="text-muted">(<?php echo number_format($row['avg_rating'] ?? 0, 1); ?>)</small>
            </div>
            <h5 style="color:var(--bp-accent);">$<?php echo number_format($row['price'], 2); ?></h5>
            <p class="small text-muted">Stock: <?php echo $row['stock']; ?></p>
            <a href="products_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-primary w-100">
                View More
            </a>
        </div>
    </div>
</div>
<?php } ?>
</div>

<!-- PAGINATION -->
<?php if ($total_pages > 1) { ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($p = 1; $p <= $total_pages; $p++) {
            $q = http_build_query(array_merge($_GET, ['page' => $p]));
        ?>
        <li class="page-item <?php echo $p == $page ? 'active' : ''; ?>">
            <a class="page-link" href="?<?php echo $q; ?>"><?php echo $p; ?></a>
        </li>
        <?php } ?>
    </ul>
</nav>
<?php } ?>

<?php include("includes/footer.php"); ?>