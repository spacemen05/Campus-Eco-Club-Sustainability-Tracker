<?php
session_start();
require_once __DIR__ . "/includes/db.php"; 


if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit();
}

$msg = null;
$msg_type = "green";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $log_id = (int)($_POST["log_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($log_id > 0 && ($action === "approve" || $action === "reject")) {


        $stmt = $pdo->prepare("SELECT id, volunteer_id, points FROM recycling_logs WHERE id = ?");
        $stmt->execute([$log_id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$log) {
            $msg = "Log not found.";
            $msg_type = "red";
        } else {
            $volunteer_id = (int)$log["volunteer_id"];
            $points_to_add = (int)$log["points"];

            if ($action === "approve") {

                try {
                    $pdo->beginTransaction();

                    $pdo->prepare("UPDATE users SET points = COALESCE(points,0) + ? WHERE id = ?")
                        ->execute([$points_to_add, $volunteer_id]);

                    $pdo->commit();

                    $msg = "Approved! Added $points_to_add points to volunteer ID #$volunteer_id.";
                    $msg_type = "green";
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    $msg = "Approve failed. Your users table may not have a 'points' column yet.";
                    $msg_type = "red";
                }

            } else {

                $stmt = $pdo->prepare("DELETE FROM recycling_logs WHERE id = ?");
                $stmt->execute([$log_id]);

                $msg = "Rejected! Log deleted (no status column in DB).";
                $msg_type = "yellow";
            }
        }
    }
}


$stmt = $pdo->query("
    SELECT rl.*, u.name AS volunteer_name
    FROM recycling_logs rl
    LEFT JOIN users u ON u.id = rl.volunteer_id
    ORDER BY rl.created_at DESC
    LIMIT 30
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Eco-Points - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { 500: '#10B981', 600: '#059669', 900: '#064E3B' } } } } }
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
                <i class="fa-solid fa-chart-line w-6"></i> <span class="font-medium">Dashboard</span>
            </a>
            <a href="users.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-users w-6"></i> <span class="font-medium">Manage Users</span>
            </a>
            <a href="adminecopoints.php" class="flex items-center px-4 py-3 bg-brand-600 text-white rounded-lg transition-colors">
                <i class="fa-solid fa-coins w-6"></i> <span class="font-medium">Eco-Points</span>
            </a>
            <a href="adminreports.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-file-invoice w-6"></i> <span class="font-medium">Reports</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 ml-64 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Eco-Points Verification</h2>
                <p class="text-gray-500">Approve adds points to the volunteer. Reject deletes the log (no status column).</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">SA</div>
            </div>
        </header>

        <?php if ($msg): ?>
            <div class="bg-<?= $msg_type ?>-100 border border-<?= $msg_type ?>-400 text-<?= $msg_type ?>-800 px-4 py-3 rounded relative mb-6">
                <i class="fa-solid fa-circle-info mr-2"></i>
                <span class="block sm:inline"><?= htmlspecialchars($msg) ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800 text-lg">Recent Logs</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-100 text-xs uppercase font-semibold text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Volunteer</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Points</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    <?= htmlspecialchars($row["volunteer_name"] ?? ("User #".$row["volunteer_id"])) ?>
                                </td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row["category"] ?? "") ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row["quantity"] ?? "") ?></td>
                                <td class="px-6 py-4 font-bold text-brand-600">+<?= htmlspecialchars($row["points"] ?? 0) ?> pts</td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row["created_at"] ?? "") ?></td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" class="inline-flex gap-2">
                                        <input type="hidden" name="log_id" value="<?= (int)$row["id"] ?>">

                                        <button type="submit" name="action" value="reject"
                                                onclick="return confirm('Reject = delete this log. Continue?');"
                                                class="px-3 py-1 bg-white border border-red-200 text-red-600 rounded hover:bg-red-50 transition-colors text-xs font-medium">
                                            Reject
                                        </button>

                                        <button type="submit" name="action" value="approve"
                                                class="px-3 py-1 bg-brand-600 text-white rounded hover:bg-brand-700 transition-colors text-xs font-medium shadow-sm">
                                            Approve
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-regular fa-circle-check text-4xl mb-2 text-green-500"></i>
                                    <p>No logs found.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
