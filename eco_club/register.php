<?php
session_start();
require_once __DIR__ . "/includes/db.php";

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = strtolower(trim($_POST["role"] ?? ""));
    $allowedRoles = ["organiser", "volunteer", "student"];

    if ($name === "" || $email === "" || $password === "" || $role === "") {
        $error = "Please fill in all fields.";
    } elseif (!in_array($role, $allowedRoles, true)) {
        $error = "Invalid role selected.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered. Please login.";
        } else {
            // Hash password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $role]);

            header("Location: login.php?success=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - EcoClub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">

    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Create Account</h1>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg"
                       value="<?= htmlspecialchars($_POST["name"] ?? "") ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg"
                       value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Role</label>
                <select name="role" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="" disabled <?= empty($_POST["role"] ?? "") ? "selected" : "" ?>>Select role</option>
                    <option value="organiser" <?= (($_POST["role"] ?? "") === "organiser") ? "selected" : "" ?>>Event Organiser</option>
                    <option value="volunteer" <?= (($_POST["role"] ?? "") === "volunteer") ? "selected" : "" ?>>Volunteer</option>
                    <option value="student" <?= (($_POST["role"] ?? "") === "student") ? "selected" : "" ?>>Student</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded-lg font-bold hover:bg-emerald-700">
                Register
            </button>
        </form>

        <p class="mt-4 text-center text-sm">
            Already have an account? <a href="login.php" class="text-blue-600">Login</a>
        </p>
    </div>

</body>
</html>
