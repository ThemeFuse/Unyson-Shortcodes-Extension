<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

wp_enqueue_style( 'fw-ext-builder-frontend-grid' );

$shortcodes_extension = fw_ext( 'shortcodes' );

if ( version_compare( $shortcodes_extension->manifest->get_version(), '1.3.9', '>=' ) ) {
	/**
	 * Updated to new version of formstone.js background
	 * which have new structure and new dependencies
	 * such as core.js , transition.js and background.js
	 * since v1.3.9
	 * jquery.fs.wallpaper.js, jquery.fs.wallpaper.min.js and scripts.js are @deprecated
	 * they remains for backward compatibility.
	 */

	// fixes https://github.com/ThemeFuse/Unyson/issues/1552
	{
		global $is_safari;

		if ($is_safari) {
			wp_enqueue_script('youtube-iframe-api', 'https://www.youtube.com/iframe_api');
		}
	}

	wp_enqueue_style(
		'fw-shortcode-section-background-video',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/css/background.css' )
	);

	wp_enqueue_script(
		'fw-shortcode-section-formstone-core',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/js/core.js' ),
		array( 'jquery' ),
		false,
		true
	);
	wp_enqueue_script(
		'fw-shortcode-section-formstone-transition',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/js/transition.js' ),
		array( 'jquery' ),
		false,
		true
	);
	wp_enqueue_script(
		'fw-shortcode-section-formstone-background',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/js/background.js' ),
		array( 'jquery' ),
		false,
		true
	);
	wp_enqueue_script(
		'fw-shortcode-section',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/js/background.init.js' ),
		array(
			'fw-shortcode-section-formstone-core',
			'fw-shortcode-section-formstone-transition',
			'fw-shortcode-section-formstone-background'
		),
		false,
		true
	);
} else {
	wp_enqueue_style(
		'fw-shortcode-section-background-video',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/css/jquery.fs.wallpaper.css' )
	);

	wp_enqueue_script(
		'fw-shortcode-section-background-video',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/js/jquery.fs.wallpaper.min.js' ),
		array( 'jquery' ),
		false,
		true
	);
	wp_enqueue_script(
		'fw-shortcode-section',
		$shortcodes_extension->get_uri( '/shortcodes/section/static/js/scripts.js' ),
		array( 'fw-shortcode-section-background-video' ),
		false,
		true
	);
}

wp_enqueue_style(
	'fw-shortcode-section',
	$shortcodes_extension->get_uri( '/shortcodes/section/static/css/styles.css' )
);

