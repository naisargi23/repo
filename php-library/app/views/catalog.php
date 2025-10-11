<h1>Catalog</h1>
<form method="get" class="card" style="margin-bottom:1rem">
  <div class="row">
    <input type="text" name="q" value="<?= \App\h($q ?? '') ?>" placeholder="Search title, author or ISBN">
    <button class="primary">Search</button>
  </div>
</form>
<div class="card">
  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Author</th>
        <th>ISBN</th>
        <th>Available</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($books as $b): ?>
        <tr>
          <td><?= \App\h($b['title']) ?></td>
          <td><?= \App\h($b['author']) ?></td>
          <td><span class="badge"><?= \App\h($b['isbn'] ?? '') ?></span></td>
          <td><?= (int) $b['available_copies'] ?> / <?= (int) $b['total_copies'] ?></td>
          <td><a href="/book.php?id=<?= (int) $b['id'] ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
