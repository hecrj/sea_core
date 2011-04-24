<h1>Sign up!</h1>
<?php $Form = new Form; ?>
<?=
// Create a new object Form, set action to /signup
$Form->open('signup', array('data-ajax' => '/users/check')) .

// Set the first related object and relate next inputs to him
$Form->to('user', $user) .

// Username input
$Form->input('Username', 'username') .

// Password input
$Form->input('Password', 'password', array('type' => 'password')) .

// Confirm password input
$Form->input('Confirm password', 'confirm_password', array('type' => 'password')) .

// E-Mail input
$Form->input('E-Mail', 'email') .

// Close with a simple button
$Form->close('Create new user');
?>