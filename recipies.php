<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user's pantry items
$stmt = $pdo->prepare("SELECT ingredient FROM pantry_items WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_ingredients = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Matching algorithm
$matched_recipes = [];

// Inside recipes.php
if(!empty($user_ingredients)) {
    $placeholders = rtrim(str_repeat('?,', count($user_ingredients)), ',');
    
    $query = "
        SELECT sub.* FROM (
            SELECT r.id, 
                   r.title, 
                   r.image_url, 
                   r.cooking_time,
                   COUNT(ri.ingredient_id) AS total_ingredients,
                   SUM(i.name IN ($placeholders)) AS matched_ingredients
            FROM recipes r
            JOIN recipe_ingredients ri ON r.id = ri.recipe_id
            JOIN ingredients i ON ri.ingredient_id = i.id
            GROUP BY r.id
        ) AS sub
        WHERE sub.matched_ingredients > 0
        ORDER BY (sub.matched_ingredients / sub.total_ingredients) DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($user_ingredients);
    $matched_recipes = $stmt->fetchAll();
}

?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h4 class="mb-4"><i class="fas fa-utensils me-2"></i>Recipe Suggestions</h4>

            <form method="POST" action="shopping_list.php">
    <div class="row">
        <?php if(empty($user_ingredients)): ?>
            <div class="alert alert-info">
                Add items to your pantry to get recipe suggestions!
            </div>
        <?php else: ?>
            <?php foreach($matched_recipes as $recipe): 
                $match_percent = round(($recipe['matched_ingredients'] / $recipe['total_ingredients']) * 100);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="selected_recipes[]" 
                                   value="<?= $recipe['id'] ?>" 
                                   id="recipe<?= $recipe['id'] ?>">
                            <label class="form-check-label" for="recipe<?= $recipe['id'] ?>">
                                Add to Shopping List
                            </label>
                        </div>
                    </div>
                    <?php if($recipe['image_url']): ?>
                        <img src="<?= htmlspecialchars($recipe['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= htmlspecialchars($recipe['title']) ?>
                            <span class="badge bg-success float-end">
                                <?= $match_percent ?>% Match
                            </span>
                        </h5>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                <?= $recipe['cooking_time'] ?> mins
                            </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="recipe_detail.php?id=<?= $recipe['id'] ?>" class="btn btn-primary btn-sm">
                                View Recipe
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if(!empty($matched_recipes)): ?>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-cart-plus me-2"></i>Generate Shopping List
        </button>
    </div>
    <?php endif; ?>
</form>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>