<h1>Add a new post</h1>
<?php $Form = new Form(); ?>
<?=

$Form->open('posts/add') .

$Form->to('post') .

$Form->input('Title', 'title') .

$Form->textarea('Content', 'content', array('placeholder' => 'Add some content to the post here...')) .

$Form->select('Status', 'status', array('Draft', 'Published')) .

$Form->close('Add new post');

?>