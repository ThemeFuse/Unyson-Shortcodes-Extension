/**
 * Load info about Unyson Shortcodes on frontend.
 *
 * Usage:
 *
 * fwUnysonShortcodesLoadData()
 *   .then(function (response) {
 *     // actual data you want to use
 *     var shortcodes = response.data.shortcodes;
 *   }
 *
 *
 * @return jQuery.deferred
 */
window.fwUnysonShortcodesLoadData = (function ($) {
	var promise = null;

	return load;
	
	function load () {
		if (promise) {
			return promise;
		}

		/**
		 * Preload shortcodes data. Don't do wp_localize_script because it
		 * makes the HTML very big and it loads this data on every page.
		 * Load it via AJAX request better.
		 */
		promise = jQuery.post(ajaxurl, {
			action: 'fw_ext_wp_shortcodes_data'
		});

		return promise;
	}
})(jQuery);

/**
 * Get underlying data from fwUnysonShortcodesLoadData() promise.
 * Should be used only when you are 100% sure that promise from the first
 * function is already resolved. It will return null if promise is pending.
 *
 * @return object | null
 */
function fwUnysonShortcodesData () {
	var promise = fwUnysonShortcodesLoadData();
	var data = null;

	if (promise.state() === 'resolved') {
		if (promise.responseJSON.success) {
			return promise.responseJSON.data.shortcodes;
		}
	}

	return data;
}

