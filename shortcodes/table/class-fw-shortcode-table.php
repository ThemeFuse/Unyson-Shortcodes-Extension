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
		require $this->locate_path( '/includes/fw-option-type-table/class-fw-option-type-table.php' );
		require $this->locate_path( '/includes/fw-option-type-textarea-cell/class-fw-option-type-textarea-cell.php' );
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

		$this->enqueue_static();

		return fw_render_view( $view_file, array(
			'atts'    => $atts,
			'content' => $content,
			'tag'     => $tag
		) );
	}

}