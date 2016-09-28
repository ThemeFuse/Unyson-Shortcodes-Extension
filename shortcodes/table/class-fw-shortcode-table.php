<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Table extends FW_Shortcode {
	/**
	 * @internal
	 */
	public function _init() {
		add_action('fw_option_types_init', array($this, '_action_load_option_type'));
		add_action('fw_ext_shortcodes_enqueue_static:table', array($this, '_action_enqueue_buttons'));
	}

	public function _action_load_option_type() {
		/**
		 * Important!
		 * We can't replace here locate_path() with hardcoded path
		 * because other theme developers already overwrote these in their themes
		 * (it was a bad initial decision to allow this overwrite)
		 */
		require_once $this->locate_path('/includes/fw-option-type-table/class-fw-option-type-table.php');
		require_once $this->locate_path('/includes/fw-option-type-textarea-cell/class-fw-option-type-textarea-cell.php');
	}

	/**
	 * Inside table shortcode can be added button shortcodes
	 * these shortcodes can have custom options
	 * and the action 'fw_ext_shortcodes_enqueue_static:button' must be executed for them
	 * Fixes https://github.com/ThemeFuse/Unyson/issues/1936
	 * @param array $data
	 */
	public function _action_enqueue_buttons ($data) {
		$attr = fw_ext_shortcodes_decode_attr(
			shortcode_parse_atts( $data['atts_string'] ),
			'table', 0
		);

		if (is_wp_error($attr)) {
			return;
		}

		if ($button = $this->get_button_shortcode()) {
			// Here is included `button/static.php` with `add_action('fw_ext_shortcodes_enqueue_static:button', ...`
			$button->_enqueue_static();
		} else {
			return;
		}

		/** @var FW_Extension_Shortcodes $shortcodes */
		$shortcodes = fw_ext('shortcodes');

		$coder = $shortcodes->get_attr_coder('json');

		foreach ($attr['table']['rows'] as $ri => $row) {
			if ('button-row' !== $row['name']) {
				continue;
			}

			foreach ($attr['table']['content'][$ri] as $row) {
				if (!isset($row['button'])) {
					return;
				}

				$atts_string = '';
				foreach (
					$coder->encode($row['button'], $this->get_button_shortcode_tag(), 0)
					as $attr_name => $attr_val
				) {
					$atts_string .= $attr_name .'="'. $attr_val .'" ';
				}

				/**
				 * Must be exactly the same as
				 * https://github.com/ThemeFuse/Unyson-Shortcodes-Extension/blob/v1.3.19/class-fw-extension-shortcodes.php#L226-L237
				 */
				do_action('fw_ext_shortcodes_enqueue_static:button', array(
					'atts_string' => $atts_string,
					'post' => $data['post'],
				));
			}
		}
	}

	protected function _render( $atts, $content = null, $tag = '' ) {
		if (
			! isset( $atts['table'] )
			|| ! isset( $atts['table']['header_options'] )
			|| ! isset( $atts['table']['header_options']['table_purpose'] )
		) {
			return '';
		}

		$view_file = $this->locate_path( '/views/' . $atts['table']['header_options']['table_purpose'] . '.php' );

		if ( ! $view_file ) {
			$view_file = $this->get_declared_path( '/views/tabular.php' );
		}

		return fw_render_view( $view_file, array(
			'atts'    => $atts,
			'content' => $content,
			'tag'     => $tag
		) );
	}

	/**
	 * @return string
	 * @since 1.3.22
	 */
	public function get_button_shortcode_tag() {
		try {
			return FW_Cache::get($cache_key = 'fw:ext:shortcodes:table:button-shortcode-name');
		} catch (FW_Cache_Not_Found_Exception $e) {
			FW_Cache::set(
				$cache_key,
				/**
				 * If you disable default shortcode 'button' and create your own shortcode use this filter to specify its name.
				 * Fixes https://github.com/ThemeFuse/Unyson/issues/2056
				 */
				$shortcode_name = apply_filters('fw:ext:shortcodes:table:button-shortcode-name', 'button')
			);

			return $shortcode_name;
		}
	}

	/**
	 * @return FW_Shortcode|null
	 * @since 1.3.22
	 */
	public function get_button_shortcode() {
		/** @var FW_Extension_Shortcodes $shortcodes */
		$shortcodes = fw_ext('shortcodes');

		return $shortcodes->get_shortcode($this->get_button_shortcode_tag());
	}
}