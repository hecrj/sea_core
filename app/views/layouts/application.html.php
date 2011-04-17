<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>HectaMVC</title>
    <?= css_tag('custom') ?>
  </head>
  <body>
    <header>
	  <div class="wrap">
        <h1>HectaMVC</h1>
        <span>A simple but functional framework with MVC pattern!</span>
      </div>
    </header>
	<div id="content">
	    <div class="wrap">
			<div id="left">
	      		<?= $this->render() // You can use this method to show the associated view and include partials! ?>
			</div>
			<div id="right">
				<div class="block">
				<h3>Stay updated!</h3>
				<p>Be sure to stay updated about newest versions of HectaMVC.</p>
				<p>You can update easily and get the latest version in <a href="https://github.com/hector0193/hectamvc">GitHub</a>.</p>
				</div>
			</div>
	    </div>
	</div>
	<footer>
		HectaMVC is being developed by Héctor Ramón Jiménez, an 18 years old guy who loves coding and web development.
	</footer>
  </body>
</html>