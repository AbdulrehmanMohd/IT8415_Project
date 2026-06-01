<?php include("includes/header.php"); ?>
<?php include("config/db.php"); ?>

<!-- HERO -->
<div class="text-center py-5 mb-4" style="background:#1a2456; border-radius:10px;">
    <h2 style="color:#c9a84c; font-size:2.5rem; font-weight:700;">ShopSphere</h2>
    <p style="color:#cccccc;">Modern Electronics E-Commerce Platform</p>
    <a href="products.php" class="btn btn-primary btn-lg mt-2">Browse Products</a>
</div>

<div class="card p-4 mb-4">
    <form method="GET" action="search.php" class="row g-2">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control"
                placeholder="Search products by name...">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">From date</label>
            <input type="date" name="date_from" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">To date</label>
            <input type="date" name="date_to" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
</div>

<h4>Latest Products</h4>
<hr>

<div class="row">
<?php
$result = mysqli_query($conn, "
    SELECT p.*, c.category_name,
    (SELECT AVG(rating) FROM dbproj_ratings WHERE product_id = p.product_id) AS avg_rating
    FROM dbproj_products p
    LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
    LIMIT 8
");

while ($row = mysqli_fetch_assoc($result)) {
    $stars = round($row['avg_rating'] ?? 0);
?>
<div class="col-md-3 mb-4">
    <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['image'] ?? 'https://placehold.co/300x200?text=No+Image'); ?>"
             class="product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <div class="card-body">
            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h6 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h6>
            <p class="text-muted small"><?php echo htmlspecialchars(substr($row['description'], 0, 60)); ?>...</p>
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
</div>

<?php include("includes/footer.php"); ?>