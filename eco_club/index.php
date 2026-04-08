<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eco Club | Sustainability Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-900 via-green-800 to-emerald-900 text-white">

<!-- NAVBAR -->
<nav class="flex justify-between items-center px-8 py-5">
    <h1 class="text-2xl font-extrabold tracking-wide">🌱 ECO CLUB</h1>
    <div class="space-x-4">
        <a href="login.php" class="px-4 py-2 rounded-lg bg-white text-green-800 font-semibold hover:bg-gray-200">
            Login
        </a>
        <a href="register.php" class="px-4 py-2 rounded-lg bg-emerald-400 text-green-900 font-semibold hover:bg-emerald-300">
            Register
        </a>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="text-center px-6 py-24">
    <h2 class="text-5xl font-extrabold mb-6">
        Campus Eco Club <br>
        <span class="text-emerald-300">Sustainability Tracker</span>
    </h2>

    <p class="max-w-2xl mx-auto text-lg text-green-100 mb-10">
        A centralized platform to manage eco-friendly events, track recycling activities,
        and reward students and volunteers for making the campus greener.
    </p>

    <div class="flex justify-center gap-6">
        <a href="register.php"
           class="px-8 py-4 rounded-xl bg-emerald-400 text-green-900 font-bold text-lg hover:bg-emerald-300 transition">
            Join Eco Club
        </a>
        <a href="login.php"
           class="px-8 py-4 rounded-xl border border-white font-semibold text-lg hover:bg-white hover:text-green-900 transition">
            Login
        </a>
    </div>
</section>

<!-- FEATURES -->
<section class="bg-white text-green-900 py-20">
    <h3 class="text-4xl font-extrabold text-center mb-12">What This System Does</h3>

    <div class="grid md:grid-cols-3 gap-10 px-10 max-w-6xl mx-auto">
        <div class="p-8 rounded-2xl shadow-lg text-center">
            <h4 class="text-xl font-bold mb-3">♻ Recycling Proof</h4>
            <p>Upload images or videos as proof of recycling activities and earn eco-points.</p>
        </div>

        <div class="p-8 rounded-2xl shadow-lg text-center">
            <h4 class="text-xl font-bold mb-3">📅 Eco Events</h4>
            <p>Join, manage, and participate in sustainability events across campus.</p>
        </div>

        <div class="p-8 rounded-2xl shadow-lg text-center">
            <h4 class="text-xl font-bold mb-3">🏆 Eco Points</h4>
            <p>Track contributions, earn points, and motivate eco-friendly behaviour.</p>
        </div>
    </div>
</section>

<!-- ROLES -->
<section class="py-20 px-10 text-center">
    <h3 class="text-4xl font-extrabold mb-10">User Roles</h3>

    <div class="grid md:grid-cols-4 gap-8 max-w-6xl mx-auto">
        <div class="p-6 rounded-xl bg-white/10">
            <h4 class="font-bold text-lg">Admin</h4>
            <p class="text-sm mt-2">Manages users, verifies data and oversees the system.</p>
        </div>

        <div class="p-6 rounded-xl bg-white/10">
            <h4 class="font-bold text-lg">Event Organiser</h4>
            <p class="text-sm mt-2">Plans and manages eco-friendly events.</p>
        </div>

        <div class="p-6 rounded-xl bg-white/10">
            <h4 class="font-bold text-lg">Volunteer</h4>
            <p class="text-sm mt-2">Participates in events and submits eco activities.</p>
        </div>

        <div class="p-6 rounded-xl bg-white/10">
            <h4 class="font-bold text-lg">Student</h4>
            <p class="text-sm mt-2">Joins events and tracks sustainability contributions.</p>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-green-950 text-center py-6 text-sm text-green-200">
    © <?= date("Y") ?> Eco Club Sustainability Tracker
</footer>

</body>
</html>
