<?php
require 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM ingredients WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['success'] = "Ingredient deleted successfully";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Cannot delete ingredient: " . $e->getMessage();
    }
}

header("Location: ingredients.php");
exit();
?>