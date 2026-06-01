<?php
include("includes/header.php");
include("config/db.php");

$q         = trim($_GET['q'] ?? '');
$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to']   ?? '';
$seller    = trim($_GET['seller'] ?? '');
$sort      = $_GET['sort'] ?? 'newest';

$where  = "WHERE 1=1";
$params = [];
$types  = "";

if (!empty($q)) {
    $where   .= " AND MATCH(p.name, p.description) AGAINST(? IN BOOLEAN MODE)";
    $params[] = $q . '*';
    $types   .= "s";
}
if (!empty($date_from)) {
    $where   .= " AND DATE(p.created_at) >= ?";
    $params[] = $date_from;
    $types   .= "s";
}
if (!empty($date_to)) {
    $where   .= " AND DATE(p.created_at) <= ?";
    $params[] = $date_to;
    $types   .= "s";
}
if (!empty($seller)) {
    $where   .= " AND u.username LIKE ?";
    $params[] = '%' . $seller . '%';
    $types   .= "s";
}

$order = "ORDER BY p.created_at DESC";
if ($sort == 'price_asc')  $order = "ORDER BY p.price ASC";
if ($sort == 'price_desc') $order = "ORDER BY p.price DESC";
if ($sort == 'popular')    $order = "ORDER BY p.views DESC";

$sql = "SELECT p.*, c.category_name, u.username AS seller_name,
        (SELECT AVG(rating) FROM dbproj_ratings WHERE product_id = p.product_id) AS avg_rating
        FROM dbproj_products p
        LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
        LEFT JOIN dbproj_users u ON p.seller_id = u.user_id
        $where $order";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<h4>Search Results <?php if ($q) echo "for: <em>\"" . htmlspecialchars($q) . "\"</em>"; ?></h4>
<hr>

<div class="card p-3 mb-4">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control"
                placeholder="Search keyword..."
                value="<?php echo htmlspecialchars($q); ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">From date</label>
            <input type="date" name="date_from" class="form-control"
                value="<?php echo htmlspecialchars($date_from); ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">To date</label>
            <input type="date" name="date_to" class="form-control"
                value="<?php echo htmlspecialchars($date_to); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="seller" class="form-control"
                placeholder="Seller username..."
                value="<?php echo htmlspecialchars($seller); ?>">
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="newest"     <?php echo $sort=='newest'    ?'selected':''; ?>>Newest</option>
                <option value="popular"    <?php echo $sort=='popular'   ?'selected':''; ?>>Most Popular</option>
                <option value="price_asc"  <?php echo $sort=='price_asc' ?'selected':''; ?>>Price Low→High</option>
                <option value="price_desc" <?php echo $sort=='price_desc'?'selected':''; ?>>Price High→Low</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Go</button>
        </div>
    </form>
</div>

<div class="row">
<?php
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $count++;
    $stars = round($row['avg_rating'] ?? 0);
?>
<div class="col-md-4 mb-4">
    <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['image'] ?? 'https://placehold.co/300x200?text=No+Image'); ?>"
             class="product-img" alt="">
        <div class="card-body">
            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h6><?php echo htmlspecialchars($row['name']); ?></h6>
            <p class="text-muted small"><?php echo htmlspecialchars(substr($row['description'], 0, 70)); ?>...</p>
            <p class="mb-1"><?php for ($i=1;$i<=5;$i++) echo $i<=$stars?'★':'☆'; ?></p>
            <strong>$<?php echo number_format($row['price'], 2); ?></strong>
            <div class="mt-2">
                <a href="products_details.php?id=<?php echo $row['product_id']; ?>"
                   class="btn btn-sm btn-primary w-100">View More</a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($count === 0) { ?>
    <div class="col-12">
        <div class="alert alert-warning">No products found. Try a different keyword or filter.</div>
    </div>
<?php } ?>
</div>

<?php include("includes/footer.php"); ?>