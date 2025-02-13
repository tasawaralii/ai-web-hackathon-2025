<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["ingredient_id"])) {


    $ingredient = $_POST["ingredient_id"];

    $exists = $pdo->query("SELECT 1 FROM pantry_items WHERE ingredient = '$ingredient'")->fetch();

    if($exists) {
        print_r($exists);
        $pdo->query("UPDATE pantry_items SET quantity = quantity + 5 WHERE ingredient = '$ingredient'");
        echo "1";
    }

    exit;    
}


if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["insert"]) && $_POST["insert"] == "true") {
            
    $name = trim($_POST['alias_name']);
    $category = $_POST['category'];
    $quantity = (int)$_POST['quantity'];
    $exp_date = $_POST['exp_date'];
    $ingredient = $_POST['ingredient'];

    $stmt = $pdo->prepare("INSERT INTO pantry_items 
    (user_id, alias_name, ingredient, category, quantity, expiration_date)
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $name, $ingredient, $category, $quantity, $exp_date]);

    echo "1";
    exit;
}

$selected_recipes = $_POST['selected_recipes'] ?? [];
$shopping_list = [];

if(empty($selected_recipes)) {

    $all_recipies = $pdo->query("SELECT id FROM recipes")->fetchAll();

    foreach($all_recipies as $s) {

        $selected_recipes[] = $s['id'];
    }

}


    // Get required ingredients
    $placeholders = rtrim(str_repeat('?,', count($selected_recipes)), ',');
    $stmt = $pdo->prepare("
        SELECT i.name, COUNT(*) AS recipe_count, 
               GROUP_CONCAT(ri.quantity SEPARATOR ' + ') AS quantities
        FROM recipe_ingredients ri
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE ri.recipe_id IN ($placeholders)
        GROUP BY i.name
    ");
    $stmt->execute($selected_recipes);
    $required_ingredients = $stmt->fetchAll();

    // Get user's pantry items
    $stmt = $pdo->prepare("SELECT LOWER(ingredient) AS name FROM pantry_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $pantry_items = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Generate shopping list
    foreach($required_ingredients as $ingredient) {
        if(!in_array(strtolower($ingredient['name']), $pantry_items)) {
            $shopping_list[] = $ingredient;
        }
    }

$categories = ['Vegetables', 'Fruits', 'Dairy', 'Meat', 'Grains', 'Beverages', 'Other'];
$ingredients = $pdo->query("SELECT * FROM ingredients")->fetchAll();

?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h4 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping List</h4>
            
            <?php if(empty($selected_recipes)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No recipes selected. Please choose recipes from the Recipes page.
                </div>
            <?php elseif(empty($shopping_list)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    You have all the required ingredients for the selected recipes!
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Ingredient</th>
                                        <th>Required For</th>
                                        <th>Total Needed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($shopping_list as $item): ?>
                                    <tr data-ingredient-id="<?= $item['name']?>">
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= $item['recipe_count'] ?> recipes</td>
                                        <td><?= $item['quantities'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success mark-purchased">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger remove-item">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>



<!-- Add/Edit Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-box me-2"></i>Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="editId">
                    <input type="hidden" name="insert" value="true">
                    
                    <div class="mb-3">
                        <label class="form-label">Item Alias Name</label>
                        <input type="text" class="form-control" name="alias_name" id="itemName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" id="itemCategory" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ingrediant</label>
                        <select class="form-select" name="ingredient" id="itemIngredient" required>
                            <?php foreach($ingredients as $ing): ?>
                                <option value="<?= $ing["name"] ?>"><?= $ing["name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="itemQuantity" min="1" value="1" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiration Date</label>
                            <input type="date" class="form-control" name="exp_date" id="itemExpDate" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add this script in shopping_list.php -->
<script>

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#itemModal form");
    
    form.addEventListener("submit", async function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(form); // Collect form data
        let response = await fetch("", {
            method: "POST",
            body: formData
        });

        let text = await response.text();

        if (text.trim() === "1") {
            let itemModal = bootstrap.Modal.getInstance(document.getElementById("itemModal"));
            if (itemModal) {
                itemModal.hide(); // Hide modal if ingredient is successfully added
            }
            showToast("Ingredient added successfully!");
        } else {
            showToast("Error adding ingredient. Please try again.", "danger");
        }
    });
});


document.querySelectorAll('.mark-purchased').forEach(btn => {
    btn.addEventListener('click', async function() {
        const row = this.closest('tr');
        const ingredientId = row.dataset.ingredientId;
        
        try {
            const formData = new URLSearchParams();
            formData.append("ingredient_id", ingredientId);
            formData.append("quantity", 5);

            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString() // Convert form data to URL-encoded string
            });

            const text = await response.text();

            if (text == "1") {
                row.classList.add('table-success');
                row.querySelector('.mark-purchased').disabled = true;
                showToast('Ingredient added to pantry!');
            } else {
                // Show the modal when the ingredient is not found
                let itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
                itemModal.show();
            }

        } catch(error) {
            console.error('Error:', error);
            showToast('Error updating pantry', 'danger');
        }
    });
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.marginLeft = "30px";
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    new bootstrap.Toast(toast).show();
    // setTimeout(() => toast.remove(), 50000);
}
</script>

<!-- <script>
// Add interactive features
document.querySelectorAll('.mark-purchased').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('tr').classList.toggle('table-success');
    });
});

document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('tr').remove();
    });
});
</script> -->

<style>
.table-hover tr {
    transition: all 0.2s ease;
}
.table-success td {
    text-decoration: line-through;
    opacity: 0.6;
}
</style>

<?php include 'includes/footer.php'; ?>