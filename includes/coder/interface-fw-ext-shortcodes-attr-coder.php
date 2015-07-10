<?php if (!defined('FW')) die('Forbidden');

interface FW_Ext_Shortcodes_Attr_Coder {
	/**
	 * @return string
	 */
	public function get_id();

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return array|WP_Error
	 */
	public function encode(array $attributes, $shortcode_tag, $post_id);

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return array|WP_Error
	 */
	public function decode(array $attributes, $shortcode_tag, $post_id);

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return bool
	 */
	public function can_decode(array $attributes, $shortcode_tag, $post_id);
}
