<h1>My Library</h1>
<h2>Active Loans</h2>
<?php if (empty($activeLoans)): ?>
    <p>No active loans.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Borrowed</th>
            <th>Due</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($activeLoans as $loan): ?>
            <tr>
                <td><?= e($loan['title']) ?></td>
                <td><?= e($loan['author']) ?></td>
                <td><?= e($loan['borrowed_at']) ?></td>
                <td><?= e($loan['due_at']) ?></td>
                <td>
                    <form method="post" action="/return/<?= (int)$loan['id'] ?>">
                        <?= csrf_field() ?>
                        <button type="submit">Return</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<h2>Past Loans</h2>
<?php if (empty($pastLoans)): ?>
    <p>No past loans.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Borrowed</th>
            <th>Returned</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pastLoans as $loan): ?>
            <tr>
                <td><?= e($loan['title']) ?></td>
                <td><?= e($loan['author']) ?></td>
                <td><?= e($loan['borrowed_at']) ?></td>
                <td><?= e($loan['returned_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
