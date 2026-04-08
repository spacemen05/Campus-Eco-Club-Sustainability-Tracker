<?php
$pageTitle = "Event Sign Up";
$active = "events";
include __DIR__ . "/_layout_top.php";
require_once __DIR__ . "/../includes/db.php";

$eventId = (int)($_GET['id'] ?? 0);
$studentId = 2; // Demo student for now

$msg = "";
$type = "ok";

try {
  if ($eventId <= 0) throw new Exception("Invalid event.");

  // Check event
  $stmt = $pdo->prepare("SELECT id, capacity, status FROM events WHERE id = ?");
  $stmt->execute([$eventId]);
  $event = $stmt->fetch();
  if (!$event) throw new Exception("Event not found.");
  if ($event['status'] === 'closed') throw new Exception("Registration closed.");

  // Prevent duplicate
  $stmt = $pdo->prepare("SELECT 1 FROM registrations WHERE event_id = ? AND student_id = ?");
  $stmt->execute([$eventId, $studentId]);
  if ($stmt->fetchColumn()) throw new Exception("You already signed up for this event.");

  // Capacity check
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
  $stmt->execute([$eventId]);
  $count = (int)$stmt->fetchColumn();
  if ($count >= (int)$event['capacity']) throw new Exception("Event is full.");

  // Insert
  $stmt = $pdo->prepare("INSERT INTO registrations (event_id, student_id) VALUES (?, ?)");
  $stmt->execute([$eventId, $studentId]);

  $msg = "Signed up successfully";
} catch (Throwable $e) {
  $type = "err";
  $msg = $e->getMessage();
}
?>

<div class="max-w-2xl">
  <div class="panel border rounded-3xl shadow-sm p-6">
    <div class="<?= $type === 'ok' ? 'text-emerald-700' : 'text-rose-700' ?> font-bold text-lg">
      <?= htmlspecialchars($msg) ?>
    </div>

    <div class="mt-5 flex gap-2">
      <a class="btn px-4 py-2 rounded-2xl border bg-white/70 hover:bg-white" href="events.php">Back to Events</a>
      <a class="btn px-4 py-2 rounded-2xl bg-slate-900 text-white hover:opacity-90" href="my_registrations.php">My Registrations</a>
    </div>
  </div>
</div>

<?php include __DIR__ . "/_layout_bottom.php"; ?>
