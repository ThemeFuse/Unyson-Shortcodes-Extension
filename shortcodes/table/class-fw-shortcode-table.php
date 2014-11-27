<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Table extends FW_Shortcode {
	/**
	 * @internal
	 */
	public function _init() {
		if ( is_admin() ) {
			$this->load_option_type();
		}
	}

	private function load_option_type() {
		require $this->locate_path('/includes/fw-option-type-table-builder/class-fw-option-type-table-builder.php');
	}

	protected function _render($atts, $content = null, $tag = '') {

		$this->enqueue_button_shortcode_static();
		$view_file = $this->locate_path('/views/' . $atts['table_purpose'] . '.php');
		if (!$view_file) {
			trigger_error(
				sprintf(__('No default view (views/view.php) found for shortcode: %s', 'fw'), $tag),
				E_USER_ERROR
			);
		}

		$this->enqueue_static();
		return fw_render_view( $view_file, array(
			'atts'    => $atts,
			'content' => $content,
			'tag'     => $tag
		) );
	}

	private function enqueue_button_shortcode_static(){
		$button = fw_ext( 'shortcodes' )->get_shortcode( 'button' );
		$static_file = $button->locate_path('/static.php');
		if ($static_file) {
			require_once $static_file;
		}
	}

}