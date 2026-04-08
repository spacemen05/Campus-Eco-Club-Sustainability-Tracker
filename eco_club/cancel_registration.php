<?php
$pageTitle = "Cancel Registration";
$active = "my_reg";
include __DIR__ . "/_layout_top.php";
require_once __DIR__ . "/../includes/db.php";

$studentId = 2;
$eventId = (int)($_GET['event_id'] ?? 0);

$msg = "";
$type = "ok";

try {
  if ($eventId <= 0) throw new Exception("Invalid event.");

  $stmt = $pdo->prepare("DELETE FROM registrations WHERE event_id = ? AND student_id = ?");
  $stmt->execute([$eventId, $studentId]);

  if ($stmt->rowCount() === 0) throw new Exception("No registration found to cancel.");

  $msg = "Registration cancelled ";
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
    <div class="mt-5">
      <a class="btn px-4 py-2 rounded-2xl bg-slate-900 text-white hover:opacity-90"
         href="my_registrations.php">
        Back to My Registrations
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . "/_layout_bottom.php"; ?>
