<?php
require 'includes/db.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle item actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['delete'])) {
        // Delete item
        $stmt = $pdo->prepare("DELETE FROM pantry_items WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['item_id'], $_SESSION['user_id']]);
    } else {
        // Add/Edit item
        
        $name = trim($_POST['alias_name']);
        $category = $_POST['category'];
        $quantity = (int)$_POST['quantity'];
        $exp_date = $_POST['exp_date'];
        $ingredient = $_POST['ingredient'];
        
        if(isset($_POST['edit_id']) && $_POST['edit_id'] != null) {

            // Update existing item
            $stmt = $pdo->prepare("UPDATE pantry_items 
                                   SET alias_name=?, ingredient=?, category=?, quantity=?, expiration_date=?
                                   WHERE id=? AND user_id=?");
            $stmt->execute([$name, $ingredient, $category, $quantity, $exp_date, $_POST['edit_id'], $_SESSION['user_id']]);
        } else {
            // Insert new item
            $stmt = $pdo->prepare("INSERT INTO pantry_items 
                                  (user_id, alias_name, ingredient, category, quantity, expiration_date)
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $name, $ingredient, $category, $quantity, $exp_date]);
        }
    }
}

// Get filter/sort parameters
$category_filter = $_GET['category'] ?? 'all';
$sort = $_GET['sort'] ?? 'expiration_date_asc';

// Build query
$query = "SELECT * FROM pantry_items WHERE user_id = ?";
$params = [$_SESSION['user_id']];

// Apply category filter
if($category_filter !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category_filter;
}

// Apply sorting
switch($sort) {
    case 'name_asc':
        $query .= " ORDER BY alias_name ASC";
        break;
    case 'expiration_date_desc':
        $query .= " ORDER BY expiration_date DESC";
        break;
    default: // expiration_date_asc
        $query .= " ORDER BY expiration_date ASC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();
$ingredients = $pdo->query("SELECT * FROM ingredients")->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h4 class="mb-4"><i class="fas fa-box-open me-2"></i>My Pantry</h4>
            
            <!-- Filters/Sort Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form class="d-flex gap-2">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="all" <?= $category_filter === 'all' ? 'selected' : '' ?>>All Categories</option>
                            <?php
                            $categories = ['Vegetables', 'Fruits', 'Dairy', 'Meat', 'Grains', 'Beverages', 'Other'];
                            foreach($categories as $cat) {
                                echo "<option value='$cat' ".($category_filter === $cat ? 'selected' : '').">$cat</option>";
                            }
                            ?>
                        </select>
                        
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="expiration_date_asc" <?= $sort === 'expiration_date_asc' ? 'selected' : '' ?>>Sort by Expiration (Soonest First)</option>
                            <option value="expiration_date_desc" <?= $sort === 'expiration_date_desc' ? 'selected' : '' ?>>Sort by Expiration (Latest First)</option>
                            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Sort by Name</option>
                        </select>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#itemModal">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Alias Name</th>
                                    <th>Ingregiant</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Expiration Date</th>
                                    <th>Days Left</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): 
                                    $exp_date = new DateTime($item['expiration_date']);
                                    $today = new DateTime();
                                    $diff = $today->diff($exp_date);
                                    $days_left = $exp_date > $today ? $diff->days : -$diff->days;
                                ?>
                                <tr class="<?= $days_left < 0 ? 'table-danger' : ($days_left <= 3 ? 'table-warning' : '') ?>">
                                    <td><strong><?= htmlspecialchars($item['alias_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($item['ingredient']) ?></td>
                                    <td><?= $item['category'] ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= date('M j, Y', strtotime($item['expiration_date'])) ?></td>
                                    <td><?= $days_left >= 0 ? "$days_left days" : "Expired" ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-btn"
                                                data-id="<?= $item['id'] ?>"
                                                data-name="<?= htmlspecialchars($item['alias_name']) ?>"
                                                data-category="<?= $item['category'] ?>"
                                                data-quantity="<?= $item['quantity'] ?>"
                                                data-exp-date="<?= $item['expiration_date'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

<script>
// Edit item functionality
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editId').value = btn.dataset.id;
        document.getElementById('itemName').value = btn.dataset.name;
        document.getElementById('itemCategory').value = btn.dataset.category;
        document.getElementById('itemQuantity').value = btn.dataset.quantity;
        document.getElementById('itemExpDate').value = btn.dataset.expDate;
        
        document.querySelector('.modal-title').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Item';
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    });
});
</script>

<?php include 'includes/footer.php'; ?>