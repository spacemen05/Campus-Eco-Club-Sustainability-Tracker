<?php
session_start();
require_once __DIR__ . "/includes/db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "Submit Recycling Log";
$active = "recycling";
include __DIR__ . "/volunteer_layout_top.php";

$volunteerId = (int)$_SESSION['user_id']; 
$successMsg = "";
$errorMsg = "";

// Points rule
function calcPoints(string $category, string $quantityText): int {
  $category = strtolower(trim($category));
  preg_match('/(\d+(\.\d+)?)/', $quantityText, $m);
  $num = isset($m[1]) ? (float)$m[1] : 1.0;
  if ($num <= 0) $num = 1.0;

  $rate = match ($category) {
    'paper'   => 10,
    'plastic' => 6,
    'metal'   => 12,
    'glass'   => 8,
    default   => 5,
  };

  return (int)round($num * $rate);
}

/*IMAGE UPLOAD (Volunteer)*/
function uploadProofImageVolunteer(array $file): array {
  // Returns [ok(bool), pathOrError(string)]
  if (!isset($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    return [false, "Please upload an image proof."];
  }
  if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    $err = (int)$file['error'];
    return [false, "Upload failed (error code: {$err})."];
  }

  // Max 3MB
  $maxBytes = 3 * 1024 * 1024;
  if (($file['size'] ?? 0) > $maxBytes) {
    return [false, "Image too large. Max 3MB."];
  }

  // Validate extension + mime
  $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
  $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
  if (!in_array($ext, $allowedExt, true)) {
    return [false, "Invalid file type. Use JPG/PNG/WEBP."];
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
  if (!in_array($mime, $allowedMime, true)) {
    return [false, "Invalid image content."];
  }

  // Save to: eco_club/uploads/recycling_proofs/
  $targetDir = __DIR__ . "/uploads/recycling_proofs";
  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  $safeName = "proof_v{$GLOBALS['volunteerId']}_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $ext;
  $targetPath = $targetDir . "/" . $safeName;

  if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    return [false, "Could not save uploaded image."];
  }

  // Store relative path (relative to eco_club/)
  $relativePath = "uploads/recycling_proofs/" . $safeName;
  return [true, $relativePath];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $category = trim($_POST['category'] ?? '');
  $quantity = trim($_POST['quantity'] ?? '');

  if ($category === "" || $quantity === "") {
    $errorMsg = "Please fill in category and quantity.";
  } else {
    // Upload image
    [$ok, $pathOrError] = uploadProofImageVolunteer($_FILES['proof'] ?? []);
    if (!$ok) {
      $errorMsg = $pathOrError;
    } else {
      try {
        $points = calcPoints($category, $quantity);

        $stmt = $pdo->prepare("
          INSERT INTO recycling_logs (volunteer_id, category, quantity, points, proof_path)
          VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$volunteerId, $category, $quantity, $points, $pathOrError]);

        $successMsg = "Submitted  +$points points added!";
      } catch (Throwable $e) {
        $errorMsg = "Failed to save log to database.";
      }
    }
  }
}
?>

<div class="max-w-2xl">
  <?php if ($successMsg): ?>
    <div class="mb-4 p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100">
      <?= htmlspecialchars($successMsg) ?>
      <div class="mt-2">
        <a class="underline" href="volunteer_points.php">View Eco Points</a>
      </div>
    </div>
  <?php elseif ($errorMsg): ?>
    <div class="mb-4 p-4 rounded-2xl bg-rose-50 text-rose-800 border border-rose-100">
      <?= htmlspecialchars($errorMsg) ?>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl border shadow-sm p-6">
    <h2 class="text-xl font-bold">Submit Recycling Log</h2>
    <p class="text-slate-600 text-sm mt-1">Saved to MySQL </p>

    <form class="mt-5 space-y-4" method="post" enctype="multipart/form-data">
      <div>
        <label class="text-sm font-semibold">Category</label>
        <select name="category" class="w-full mt-1 border rounded-xl p-2">
          <option value="plastic">plastic</option>
          <option value="paper">paper</option>
          <option value="metal">metal</option>
          <option value="glass">glass</option>
          <option value="other">other</option>
        </select>
      </div>

      <div>
        <label class="text-sm font-semibold">Quantity</label>
        <input name="quantity" class="w-full mt-1 border rounded-xl p-2"
               placeholder="e.g. 2 kg / 1 box" required>
      </div>

      <div>
        <label class="text-sm font-semibold">Proof Image</label>
        <input type="file" name="proof" accept="image/*"
               class="w-full mt-1 border rounded-xl p-2" required>
        <p class="text-xs text-slate-500 mt-1">JPG/PNG/WEBP, max 3MB.</p>
      </div>

      <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
        Submit
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . "/volunteer_layout_bottom.php"; ?>
