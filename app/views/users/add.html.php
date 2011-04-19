<h1>Sign up!</h1>
<?php
// Create a new object Form and set action to /signup
$Form = new Form('signup');

// Set the object and name it to relate next inputs
$Form->to('user', $user);

// Username input
$Form->input('username');

// Password input
$Form->input('password', array('type' => 'password'));

// E-Mail input
$Form->input('email', array('label' => 'E-Mail'));

// Add button
$Form->button('Create new user');
?>
<?= $Form->render() ?>