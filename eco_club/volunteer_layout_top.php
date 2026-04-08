<?php
$pageTitle = $pageTitle ?? "Eco-Club";
$active    = $active ?? "dashboard";

require_once __DIR__ . "/includes/db.php";


$userId = 1;

// Fetch user from DB
$stmt = $pdo->prepare("SELECT name, role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$userName = $user['name'] ?? "User";
$userRole = $user['role'] ?? "volunteer";
$userRoleLabel = ucwords(str_replace('_', ' ', $userRole));
$avatarLetter = strtoupper(mb_substr($userName, 0, 1));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <title><?= htmlspecialchars($pageTitle) ?></title>

  <style>
    html, body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .lift { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
    .lift:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(15,23,42,.08); }
    .btn { transition: transform .06s ease; }
    .btn:active { transform: translateY(1px) scale(.99); }
    .panel { background: rgba(255,255,255,.78); backdrop-filter: blur(10px); }
    .trow:hover { background: rgba(2,132,199,.06); }
  </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-white to-emerald-50 text-slate-900">
  <div class="min-h-screen flex flex-nowrap">

    <!-- Sidebar -->
    <?php include __DIR__ . "/volunteer_sidebar.php"; ?>

    <!-- Main -->
    <main class="flex-1 min-w-0">

      <!-- Top Header -->
      <header class="bg-white/70 backdrop-blur border-b sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">

          <div>
            <h1 class="text-2xl font-bold"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-sm text-slate-500">Volunteer Panel</p>
          </div>

          <!-- RIGHT SIDE -->
          <div class="flex items-center gap-4">

            <div class="hidden sm:block text-right">
              <div class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></div>
              <div class="text-xs text-slate-500"><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? '')) ?></div>
            </div>

            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
              <?= htmlspecialchars($avatarLetter) ?>
            </div>

            <!-- LOGOUT -->
            <a href="/eco_club/volunteer_logout.php"
               class="btn px-3 py-2 rounded-xl bg-red-100 text-red-700 text-sm font-semibold hover:bg-red-200">
              Logout
            </a>

          </div>

        </div>
      </header>

      <!-- Page Content -->
      <div class="max-w-6xl mx-auto px-6 py-6">
