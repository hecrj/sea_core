Forms = {
	blocks : null,
	hidden : null,
	queues : $({}),
	timeout : null,
	init : function()
	{
		Forms.blocks = $('#right div.block:visible');
		Forms.hidden = $('#right div.block.hidden');
		$window = $(window);
		$('input, select, textarea').each(function(i, input){
			var $input = $(input), $block = $('#info_' + input.id);
			
			if(!$block.length) return true;
			
			$input
			.focusin(function(){
				clearTimeout(Forms.timeout);
				Forms.blocks.slideUp(200);
				$block.slideDown(200);
			})
			.focusout(function(){
				$block.slideUp(200);
				Forms.timeout = setTimeout(function(){ Forms.blocks.slideDown(200) }, 1);
			})
		});
		$window.scroll(function(){
			if($window.scrollTop() >= 100)
				Forms.hidden.css({ position: 'fixed', top: 10 });
			else
				Forms.hidden.css({ position: 'absolute', top: 'auto' });
		});
	}
}

$(function(){
	Forms.init();
});