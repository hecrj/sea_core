<?php if($user->isLogged()): ?>
<h1>Welcome, <?= $user->username ?>!</h1>
<p>Your e-mail is: <strong><?= $user->email ?></strong></p>
<p>You are a <strong><?= $user->role ?></strong></p>
<p>Your are part of the groups:</p>
<ul>
<?php foreach($user->groups as $group): ?>
     <li><?= $group ?></li>
<?php endforeach; ?>
</ul>
<p><a href="/users/logout">Logout &raquo;</a></p>
<?php else: ?>
<h1>Login</h1>

<?php $Form = new Form; ?>
<?=

$Form->open('users/login') .

$Form->to('user') .

$Form->input('Username', 'username') .

$Form->input('Password', 'password', array('type' => 'password')) .

$Form->close('Login');

?>
<?php endif; ?>
