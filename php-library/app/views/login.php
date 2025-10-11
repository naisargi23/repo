<h1>Login</h1>
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
        <div>Email</div>
        <input type="email" name="email" value="<?= \App\h($email ?? '') ?>" required>
      </label>
      <label>
        <div>Password</div>
        <input type="password" name="password" required>
      </label>
    </div>
    <div style="margin-top:1rem">
      <button class="primary" type="submit">Login</button>
    </div>
  </form>
</div>
