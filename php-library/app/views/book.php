<h1><?= \App\h($book['title']) ?></h1>
<p class="muted">by <?= \App\h($book['author']) ?></p>

<?php if (!empty($errors)): ?>
  <div class="alert">
    <?php foreach ($errors as $e): ?>
      <div><?= \App\h($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($justBorrowed): ?>
  <div class="alert">Book borrowed successfully.</div>
<?php endif; ?>

<div class="card">
  <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap">
    <div>
      <div><strong>ISBN:</strong> <span class="badge"><?= \App\h($book['isbn'] ?? '') ?></span></div>
      <div><strong>Copies:</strong> <?= (int) $book['available_copies'] ?> available of <?= (int) $book['total_copies'] ?></div>
    </div>
    <form method="post">
      <button class="primary" <?= (int)$book['available_copies'] <= 0 ? 'disabled' : '' ?>>Borrow</button>
    </form>
  </div>
</div>
