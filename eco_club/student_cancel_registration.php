<?php
session_start();
require_once __DIR__ . "/includes/db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "Cancel Registration";
$active = "my_reg";
include __DIR__ . "/student_layout_top.php";

$studentId = (int)$_SESSION['user_id'];
$regId = (int)($_GET['reg_id'] ?? 0);


$msg = "";
$type = "ok";

try {
  if ($regId <= 0) throw new Exception("Invalid registration.");

  $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = ? AND student_id = ? LIMIT 1");
  $stmt->execute([$regId, $studentId]);

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
         href="student_my_registrations.php">
        Back to My Registrations
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . "/student_layout_bottom.php"; ?>
