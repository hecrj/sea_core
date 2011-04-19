<?php foreach($users as $user): ?>
<p><?= $user->username ?> &raquo; <?= $user->email ?></p>
<?php endforeach; ?>