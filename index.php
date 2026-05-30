<?php include("includes/header.php"); ?>
<?php include("config/db.php"); ?>

<!-- HERO SECTION -->
<div class="text-center py-5 mb-4" style="background: linear-gradient(135deg, #1a1a2e, #16213e); border-radius: 12px;">
    <h1 style="color:var(--bp-accent); font-size:3rem; font-weight:700;">
        <i class="bi bi-cart4"></i> ShopSphere
    </h1>
    <p style="color:#cccccc; font-size:1.2rem;">Modern Electronics E-Commerce Platform</p>
    <a href="products.php" class="btn btn-primary btn-lg mt-2">
        <i class="bi bi-grid"></i> Browse Products
    </a>
</div>

<!-- SEARCH BAR -->
<div class="card p-4 mb-4">
    <form method="GET" action="search.php" class="row g-2">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control bg-dark text-white border-secondary" placeholder="Search products...">
        </div>
        <div class="col-md-3">
            <input type="date" name="date_from" class="form-control bg-dark text-white border-secondary">
        </div>
        <div class="col-md-3">
            <input type="date" name="date_to" class="form-control bg-dark text-white border-secondary">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<!-- LATEST PRODUCTS -->
<h3 class="mb-3">🆕 Latest Products</h3>

<div class="row">
<?php
$result = mysqli_query($conn, "SELECT p.*, c.category_name,
    (SELECT AVG(rating) FROM dbproj_ratings WHERE product_id = p.product_id) AS avg_rating
    FROM dbproj_products p
    LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
    LIMIT 8");

while ($row = mysqli_fetch_assoc($result)) {
    $stars = round($row['avg_rating'] ?? 0);
?>
<div class="col-md-3 mb-4">
    <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['image'] ?? 'https://placehold.co/300x200?text=No+Image'); ?>"
             class="product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <div class="p-3">
            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h6><?php echo htmlspecialchars($row['name']); ?></h6>
            <p class="text-muted small"><?php echo htmlspecialchars(substr($row['description'], 0, 60)) . '...'; ?></p>
            <div class="mb-1">
                <?php for ($i = 1; $i <= 5; $i++) echo $i <= $stars ? '⭐' : '☆'; ?>
            </div>
            <h5 style="color:var(--bp-accent);">$<?php echo number_format($row['price'], 2); ?></h5>
            <a href="products_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-primary w-100 mt-2">
                View More
            </a>
        </div>
    </div>
</div>
<?php } ?>
</div>

<?php include("includes/footer.php"); ?>