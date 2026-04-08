```php
<?php
session_start();
require_once __DIR__ . "/includes/db.php"; // PDO: $pdo

// Admin-only guard 
if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit();
}

$msg = null;
$msg_type = "green";

// --- ACTION: HANDLE ADD USER ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_user"])) {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    //  DB stores roles
    $role = "student";

    if ($name === "" || $email === "" || $password === "") {
        $msg = "Please fill in all fields.";
        $msg_type = "red";
    } else {
        // Store into password_hash  hash it.
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (role, name, email, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$role, $name, $email, $password_hash]);

            $msg = "New student added successfully!";
            $msg_type = "green";
        } catch (Throwable $e) {
            $msg = "Error adding user. (Email might already exist)";
            $msg_type = "red";
        }
    }
}

// --- ACTION: HANDLE DELETE USER ---
if (isset($_GET["delete_id"])) {
    $id = (int)($_GET["delete_id"] ?? 0);

    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $msg = "User deleted successfully.";
            $msg_type = "green";
        } catch (Throwable $e) {
            $msg = "Error deleting user.";
            $msg_type = "red";
        }
    }
}

// --- FETCH ALL STUDENTS ---
$stmt = $pdo->query("SELECT id, role, name, email, created_at FROM users WHERE LOWER(role)='student' ORDER BY id DESC");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - EcoClub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 500: '#10B981', 600: '#059669', 900: '#064E3B' } } } }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

<div class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex flex-col fixed h-full transition-all duration-300">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-2xl font-bold text-brand-500"><i class="fa-solid fa-leaf mr-2"></i>Admin</h1>
        </div>
        <nav class="flex-1 px-2 py-4 space-y-2">
            <a href="admindashboard.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-chart-line w-6"></i><span class="font-medium">Dashboard</span>
            </a>
            <a href="users.php" class="flex items-center px-4 py-3 bg-brand-600 text-white rounded-lg transition-colors">
                <i class="fa-solid fa-users w-6"></i><span class="font-medium">Manage Users</span>
            </a>
            <a href="adminecopoints.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-coins w-6"></i><span class="font-medium">Eco-Points</span>
            </a>
            <a href="adminreports.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-file-invoice w-6"></i><span class="font-medium">Reports</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 ml-64 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manage Students</h2>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">SA</div>
            </div>
        </header>

        <?php if ($msg): ?>
            <div class="bg-<?= $msg_type ?>-100 border border-<?= $msg_type ?>-400 text-<?= $msg_type ?>-700 px-4 py-3 rounded relative mb-6">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
            <h3 class="font-bold text-lg mb-4">Add New Student</h3>
            <form method="POST" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <button type="submit" name="add_user"
                        class="px-6 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors">
                    <i class="fa-solid fa-plus mr-2"></i> Add
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800">Student List</h3>
            </div>

            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-100 text-xs uppercase font-semibold text-gray-500">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">#<?= (int)$row["id"] ?></td>
                                <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($row["name"] ?? "") ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row["email"] ?? "") ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row["created_at"] ?? "") ?></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="users.php?delete_id=<?= (int)$row["id"] ?>"
                                       onclick="return confirm('Are you sure you want to delete this user?');"
                                       class="text-red-500 hover:text-red-700 font-medium">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-4 text-center">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
```
