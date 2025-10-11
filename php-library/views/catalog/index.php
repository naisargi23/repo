<h1>Book Catalog</h1>
<?php if (empty($books)): ?>
    <p>No books found.</p>
<?php else: ?>
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
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= e($book['title']) ?></td>
                <td><?= e($book['author']) ?></td>
                <td><?= e($book['isbn'] ?? '') ?></td>
                <td><?= (int)$book['copies_available'] ?> / <?= (int)$book['copies_total'] ?></td>
                <td>
                    <?php if (auth_user() && (int)$book['copies_available'] > 0): ?>
                        <form method="post" action="/borrow/<?= (int)$book['id'] ?>">
                            <?= csrf_field() ?>
                            <button type="submit">Borrow</button>
                        </form>
                    <?php elseif (!auth_user()): ?>
                        <a href="/login">Login to borrow</a>
                    <?php else: ?>
                        <span>Not available</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
