<?php

// Get alert count for navigation badge
if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM pantry_items 
        WHERE user_id = ? 
        AND expiration_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $alert_count = $stmt->fetchColumn();
}

?>
       
       <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="/pantry">
                            <i class="fas fa-home me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pantry.php">
                            <i class="fas fa-box-open me-2"></i>
                            My Pantry
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="recipies.php">
                            <i class="fas fa-utensils me-2"></i>
                            Recipes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ingredients.php">
                            <i class="fas fa-bell me-2"></i>
                            Ingredients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="alerts.php">
                            <i class="fas fa-bell me-2"></i>
                            Alerts
                            <?php if(isset($alert_count) && $alert_count > 0): ?>
                                <span class="badge bg-danger ms-2"><?= $alert_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shopping_list.php">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Shopping List
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
