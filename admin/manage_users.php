<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid != $_SESSION['user_id']) { // Prevent self-delete
        $stmt = mysqli_prepare($conn, "DELETE FROM dbproj_users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        mysqli_stmt_execute($stmt);
    }
    header("Location: manage_users.php");
    exit();
}

// Handle role change
if (isset($_POST['change_role'])) {
    $uid  = (int)$_POST['user_id'];
    $role = $_POST['role'];
    $stmt = mysqli_prepare($conn, "UPDATE dbproj_users SET role = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $role, $uid);
    mysqli_stmt_execute($stmt);
    header("Location: manage_users.php");
    exit();
}

$users = mysqli_query($conn, "SELECT * FROM dbproj_users ORDER BY created_at DESC");
?>

<h2 class="mb-4"><i class="bi bi-people"></i> Manage Users</h2>
<a href="dashboard.php" class="btn btn-outline-light btn-sm mb-3">← Back</a>

<div class="card p-4">
<table class="table table-dark table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Registered</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($u = mysqli_fetch_assoc($users)) { ?>
        <tr>
            <td><?php echo $u['user_id']; ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td>
                <form method="POST" class="d-flex gap-1">
                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                    <select name="role" class="form-select form-select-sm bg-dark text-white border-secondary w-auto">
                        <option value="customer" <?php echo $u['role']=='customer'?'selected':''; ?>>Customer</option>
                        <option value="seller" <?php echo $u['role']=='seller'?'selected':''; ?>>Seller</option>
                        <option value="admin" <?php echo $u['role']=='admin'?'selected':''; ?>>Admin</option>
                    </select>
                    <button type="submit" name="change_role" class="btn btn-sm btn-outline-warning">Save</button>
                </form>
            </td>
            <td class="small text-muted"><?php echo $u['created_at']; ?></td>
            <td>
                <?php if ($u['user_id'] != $_SESSION['user_id']) { ?>
                    <a href="manage_users.php?delete=<?php echo $u['user_id']; ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this user?')">
                        <i class="bi bi-trash"></i>
                    </a>
                <?php } else { ?>
                    <span class="text-muted small">(you)</span>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>

<?php include("../includes/footer.php"); ?>