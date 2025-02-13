<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = $success = '';
$ingredients = [];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    
    if(empty($name)) {
        $error = "Ingredient name cannot be empty";
    } else {
        try {
            // Check if ingredient exists
            $stmt = $pdo->prepare("SELECT id FROM ingredients WHERE name = ?");
            $stmt->execute([strtolower($name)]);
            
            if($stmt->rowCount() > 0) {
                $error = "Ingredient already exists!";
            } else {
                // Insert new ingredient
                $stmt = $pdo->prepare("INSERT INTO ingredients (name) VALUES (?)");
                $stmt->execute([ucfirst(strtolower($name))]);
                $success = "Ingredient added successfully!";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Get existing ingredients
try {
    $stmt = $pdo->query("SELECT * FROM ingredients ORDER BY name");
    $ingredients = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Failed to load ingredients: " . $e->getMessage();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h4 class="mb-4"><i class="fas fa-carrot me-2"></i>Manage Ingredients</h4>
            
            <!-- Add Ingredient Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Ingredient</h5>
                </div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" 
                                       placeholder="Enter ingredient name" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Add Ingredient
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Ingredients Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Existing Ingredients</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Ingredient Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0;
                                foreach($ingredients as $ingredient): ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><?= htmlspecialchars($ingredient['name']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="<?= $ingredient['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Delete ingredient functionality
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const ingredientId = this.dataset.id;
        if(confirm('Are you sure you want to delete this ingredient?')) {
            window.location.href = `delete_ingredient.php?id=${ingredientId}`;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>