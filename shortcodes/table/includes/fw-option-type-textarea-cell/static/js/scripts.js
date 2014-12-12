( function ($) {
	var init = function(){
		var $container = $(this);

		$container.find('.fw-textarea-tab.control textarea').on('change', function(){
			$container.find('.fw-textarea-tab.content').text($(this).val());
		});

		$container.find('.fw-textarea-tab.control textarea').on('blur', function(){
			$container.trigger('deactivate');
		});

		$container.on('deactivate', function(){
			$container.find('.fw-textarea-tab.control').addClass('closed');
			$container.find('.fw-textarea-tab.content').removeClass('closed');
		});

		$container.on('activate', function(){
			$container.find('.fw-textarea-tab.control').removeClass('closed');
			$container.find('.fw-textarea-tab.content').addClass('closed');
			$container.find('.fw-textarea-tab.control textarea').focus();
		});

	};

	fwEvents.on('fw:options:init', function (data) {
		data.$elements.find('.fw-option-type-textarea-cell:not(.fw-option-initialized)').each(init).addClass('fw-option-initialized');
	});
})(jQuery);