<?php
session_start();
require_once __DIR__ . "/includes/db.php"; // PDO connection: $pdo


if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit();
}




$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE LOWER(role)='student'");
$total_students = (int)$stmt->fetchColumn();


$stmt = $pdo->query("SELECT COALESCE(SUM(CAST(quantity AS DECIMAL(10,2))),0) FROM recycling_logs");
$total_qty = (float)$stmt->fetchColumn();
$total_kg = number_format($total_qty, 1); 


$stmt = $pdo->query("SELECT COALESCE(SUM(points),0) FROM recycling_logs");
$total_points = number_format((int)$stmt->fetchColumn());


$pending_count = 0;


$stmt = $pdo->query("
    SELECT rl.*, u.name AS volunteer_name
    FROM recycling_logs rl
    LEFT JOIN users u ON u.id = rl.volunteer_id
    ORDER BY rl.created_at DESC
    LIMIT 5
");
$table_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco-Club Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#10B981',
                            600: '#059669',
                            900: '#064E3B',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    
    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-900 text-white flex flex-col fixed h-full transition-all duration-300" id="sidebar">
            <div class="h-16 flex items-center justify-center border-b border-slate-800">
                <h1 class="text-2xl font-bold text-brand-500"><i class="fa-solid fa-leaf mr-2"></i>Admin</h1>
            </div>
            
            <nav class="flex-1 px-2 py-4 space-y-2">
                <a href="admindashboard.php" class="flex items-center px-4 py-3 bg-brand-600 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-chart-line w-6"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="users.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-users w-6"></i>
                    <span class="font-medium">Manage Users</span>
                </a>
                
                <a href="adminecopoints.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors"> 
                    <i class="fa-solid fa-coins w-6"></i>
                    <span class="font-medium">Eco-Points</span>
                </a>
                
                <a href="adminreports.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-file-invoice w-6"></i>
                    <span class="font-medium">Reports</span>
                </a>
            </nav>
            
            <div class="p-4 border-t border-slate-800">
                <a href="adminlogout.php" class="flex items-center px-4 py-2 text-red-400 hover:text-red-300 transition-colors">
                    <i class="fa-solid fa-right-from-bracket w-6"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </aside>            

        <main class="flex-1 ml-64 p-8 overflow-y-auto">

            <header class="flex justify-between items-center mb-8"> 
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Overview</h2>
                    <p class="text-gray-500">Welcome back, Admin.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <button class="p-2 text-gray-400 hover:text-brand-600 transition-colors relative">
                        <i class="fa-solid fa-bell text-xl"></i>
                        <?php if($pending_count > 0): ?>
                            <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                        <?php endif; ?>
                    </button>
                    
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-700">System Admin</p>
                            <p class="text-xs text-gray-500">IT Department</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">
                            SA
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Students</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $total_students; ?></h3>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-green-600 mt-4 flex items-center">
                        <i class="fa-solid fa-arrow-up mr-1"></i> <span>Active Users</span>
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Recycled (Kg)</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $total_kg; ?> kg</h3>
                        </div>
                        <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                            <i class="fa-solid fa-recycle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-green-600 mt-4 flex items-center">
                        <i class="fa-solid fa-arrow-up mr-1"></i> <span>Total Logged</span>
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Points Awarded</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $total_points; ?></h3>
                        </div>
                        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                            <i class="fa-solid fa-coins text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">
                        Total points from recycling logs
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Approvals</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $pending_count; ?></h3>
                        </div>
                        <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                            <i class="fa-solid fa-clock text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-orange-600 mt-4 font-medium">
                        (No status column in DB)
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Recent Recycling Submissions</h3>
                    <a href="adminecopoints.php" class="text-sm text-brand-600 font-medium hover:text-brand-700">View All</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Volunteer</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3">Quantity</th>
                                <th class="px-6 py-3">Points</th>
                                <th class="px-6 py-3">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (!empty($table_rows)): ?>
                                <?php foreach ($table_rows as $row): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        <?= htmlspecialchars($row["volunteer_name"] ?? ("User #" . ($row["volunteer_id"] ?? ""))) ?>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["category"] ?? "") ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["quantity"] ?? "") ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["points"] ?? "") ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["created_at"] ?? "") ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No records found.</td>
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
