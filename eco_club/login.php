<?php
session_start();
require_once __DIR__ . "/includes/db.php"; // provides $pdo

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please fill in both email and password.";
    } else {
        //  your table column is password_hash (NOT password)
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $stored = (string)($user["password_hash"] ?? "");

            // Support BOTH hashed and plain-text passwords inside password_hash
            $isHash =
             str_starts_with($stored, '$2y$') ||
             str_starts_with($stored, '$2a$') ||
             str_starts_with($stored, '$argon2');
             
            $ok = $isHash ? password_verify($password, $stored) : hash_equals($stored, $password);

            if ($ok) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["role"] = $user["role"];

                //  your roles in DB look lowercase (volunteer), so normalize
                $role = strtolower(trim($user["role"]));

                if ($role === "admin") {
                    header("Location: admindashboard.php");
                } elseif ($role === "student") {
                    header("Location: student_dashboard.php");
                } elseif ($role === "organizer" || $role === "organiser" || $role === "event organizer" || $role === "event organiser") {
                    header("Location: Event_organiser_index.php");
                } elseif ($role === "volunteer") {
                    header("Location: volunteer_dashboard.php");

                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - EcoClub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Welcome Back</h1>

        <?php if (isset($_GET["success"])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm text-center">
                Registration successful! Please login.
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg"
                       value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700">
                Login
            </button>
        </form>

        <p class="mt-4 text-center text-sm">
            No account? <a href="register.php" class="text-blue-600">Register here</a>
        </p>
    </div>

</body>
</html>
