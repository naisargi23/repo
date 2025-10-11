<h1>My Loans</h1>
<?php if (!empty($errors)): ?>
  <div class="alert">
    <?php foreach ($errors as $e): ?>
      <div><?= \App\h($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php if (!empty($messages)): ?>
  <div class="alert">
    <?php foreach ($messages as $m): ?>
      <div><?= \App\h($m) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<div class="card">
  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Borrowed</th>
        <th>Due</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($loans as $l): ?>
      <tr>
        <td><?= \App\h($l['title']) ?></td>
        <td><?= date('Y-m-d', strtotime($l['borrowed_at'])) ?></td>
        <td><?= date('Y-m-d', strtotime($l['due_date'])) ?></td>
        <td>
          <?php if ($l['returned_at']): ?>
            <span class="badge">Returned</span>
          <?php elseif ($l['is_overdue']): ?>
            <span class="badge danger">Overdue</span>
          <?php else: ?>
            <span class="badge">Active</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!$l['returned_at']): ?>
          <form method="post" style="display:inline">
            <input type="hidden" name="return_id" value="<?= (int) $l['id'] ?>">
            <button>Return</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
