<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = $success = '';
$ingredients = [];

// Get all ingredients
try {
    $stmt = $pdo->query("SELECT * FROM ingredients ORDER BY name");
    $ingredients = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Failed to load ingredients: " . $e->getMessage();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert recipe
        $stmt = $pdo->prepare("INSERT INTO recipes 
            (title, instructions, cooking_time, servings) 
            VALUES (?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['instructions'],
            $_POST['cooking_time'],
            $_POST['servings']
        ]);
        
        $recipe_id = $pdo->lastInsertId();

        // Insert ingredients
        foreach($_POST['ingredients'] as $index => $ingredient_id) {
            if(!empty($ingredient_id) && !empty($_POST['quantities'][$index])) {
                $stmt = $pdo->prepare("INSERT INTO recipe_ingredients 
                    (recipe_id, ingredient_id, quantity) 
                    VALUES (?, ?, ?)");
                $stmt->execute([
                    $recipe_id,
                    $ingredient_id,
                    $_POST['quantities'][$index]
                ]);
            }
        }

        $pdo->commit();
        $success = "Recipe added successfully!";
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = "Error adding recipe: " . $e->getMessage();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h4 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Add New Recipe</h4>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST" id="recipeForm">
                        <!-- Basic Info -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Recipe Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cooking Time (minutes)</label>
                                    <input type="number" class="form-control" name="cooking_time" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Servings</label>
                                    <input type="number" class="form-control" name="servings" min="1" required>
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-carrot me-2"></i>Ingredients</h5>
                            <div id="ingredientsContainer">
                                <div class="ingredient-row mb-3">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-6">
                                            <select class="form-select" name="ingredients[]" required>
                                                <option value="">Select Ingredient</option>
                                                <?php foreach($ingredients as $ing): ?>
                                                    <option value="<?= $ing['id'] ?>">
                                                        <?= htmlspecialchars($ing['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" 
                                                   name="quantities[]" 
                                                   placeholder="Quantity (e.g., 2 cups)" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-ingredient">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" id="addIngredient">
                                <i class="fas fa-plus me-2"></i>Add Another Ingredient
                            </button>
                        </div>

                        <!-- Instructions -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-list-ol me-2"></i>Instructions</h5>
                            <textarea class="form-control" name="instructions" 
                                      rows="6" placeholder="Enter step-by-step instructions..." 
                                      required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Save Recipe
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Dynamic ingredient fields
document.getElementById('addIngredient').addEventListener('click', function() {
    const newRow = document.querySelector('.ingredient-row').cloneNode(true);
    newRow.querySelectorAll('select, input').forEach(field => {
        field.value = '';
        field.removeAttribute('required');
    });
    document.getElementById('ingredientsContainer').appendChild(newRow);
});

document.addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-ingredient')) {
        if(document.querySelectorAll('.ingredient-row').length > 1) {
            e.target.closest('.ingredient-row').remove();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>