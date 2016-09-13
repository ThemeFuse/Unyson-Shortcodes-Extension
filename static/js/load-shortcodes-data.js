/**
 * Load info about Unyson Shortcodes on frontend.
 *
 * Usage:
 *
 * fw.shortcodesLoadData()
 *   .then(function (response) {
 *     // actual data you want to use
 *     var shortcodes = response.data.shortcodes;
 *   }
 *
 * @return jQuery.deferred
 * @since 1.3.19
 */
fw.shortcodesLoadData = (function ($) {
	var promise = null;

	return load;

	function load (forceRewrite) {
		if (forceRewrite) {
		    promise = null;
		}

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
 * Get underlying data from fw.shortcodesLoadData() promise.
 * Should be used only when you are 100% sure that promise from the first
 * function is already resolved. It will return null if promise is pending.
 *
 * @return object | null
 * @since 1.3.19
 */
fw.unysonShortcodesData = function fwUnysonShortcodesData (forceRewrite) {
	var promise = fw.shortcodesLoadData(forceRewrite);
	var data = null;

	if (promise.state() === 'resolved') {
		if (promise.responseJSON.success) {
			return promise.responseJSON.data;
		}
	}

	return data;
}

