<h1>Login</h1>
<form method="post" action="/login" class="card form">
    <?= csrf_field() ?>
    <label>Email
        <input type="email" name="email" required>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <button type="submit">Log in</button>
    <p>Don't have an account? <a href="/signup">Sign up</a></p>
</form>
