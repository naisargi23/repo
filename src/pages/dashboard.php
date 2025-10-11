<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/helpers.php';

// Temporary counters until DB layer
$counters = [
    'books' => 0,
    'students' => 0,
    'loansActive' => 0,
    'loansOverdue' => 0,
];

ob_start();
?>
<h2>Dashboard</h2>
<div class="grid">
    <div class="card">
        <h3>Total Books</h3>
        <div style="font-size:1.8rem; font-weight:700;">
            <?= (int)$counters['books'] ?>
        </div>
    </div>
    <div class="card">
        <h3>Students</h3>
        <div style="font-size:1.8rem; font-weight:700;">
            <?= (int)$counters['students'] ?>
        </div>
    </div>
    <div class="card">
        <h3>Active Loans</h3>
        <div style="font-size:1.8rem; font-weight:700;">
            <?= (int)$counters['loansActive'] ?>
        </div>
    </div>
    <div class="card">
        <h3>Overdue Loans</h3>
        <div style="font-size:1.8rem; font-weight:700; color:#dc2626;">
            <?= (int)$counters['loansOverdue'] ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
render('Dashboard', $content);
