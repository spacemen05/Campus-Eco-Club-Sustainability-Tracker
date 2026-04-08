<?php
$pageTitle = "Event Details";
$active = "events";
include __DIR__ . "/_layout_top.php";
require_once __DIR__ . "/../includes/db.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$e = $stmt->fetch();
?>

<?php if (!$e): ?>
  <div class="panel border rounded-3xl p-6">Event not found.</div>
<?php else: ?>
  <div class="panel border rounded-3xl shadow-sm p-6">
    <h2 class="text-2xl font-extrabold"><?= htmlspecialchars($e['title']) ?></h2>
    <p class="text-slate-600 mt-2">
      <?= htmlspecialchars($e['event_date']) ?> • <?= htmlspecialchars(substr($e['event_time'],0,5)) ?> • <?= htmlspecialchars($e['venue']) ?>
    </p>

    <div class="mt-4 p-4 rounded-2xl bg-white/70 border">
      <?= nl2br(htmlspecialchars($e['description'] ?? 'No description.')) ?>
    </div>

    <div class="mt-6 flex gap-2">
      <a class="btn px-4 py-2 rounded-2xl border bg-white/70 hover:bg-white" href="events.php">Back</a>
      <a class="btn px-4 py-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700"
         href="event_signup.php?id=<?= (int)$e['id'] ?>">Sign Up</a>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . "/_layout_bottom.php"; ?>
