<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>HectaMVC</title>
    <?= css_tag('custom') ?>
    <?= js_tag('jquery-1.5.2.min') ?>
    <?= js_tag('hectamvc') ?>
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
                <?php $this->render() // You can use this method to show the associated view and include partials! ?>
            </div>
            <div id="right">
                <?php
					// If $blocks is not defined
					if(!isset($blocks))
						// Render default layout blocks
						$this->render('layouts/blocks');
					
					// If $blocks is not an array
					elseif(!is_array($blocks))
						// Render the block
						$this->render($blocks);
					
					// If $block is an array
					else
						// Iterate over $blocks
						foreach($blocks as $block)
							// Render each block
							$this->render($block);
				?>
            </div>
        </div>
    </div>
    <footer>
        HectaMVC is being developed by Héctor Ramón Jiménez, an 18 years old guy who loves coding and web development.
    </footer>
  </body>
</html>