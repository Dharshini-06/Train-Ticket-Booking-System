<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];

    switch ($role) {
        case "admin":
            header("Location: admin/emp-login.php");
            break;
        case "employee":
            header("Location: employee/emp-login.php");
            break;
        case "passenger":
            header("Location: pass-login.php");
            break;
        default:
            // Invalid role or no selection
            header("Location: index.php?error=invalid_role");
            break;
    }
    exit();
} else {
    // If accessed directly, redirect back to index
    header("Location: index.php");
    exit();
}
?>
