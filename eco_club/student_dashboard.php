<?php
session_start();

$pageTitle = "Dashboard";
$active = "dashboard";
include __DIR__ . "/student_layout_top.php";
require_once __DIR__ . "/includes/db.php";

$studentId = (int)($_SESSION['user_id'] ?? 0);

// student name
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$studentId]);
$studentName = $stmt->fetchColumn() ?: "student";

// Total eco points
$stmt = $pdo->prepare("SELECT COALESCE(SUM(points),0) FROM recycling_logs WHERE student_id = ?");
$stmt->execute([$studentId]);
$ecoPoints = (int)$stmt->fetchColumn();

// Total recycling logs
$stmt = $pdo->prepare("SELECT COUNT(*) FROM recycling_logs WHERE student_id = ?");
$stmt->execute([$studentId]);
$logCount = (int)$stmt->fetchColumn();

// Upcoming events (all, sorted)
$stmt = $pdo->query("
  SELECT id, title, event_date, event_time, venue, status
  FROM events
  ORDER BY event_date ASC, event_time ASC
");
$upcomingEvents = $stmt->fetchAll();

// Count upcoming events
$upcomingCount = count($upcomingEvents);

// Recent logs (latest 5)
$stmt = $pdo->prepare("
  SELECT category, quantity, points, created_at
  FROM recycling_logs
  WHERE volunteer_id = ?
  ORDER BY created_at DESC
  LIMIT 5
");
$stmt->execute([$studentId]);
$recentLogs = $stmt->fetchAll();
?>

<!-- Welcome strip -->
<div class="mb-6">
  <div class="panel border rounded-3xl p-5 shadow-sm lift">
    <div class="flex items-center justify-between gap-4">
      <div>
        <div class="text-sm text-slate-500">Welcome back</div>
        <div class="text-2xl font-extrabold tracking-tight"><?= htmlspecialchars($studentName) ?></div>
        <div class="text-sm text-slate-600 mt-1">Live data from MySQL </div>
      </div>

      <div class="hidden sm:flex gap-2">
        <a class="btn px-4 py-2 rounded-2xl bg-slate-900 text-white hover:opacity-90" href="student_events.php">Browse Events</a>
        <a class="btn px-4 py-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700" href="student_recycling_submit.php">Submit Log</a>
      </div>
    </div>
  </div>
</div>

<!-- Cards -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="panel border rounded-3xl p-5 shadow-sm lift">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-slate-500">My Eco Points</div>
        <div class="text-4xl font-extrabold tracking-tight mt-1"><?= $ecoPoints ?></div>
        <div class="text-xs text-slate-500 mt-2">Calculated from recycling_logs</div>
      </div>
    </div>
    <div class="mt-4">
      <a class="btn inline-flex px-4 py-2 rounded-2xl border bg-white/70 hover:bg-white" href="student_points.php">View points</a>
    </div>
  </div>

  <div class="panel border rounded-3xl p-5 shadow-sm lift">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-slate-500">Upcoming Events</div>
        <div class="text-4xl font-extrabold tracking-tight mt-1"><?= $upcomingCount ?></div>
        <div class="text-xs text-slate-500 mt-2">Loaded from events table</div>
      </div>
    </div>
    <div class="mt-4">
      <a class="btn inline-flex px-4 py-2 rounded-2xl bg-slate-900 text-white hover:opacity-90" href="student_events.php">Go to events</a>
    </div>
  </div>

  <div class="panel border rounded-3xl p-5 shadow-sm lift">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-slate-500">Recycling Logs</div>
        <div class="text-4xl font-extrabold tracking-tight mt-1"><?= $logCount ?></div>
        <div class="text-xs text-slate-500 mt-2">Your submitted logs</div>
      </div>
    </div>
    <div class="mt-4">
      <a class="btn inline-flex px-4 py-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700" href="student_recycling_submit.php">Submit log</a>
    </div>
  </div>
</section>

<!-- Upcoming events table -->
<section class="panel border rounded-3xl shadow-sm overflow-hidden">
  <div class="p-5 flex items-center justify-between">
    <h2 class="text-lg font-extrabold tracking-tight">Upcoming Events</h2>
    <a class="btn text-sm px-3 py-2 rounded-2xl border bg-white/70 hover:bg-white" href="student_events.php">View all</a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 sticky top-0">
        <tr>
          <th class="text-left px-5 py-3">Event</th>
          <th class="text-left px-5 py-3">Date</th>
          <th class="text-left px-5 py-3">Time</th>
          <th class="text-left px-5 py-3">Venue</th>
          <th class="text-left px-5 py-3">Status</th>
          <th class="text-right px-5 py-3">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y">
<?php if (empty($upcomingEvents)): ?>
  <tr>
    <td colspan="6" class="px-5 py-4 text-slate-600">
      No upcoming events.
    </td>
  </tr>
<?php else: ?>
  <?php foreach ($upcomingEvents as $e): ?>
    <?php
      $status = $e['status'];
      $pill = $status === 'open'
        ? "bg-emerald-100 text-emerald-800"
        : ($status === 'limited'
            ? "bg-amber-100 text-amber-800"
            : "bg-rose-100 text-rose-800");
    ?>
    <tr class="trow">
      <td class="px-5 py-3 font-semibold"><?= htmlspecialchars($e['title']) ?></td>
      <td class="px-5 py-3"><?= htmlspecialchars($e['event_date']) ?></td>
      <td class="px-5 py-3"><?= htmlspecialchars(substr($e['event_time'],0,5)) ?></td>
      <td class="px-5 py-3"><?= htmlspecialchars($e['venue']) ?></td>
      <td class="px-5 py-3">
        <span class="px-2 py-1 rounded-full text-xs <?= $pill ?>">
          <?= htmlspecialchars($status) ?>
        </span>
      </td>
      <td class="px-5 py-3 text-right">
        <a class="btn px-3 py-2 rounded-2xl border bg-white/70 hover:bg-white"
           href="student_event_view.php?id=<?= (int)$e['id'] ?>">View</a>
        <a class="btn px-3 py-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700"
           href="student_event_signup.php?id=<?= (int)$e['id'] ?>">Sign Up</a>
      </td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>
</tbody>
    </table>
  </div>
</section>

<?php include __DIR__ . "/student_layout_bottom.php"; ?>
