<h1>Sign up</h1>
<form method="post" action="/signup" class="card form">
    <?= csrf_field() ?>
    <label>Name
        <input type="text" name="name" required>
    </label>
    <label>Email
        <input type="email" name="email" required>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <button type="submit">Create account</button>
    <p>Already have an account? <a href="/login">Log in</a></p>
</form>
