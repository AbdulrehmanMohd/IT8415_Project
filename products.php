<?php
include("includes/header.php");
include("config/db.php");
?>

<h4>Products</h4>
<hr>

<div class="card p-3 mb-4">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control"
                placeholder="Search by name..."
                value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php
                $cat_result = mysqli_query($conn, "SELECT * FROM dbproj_categories ORDER BY category_name");
                while ($c = mysqli_fetch_assoc($cat_result)) {
                    $sel = (isset($_GET['category']) && $_GET['category'] == $c['category_id']) ? 'selected' : '';
                    echo "<option value='{$c['category_id']}' $sel>" . htmlspecialchars($c['category_name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="newest"     <?php echo (($_GET['sort']??'')=='newest')    ?'selected':''; ?>>Newest First</option>
                <option value="price_asc"  <?php echo (($_GET['sort']??'')=='price_asc') ?'selected':''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo (($_GET['sort']??'')=='price_desc')?'selected':''; ?>>Price: High to Low</option>
                <option value="popular"    <?php echo (($_GET['sort']??'')=='popular')   ?'selected':''; ?>>Most Popular</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>
</div>

<?php
$per_page = 9;
$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset   = ($page - 1) * $per_page;

$where  = "WHERE 1=1";
$params = [];
$types  = "";

if (!empty($_GET['q'])) {
    $where   .= " AND MATCH(p.name, p.description) AGAINST(? IN BOOLEAN MODE)";
    $params[] = $_GET['q'] . '*';
    $types   .= "s";
}
if (!empty($_GET['category'])) {
    $where   .= " AND p.category_id = ?";
    $params[] = (int)$_GET['category'];
    $types   .= "i";
}

$order = "ORDER BY p.created_at DESC";
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':  $order = "ORDER BY p.price ASC";  break;
        case 'price_desc': $order = "ORDER BY p.price DESC"; break;
        case 'popular':    $order = "ORDER BY p.views DESC"; break;
    }
}

$count_sql = "SELECT COUNT(*) AS total FROM dbproj_products p $where";
$stmt = mysqli_prepare($conn, $count_sql);
if (!empty($types)) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$total_rows  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
$total_pages = ceil($total_rows / $per_page);

$sql = "SELECT p.*, c.category_name,
        (SELECT AVG(rating) FROM dbproj_ratings WHERE product_id = p.product_id) AS avg_rating
        FROM dbproj_products p
        LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
        $where $order LIMIT ? OFFSET ?";

$all_params   = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types    = $types . "ii";

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
        <div class="card-body">
            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h6 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h6>
            <p class="text-muted small"><?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>...</p>
            <p class="mb-1">
                <?php for ($i=1;$i<=5;$i++) echo $i<=$stars?'★':'☆'; ?>
                <small class="text-muted">(<?php echo number_format($row['avg_rating']??0,1); ?>)</small>
            </p>
            <strong>$<?php echo number_format($row['price'], 2); ?></strong>
            <p class="text-muted small mb-2">Stock: <?php echo $row['stock']; ?></p>
            <a href="products_details.php?id=<?php echo $row['product_id']; ?>"
               class="btn btn-sm btn-primary w-100">View More</a>
        </div>
    </div>
</div>
<?php } ?>
</div>

<?php if ($total_pages > 1) { ?>
<nav>
    <ul class="pagination justify-content-center">
        <?php for ($p=1; $p<=$total_pages; $p++) {
            $qs = http_build_query(array_merge($_GET, ['page'=>$p]));
        ?>
        <li class="page-item <?php echo $p==$page?'active':''; ?>">
            <a class="page-link" href="?<?php echo $qs; ?>"><?php echo $p; ?></a>
        </li>
        <?php } ?>
    </ul>
</nav>
<?php } ?>

<?php include("includes/footer.php"); ?>