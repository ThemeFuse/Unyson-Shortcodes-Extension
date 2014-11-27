<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');

wp_enqueue_style(
	'fw-shortcode-calendar-bootstrap3',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/libs/bootstrap3/css/bootstrap-grid.css')
);
wp_enqueue_style(
	'fw-shortcode-calendar-calendar',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/css/calendar.css')
);
wp_enqueue_style(
	'fw-shortcode-calendar',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/css/styles.css')
);


wp_enqueue_script(
	'fw-shortcode-calendar-bootstrap3',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/libs/bootstrap3/js/bootstrap.min.js'),
	array('jquery', 'underscore'),
	fw()->manifest->get_version(),
	true
);
wp_enqueue_script(
	'fw-shortcode-calendar-timezone',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/libs/jstimezonedetect/jstz.min.js'),
	array('jquery', 'underscore'),
	fw()->manifest->get_version(),
	true
);
wp_enqueue_script(
	'fw-shortcode-calendar-calendar',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/js/calendar.js'),
	array('jquery', 'underscore', 'fw-shortcode-calendar-bootstrap3', 'fw-shortcode-calendar-timezone'),
	fw()->manifest->get_version(),
	true
);
wp_enqueue_script(
	'fw-shortcode-calendar',
	$shortcodes_extension->get_declared_URI('/shortcodes/calendar/static/js/scripts.js'),
	array('jquery', 'underscore', 'fw-shortcode-calendar-calendar'),
	fw()->manifest->get_version(),
	true
);

$locale = get_locale();
wp_localize_script(
	'fw-shortcode-calendar',
	'fwShortcodeCalendarLocalize',
	array(
		'event'  => __('Event', 'fw'),
		'events' => __('Events', 'fw'),
		'today'  => __('Today', 'fw'),
		'locale' => $locale
	)
);
wp_localize_script(
	'fw-shortcode-calendar',
	'calendar_languages',
	array($locale => array(
		'error_noview' => __('Calendar: View {0} not found', 'fw'),
		'error_dateformat' => __('Calendar: Wrong date format {0}. Should be either "now" or "yyyy-mm-dd"', 'fw'),
		'error_loadurl' => __('Calendar: Event URL is not set', 'fw'),
		'error_where' => __('Calendar: Wrong navigation direction {0}. Can be only "next" or "prev" or "today"', 'fw'),
		'error_timedevide' => __('Calendar: Time split parameter should divide 60 without decimals. Something like 10, 15, 30', 'fw'),
		'no_events_in_day' => __('No events in this day.', 'fw'),
		'title_year' => __('{0}', 'fw'),
		'title_month' => __('{0} {1}', 'fw'),
		'title_week' => __('week {0} of {1}', 'fw'),
		'title_day' => __('{0} {1} {2}, {3}', 'fw'),
		'week' => __('Week {0}', 'fw'),
		'all_day' => __('All day', 'fw'),
		'time' => __('Time', 'fw'),
		'events' => __('Events', 'fw'),
		'before_time' => __('Ends before timeline', 'fw'),
		'after_time' => __('Starts after timeline', 'fw'),
		'm0' => __('January', 'fw'),
		'm1' => __('February', 'fw'),
		'm2' => __('March', 'fw'),
		'm3' => __('April', 'fw'),
		'm4' => __('May', 'fw'),
		'm5' => __('June', 'fw'),
		'm6' => __('July', 'fw'),
		'm7' => __('August', 'fw'),
		'm8' => __('September', 'fw'),
		'm9' => __('October', 'fw'),
		'm10' => __('November', 'fw'),
		'm11' => __('December', 'fw'),
		'ms0' => __('Jan', 'fw'),
		'ms1' => __('Feb', 'fw'),
		'ms2' => __('Mar', 'fw'),
		'ms3' => __('Apr', 'fw'),
		'ms4' => __('May', 'fw'),
		'ms5' => __('Jun', 'fw'),
		'ms6' => __('Jul', 'fw'),
		'ms7' => __('Aug', 'fw'),
		'ms8' => __('Sep', 'fw'),
		'ms9' => __('Oct', 'fw'),
		'ms10' => __('Nov', 'fw'),
		'ms11' => __('Dec', 'fw'),
		'd0' => __('Sunday', 'fw'),
		'd1' => __('Monday', 'fw'),
		'd2' => __('Tuesday', 'fw'),
		'd3' => __('Wednesday', 'fw'),
		'd4' => __('Thursday', 'fw'),
		'd5' => __('Friday', 'fw'),
		'd6' => __('Saturday', 'fw'),
	))
);
