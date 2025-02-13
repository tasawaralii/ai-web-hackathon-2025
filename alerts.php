<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get items expiring within 3 days
$alert_threshold = 3;
$today = new DateTime();
$threshold_date = $today->modify("+$alert_threshold days")->format('Y-m-d');

$stmt = $pdo->prepare("
    SELECT *, 
           DATEDIFF(expiration_date, CURDATE()) AS days_remaining
    FROM pantry_items 
    WHERE user_id = ?
    AND expiration_date <= ?
    ORDER BY expiration_date ASC
");
$stmt->execute([$_SESSION['user_id'], $threshold_date]);
$alert_items = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h4 class="mb-4"><i class="fas fa-bell me-2"></i>Expiration Alerts</h4>
            
            <?php if(empty($alert_items)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    No expiring items! Your pantry is fresh!
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($alert_items as $item): 
                        $exp_date = new DateTime($item['expiration_date']);
                        $days_left = $item['days_remaining'];
                        $alert_type = $days_left < 0 ? 'danger' : ($days_left <= 2 ? 'warning' : 'info');
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card alert-card border-<?= $alert_type ?> shadow-sm">
                            <div class="card-header bg-<?= $alert_type ?> text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= htmlspecialchars($item['alias_name']) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Expiration Date</small>
                                        <div class="fw-bold">
                                            <?= date('M j, Y', strtotime($item['expiration_date'])) ?>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted">Days Remaining</small>
                                        <div class="h4 mb-0 text-<?= $alert_type ?>">
                                            <?= $days_left >= 0 ? $days_left : 'EXPIRED' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-secondary">
                                        <?= $item['category'] ?>
                                    </span>
                                    <form method="POST" action="delete_item.php">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
.alert-card {
    transition: transform 0.2s;
    border-left-width: 4px;
}
.alert-card:hover {
    transform: translateY(-3px);
}
</style>

<?php include 'includes/footer.php'; ?>