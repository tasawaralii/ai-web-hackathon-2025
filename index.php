<?php

    include 'includes/db.php';

    if(!isset($_SESSION['user_id'])) {
        
        header("Location: login.php");
        exit();
    }

    $total_items = $pdo->query("SELECT COUNT(*) as total FROM pantry_items WHERE user_id = " . $_SESSION['user_id'])->fetch();
    $total_items = $total_items['total'];

    $total_recipes = $pdo->query(("SELECT COUNT(*) as total FROM recipes"))->fetch();
    $total_recipes = $total_recipes['total'];
    
    ?>
<?php include 'includes/header.php'; ?>
<div class="container-fluid">
    <div class="row">
        
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <!-- Quick Stats -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="stat-card p-4">
                        <h5><i class="fas fa-hourglass-end text-danger me-2"></i>Expiring Soon</h5>
                        <h2 class="mt-3"><?= $alert_count ?> Items</h2>
                        <p class="text-muted mb-0">Check <a href="alerts.php">alerts</a> for details</p>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="stat-card p-4">
                        <h5><i class="fas fa-cubes text-primary me-2"></i>Total Items</h5>
                        <h2 class="mt-3"><?= $total_items ?> Items</h2>
                        <p class="text-muted mb-0">Across 7 categories</p>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="stat-card p-4">
                        <h5><i class="fas fa-book-open text-success me-2"></i>Recipe Ideas</h5>
                        <h2 class="mt-3"><?= $total_recipes ?> Suggestions</h2>
                        <p class="text-muted mb-0">Based on your pantry</p>
                    </div>
                </div>
            </div>

<!-- Pantry Snapshot -->
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Pantry Snapshot</h5>
                        </div>
                        <div class="card-body">
                            <!-- Category Grid -->
                            <div class="row g-3">
                                <div class="col-6 col-md-4">
                                    <div class="pantry-item-card p-3 bg-white">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0">Vegetables</h6>
                                            <span class="category-badge badge">8 items</span>
                                        </div>
                                        <p class="text-muted mb-0 small">3 expiring soon</p>
                                    </div>
                                </div>
                                <!-- Repeat for other categories -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <a href="add-recipe.php">
                            <button class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-plus me-2"></i>Add Recipe
                            </button>
                            </a>
                            <a href="recipies.php">
                            <button class="btn btn-success w-100 mb-3">
                                <i class="fas fa-utensils me-2"></i>Generate Meal Plan
                            </button>
                            </a>
                            <a href="shopping_list.php">
                            <button class="btn btn-warning w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Quick Shop
                            </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>