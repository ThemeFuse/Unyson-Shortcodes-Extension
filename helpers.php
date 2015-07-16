<?php if (!defined('FW')) die('Forbidden');

/**
 * @param array $attributes Encoded attributes
 * @param $shortcode_tag 'button', 'section', etc.
 * @param $post_id
 * @return array|WP_Error
 * @since 1.3.0
 */
function fw_ext_shortcodes_decode_attr(array $attributes, $shortcode_tag, $post_id) {
	/**
	 * @var FW_Extension_Shortcodes $shortcodes_ext
	 */
	$shortcodes_ext = fw_ext('shortcodes');

	foreach ($shortcodes_ext->get_attr_coder() as $coder) {
		if ($coder->can_decode($attributes, $shortcode_tag, $post_id)) {
			return $coder->decode($attributes, $shortcode_tag, $post_id);
		}
	}

	return $attributes;
}
