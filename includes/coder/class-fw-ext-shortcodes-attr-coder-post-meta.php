<?php if (!defined('FW')) die('Forbidden');

class FW_Ext_Shortcodes_Attr_Coder_Post_Meta implements FW_Ext_Shortcodes_Attr_Coder {
	private $meta_key = 'fw-shortcode-settings';
	private $meta_key_defaults = 'fw-shortcode-default-values';

	/**
	 * @return string
	 */
	public function get_id() {
		return 'post_meta';
	}

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return array|WP_Error
	 */
	public function encode(array $attributes, $shortcode_tag, $post_id) {
		/**
		 * Has no portable encode functionality,
		 * because it is depended on some $_POST data
		 * and it is used when shortcodes are mixed with content (edit-shortcodes extension)
		 * @see FW_Extension_Editor_Shortcodes::_action_admin_save_shortcodes
		 */
		return new WP_Error('not_portable', 'Post Meta encode is not portable');
	}

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return array|WP_Error
	 */
	public function decode(array $attributes, $shortcode_tag, $post_id) {
		if ( ! $this->can_decode($attributes, $shortcode_tag, $post_id) ) {
			return new WP_Error('cannot_decode', 'Cannot decode');
		}

		$option_values  = json_decode( get_post_meta( $post_id, $this->meta_key, true ), true );
		$default_values = json_decode( get_post_meta( $post_id, $this->meta_key_defaults, true ), true );

		$id = $attributes['fw_shortcode_id'];
		$attributes = $default_values[ $shortcode_tag ];

		if ( is_array( $option_values ) and false === empty( $option_values ) ) {
			if ( preg_match( '/^[A-Za-z0-9]+$/', $id ) ) {
				if ( isset( $option_values[ $shortcode_tag ][ $id ] ) ) {
					$attributes = $option_values[ $shortcode_tag ][ $id ];
				}
			}
		}

		if (is_array($attributes)) {
			$fixed_atts = array();

			foreach ( $attributes as $key => $value ) {
				/**
				 * The WordPress shortcode parser doesn't work when using attributes with dashes.
				 * (same as in json coder)
				 */
				$fixed_atts[ str_replace( '-', '_', $key ) ] = $value;
			}

			return $fixed_atts;
		} else {
			return $attributes;
		}
	}

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return bool
	 */
	public function can_decode(array $attributes, $shortcode_tag, $post_id) {
		return isset($attributes['fw_shortcode_id']); // https://github.com/ThemeFuse/Unyson/issues/469
	}
}
