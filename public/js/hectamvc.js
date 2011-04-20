Forms = {
	blocks : null,
	hidden : null,
	timeout : null,
	init : function()
	{
		Forms.blocks = $('#right div.block:visible');
		Forms.hidden = $('#right div.block.hidden');
		$window = $(window);
		$('form').each(function(f, form){
			var $form = $(form), ajax = $form.data('ajax');
			$('input, select, textarea', $form).each(function(i, input){
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
				
				if(ajax)
				{
					var $field = $('#field_' + input.id), $div = $('<div class="message">').appendTo($block).hide();
					$input.change(function(){
						HectaMVC.Ajax(Forms, { url: ajax + '/' + input.id, data: $form.serializeArray(), field: $field, div: $div });
					});
				}
			});
			$window.scroll(function(){
				if($window.scrollTop() >= 100)
					Forms.hidden.css({ position: 'fixed', top: 10 });
				else
					Forms.hidden.css({ position: 'absolute', top: 'auto' });
			});
		});	
	},
	Ajax_Load : function(options){
		options.field.removeClass('error success').addClass('loading');
	},
	Ajax_Response : function(response, options)
	{	
		if(response.indexOf("is correct!") != -1)
			var status = 'success';
		else
			var status = 'error';
		
		options.field.removeClass('loading').addClass(status);
		options.div.removeClass('error success').addClass(status).html(response).show();
	}
}

var HectaMVC = {
	Ajax: true,
	optionsDefault : { delay: false, Load: true, type: 'POST', url: '/', data: {}, Response: true, api: false },
	Request : false,
	runningRequest : false,
	delayCount : 0,
	
	init : function(){
		Forms.init();
	},
	Ajax : function( object, optionsCustom ){
		if(! this.Ajax) return false;
		var options = $.extend({}, this.optionsDefault, optionsCustom);
		if(this.runningRequest){
			if(options.delay) clearTimeout(this.delayCount);
		}else {
			this.runningRequest = true;
			if(options.Load) object.Ajax_Load(options);
		}
		if(options.delay) this.delayCount = setTimeout(function(){ Hectarea.Ajax_Go(object, options) }, options.delay);
		else this.Ajax_Go(object, options);
		
		return false;
	},
	Ajax_Go : function( object, options ){
		this.Request = $.ajax({
			type: options.type,
			url: options.url,
			data: options.data,
			success: function( response ){
				if(options.Response) object.Ajax_Response(response, options);
				HectaMVC.runningRequest = false;
			}
		});
	}
}

$(function(){
	HectaMVC.init();
});