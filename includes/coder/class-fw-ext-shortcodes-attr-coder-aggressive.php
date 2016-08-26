<?php

if (! defined('FW')) { die('Forbidden'); }

class FW_Ext_Shortcodes_Attr_Coder_Aggressive implements FW_Ext_Shortcodes_Attr_Coder {
	public function get_id() {
		return 'aggressive';
	}

	private $symbol_table = array();

	public function __construct() {
		$before = '‹';
		$after  = '›';

		// Extended ASCII codes http://www.ascii-code.com/
		foreach (array(
			/**
			 * Fixes https://github.com/ThemeFuse/Unyson-WP-Shortcodes-Extension/issues/2
			 * Important! The order matters, it must be or first or last, and exactly the same must be in js coder
			 */
			$before  => 'ˆ',
			'['  => 'º',
			']'  => '¹',
			'"'  => '²',
			"'"  => '³',
			'&'  => '¯',
			'='  => '´',
			'\\' => 'ª',
			'<'  => '¨',
			'>'  => '˜',
		) as $original => $encoded) {
			$this->symbol_table[$original] = $before . $encoded . $after;
		}
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

		if (! empty($array_keys)) {
			$encoded['_array_keys'] = $this->encode_value(json_encode($array_keys));
		}

		$encoded['_fw_coder'] = 'aggressive';

		return $encoded;
	}

	private function encode_value($value) {
		return str_replace(
			array_keys($this->symbol_table),
			array_values($this->symbol_table),
			$value
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


		unset($attributes['_fw_coder']);

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

	public function decode_value($value) {
		return str_replace(
			array_reverse(array_values($this->symbol_table)),
			array_reverse(array_keys($this->symbol_table)),
			$value
		);
	}

	public function can_decode(array $attributes, $shortcode_tag, $post_id) {
		return isset($attributes['_fw_coder']) && $attributes['_fw_coder'] == 'aggressive';
	}
}

