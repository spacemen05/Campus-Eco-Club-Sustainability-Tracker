<aside class="w-72 shrink-0 hidden md:flex md:flex-col border-r bg-white/70 backdrop-blur min-h-screen">
  <div class="p-5 border-b">
    <div class="flex items-center gap-3">
      <div class="w-11 h-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold shadow">
        EC
      </div>
      <div>
        <div class="font-extrabold leading-tight tracking-tight">Eco-Club Tracker</div>
        <div class="text-xs text-slate-500">Student Dashboard</div>
      </div>
    </div>
  </div>

  <?php
  if (!function_exists('navItem')) {
    function navItem(string $key, string $active): string {
      return $key === $active
        ? "bg-emerald-600 text-white shadow"
        : "text-slate-700 hover:bg-slate-100";
    }
  }
  ?>

  <nav class="p-3 space-y-2 text-sm">
    <a href="student_dashboard.php" class="flex items-center gap-2 px-3 py-2 rounded-2xl transition <?= navItem('dashboard',$active) ?>">
      <span></span><span class="font-medium">Dashboard</span>
    </a>

    <a href="student_events.php" class="flex items-center gap-2 px-3 py-2 rounded-2xl transition <?= navItem('events',$active) ?>">
      <span></span><span class="font-medium">Upcoming Events</span>
    </a>

    <a href="student_my_registrations.php" class="flex items-center gap-2 px-3 py-2 rounded-2xl transition <?= navItem('my_reg',$active) ?>">
      <span></span><span class="font-medium">My Registrations</span>
    </a>

    <a href="student_recycling_submit.php" class="flex items-center gap-2 px-3 py-2 rounded-2xl transition <?= navItem('recycling',$active) ?>">
      <span></span><span class="font-medium">Submit Recycling Log</span>
    </a>

    <a href="student_points.php" class="flex items-center gap-2 px-3 py-2 rounded-2xl transition <?= navItem('points',$active) ?>">
      <span></span><span class="font-medium">Eco Points</span>
    </a>

  </nav>

  <div class="mt-auto p-4 border-t text-xs text-slate-500">
    UI polished • consistent • clean
  </div>
</aside>
