<div class="ajax-container">
<?php foreach($users as $user): ?>
<p><?= $user->username ?> &raquo; <?= $user->email ?> &raquo; <?= $user->role ?></p>
<?php endforeach; ?>
<?= $user_pages->render(array('ajax' => true, 'pages' => 2)) ?>
</div>