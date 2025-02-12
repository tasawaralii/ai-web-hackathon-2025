<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Pantry</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
    :root {
        --primary-color: #2a9d8f;
        --secondary-color: #264653;
        --accent-color: #e9c46a;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #ffffff;
    }

    .navbar {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }

    .stat-card {
        background: #ffffff;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .category-badge {
            background: var(--accent-color);
            color: var(--secondary-color);
            font-weight: 600;
        }
        
    .sidebar {
        background: #ffffff;
        border-right: 1px solid rgba(0,0,0,0.1);
        height: 100vh;
        position: fixed;
        left: 0;
        z-index: 1000;
    }

    .pantry-item-card {
        background: #ffffff;
        border-left: 4px solid var(--primary-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    @media (max-width: 991.98px) {
        .sidebar {
            position: fixed;
            top: 56px; /* Navbar height */
            left: -100%;
            transition: all 0.3s;
        }
        .sidebar.show {
            left: 0;
        }
        main {
            margin-left: 0 !important;
        }
    }
</style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <!-- Sidebar Toggle Button -->
            <button class="btn btn-link text-light me-3 d-lg-none" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#sidebar">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="#">
                <i class="fas fa-leaf me-2"></i>
                Smart Pantry
            </a>
            
            <?php if(isset($_SESSION['user_id'])) : ?>

            <!-- User Profile Dropdown -->
            <div class="dropdown ms-auto">
                <button class="btn btn-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-2"></i><?= $_SESSION['username'] ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            <?php endif ?>
            </div>
        </div>
    </nav>