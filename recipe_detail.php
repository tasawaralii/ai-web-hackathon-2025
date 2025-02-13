<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$recipe_id = $_GET['id'] ?? 0;

// Get recipe details
$stmt = $pdo->prepare("
    SELECT r.*, 
           GROUP_CONCAT(i.name SEPARATOR ', ') AS ingredients
    FROM recipes r
    JOIN recipe_ingredients ri ON r.id = ri.recipe_id
    JOIN ingredients i ON ri.ingredient_id = i.id
    WHERE r.id = ?
");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

// Get user's pantry items
$stmt = $pdo->prepare("SELECT ingredient FROM pantry_items WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_ingredients = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Check which ingredients are available
$ingredients_list = explode(', ', $recipe['ingredients']);
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="card shadow-sm">
                <?php if($recipe['image_url']): ?>
                    <img src="<?= htmlspecialchars($recipe['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h2 class="mb-4"><?= htmlspecialchars($recipe['title']) ?></h2>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Details</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Cooking Time:</strong> <?= $recipe['cooking_time'] ?> mins</li>
                                        <li><strong>Servings:</strong> <?= $recipe['servings'] ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <h4><i class="fas fa-list-ul me-2"></i>Ingredients</h4>
                            <ul class="list-group mb-4">
                                <?php foreach($ingredients_list as $ingredient): ?>
                                    <li class="list-group-item">
                                        <?= htmlspecialchars(trim($ingredient)) ?>
                                        <?php if(in_array(strtolower(trim($ingredient)), array_map('strtolower', $user_ingredients))): ?>
                                            <span class="badge bg-success float-end">
                                                <i class="fas fa-check"></i> Available
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <h4><i class="fas fa-list-ol me-2"></i>Instructions</h4>
                            <div class="border p-3 rounded">
                                <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>