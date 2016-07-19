<?php if (!defined('FW')) die('Forbidden');

class FW_Ext_Shortcodes_Attr_Coder_JSON implements FW_Ext_Shortcodes_Attr_Coder {
	/**
	 * @return string
	 */
	public function get_id() {
		return 'json';
	}

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return array|WP_Error
	 */
	public function encode(array $attributes, $shortcode_tag, $post_id) {
		$encoded    = array();
		$array_keys = array(); // remember which keys contains json encoded arrays

		foreach ($attributes as $key => $value) {
			/**
			 * The WordPress shortcode parser doesn't work when using attributes with dashes.
			 */
			$key = str_replace('-', '_', $key);

			if (is_array($value)) {
				$value = json_encode($value);
				$array_keys[$key] = $key;
			}

			$encoded[$key] = $this->encode_value($value);
		}

		if (!empty($array_keys)) {
			$encoded['_array_keys'] = $this->encode_value(json_encode($array_keys));
		}

		$encoded['_made_with_builder'] = 'true';

		return $encoded;
	}

	private function encode_value($value) {
		/**
		 * Replace '[' and ']' to fix http://bit.ly/1HoHVhl
		 * Replace new lines to fix http://bit.ly/1J887Om
		 *
		 * http://www.degraeve.com/reference/specialcharacters.php
		 */
		return str_replace(
			array('[',     ']',     "\r\n",   '\\'),
			array('&#91;', '&#93;', '&#010;', '&#92;'),
			htmlentities($value, ENT_QUOTES, 'UTF-8')
		);
	}

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return array|WP_Error
	 */
	public function decode(array $attributes, $shortcode_tag, $post_id) {
		if (!$this->can_decode($attributes, $shortcode_tag, $post_id)) {
			return $attributes;
		}

		unset($attributes['_made_with_builder']);

		$array_keys = array();
		if (isset($attributes['_array_keys'])) {
			$array_keys = json_decode($this->decode_value($attributes['_array_keys']), true);
			unset($attributes['_array_keys']);
		}

		$decoded = array();
		foreach ($attributes as $key => $value) {
			$decoded[$key] = isset($array_keys[$key])
				? json_decode($this->decode_value($value), true)
				: $this->decode_value($value);
		}

		return $decoded;
	}

	public function decode_value($encoded_value) {
		return html_entity_decode($encoded_value, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * @param array $attributes
	 * @param string $shortcode_tag
	 * @param int $post_id
	 * @return bool
	 */
	public function can_decode(array $attributes, $shortcode_tag, $post_id) {
		return isset($attributes['_made_with_builder']); // https://github.com/ThemeFuse/Unyson/issues/469
	}
}
