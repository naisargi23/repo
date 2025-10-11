<h1>Create account</h1>
<?php if (!empty($errors)): ?>
  <div class="alert">
    <?php foreach ($errors as $e): ?>
      <div><?= \App\h($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<div class="card">
  <form method="post">
    <div class="row">
      <label>
        <div>Name</div>
        <input type="text" name="name" value="<?= \App\h($name ?? '') ?>" required>
      </label>
      <label>
        <div>Email</div>
        <input type="email" name="email" value="<?= \App\h($email ?? '') ?>" required>
      </label>
    </div>
    <div class="row">
      <label>
        <div>Password</div>
        <input type="password" name="password" required>
      </label>
      <label>
        <div>Confirm</div>
        <input type="password" name="confirm" required>
      </label>
    </div>
    <div style="margin-top:1rem">
      <button class="primary" type="submit">Create account</button>
    </div>
  </form>
</div>
