<?php if (!defined('FW')) die('Forbidden');

class FW_Extension_Shortcodes extends FW_Extension
{
	/** @var  FW_Shortcode[] $shortcodes */
	private $shortcodes;

	/**
	 * Gets a certain shortcode by a given tag
	 *
	 * @param string $tag The shortcode tag
	 * @return FW_Shortcode|null
	 */
	public function get_shortcode($tag)
	{
		$this->load_shortcodes();
		return isset($this->shortcodes[$tag]) ? $this->shortcodes[$tag] : null;
	}

	/**
	 * Gets all shortcodes
	 *
	 * @return FW_Shortcode[]
	 */
	public function get_shortcodes()
	{
		$this->load_shortcodes();
		return $this->shortcodes;
	}

	/**
	 * @internal
	 */
	protected function _init()
	{
		if (!is_admin()) {

			// loads the shortcodes
			add_action('fw_extensions_init', array($this, '_action_fw_extensions_init'));

			// renders the shortcodes so that css will get in <head>
			add_action('wp_enqueue_scripts', array($this, '_action_enqueue_shortcodes_static_in_frontend_head'));
		} elseif (
			defined('DOING_AJAX') &&
			DOING_AJAX === true   &&
			FW_Request::POST('fw_load_shortcodes')
		) {

			// load the shortcodes if this was requested via ajax
			add_action('fw_extensions_init', array($this, '_action_fw_extensions_init'));
		}
	}

	/**
	 * @internal
	 */
	public function _action_fw_extensions_init()
	{
		$this->load_shortcodes();
		$this->register_shortcodes();
	}

	public function load_shortcodes()
	{
		static $is_loading = false; // prevent recursion

		if ($is_loading) {
			trigger_error('Recursive shortcodes load', E_USER_WARNING);
			return;
		}

		if ($this->shortcodes) {
			return;
		}

		$is_loading = true;

		$disabled_shortcodes = apply_filters('fw_ext_shortcodes_disable_shortcodes', array());
		$this->shortcodes    = _FW_Shortcodes_Loader::load(array(
			'disabled_shortcodes' => $disabled_shortcodes
		));

		$is_loading = false;
	}

	private function register_shortcodes()
	{
		foreach ($this->shortcodes as $tag => $instance) {
			add_shortcode($tag, array($instance, 'render'));
		}
	}

	/**
	 * Make sure to enqueue shortcodes static in <head> (not in <body>)
	 * @internal
	 */
	public function _action_enqueue_shortcodes_static_in_frontend_head()
	{
		/** @var WP_Post $post */
		global $post;

		if (!$post) {
			return;
		}

		$this->enqueue_shortcodes_static($post->post_content);
	}

	private function enqueue_shortcodes_static( $content ) {
		preg_replace_callback( '/'. get_shortcode_regex() .'/s', array( $this, 'enqueue_shortcode_static'), $content );
	}

	private function enqueue_shortcode_static( $shortcode ) {
		/**
		 * Remember the enqueued shortcodes and prevent enqueue static multiple times.
		 * There is no sense to call enqueue_static() multiple times
		 * because there is no dynamic data passed to it.
		 */
		static $enqueued_shortcodes = array();

		// allow [[foo]] syntax for escaping a tag
		if ( $shortcode[1] == '[' && $shortcode[6] == ']' ) {
			return;
		}

		$tag = $shortcode[2];

		if ( ! is_null( $this->get_shortcode( $tag ) ) ) {
			if (!isset($enqueued_shortcodes[$tag])) {
				$this->get_shortcode($tag)->_enqueue_static();
				$enqueued_shortcodes[$tag] = true;
			}

			do_action('fw_ext_shortcodes_enqueue_static:'. $tag, array(
				/**
				 * Transform to array:
				 * $attr = shortcode_parse_atts( $data['atts_string'] );
				 *
				 * By default it's not transformed, but sent as raw string,
				 * to prevent useless computation for every shortcode,
				 * because this action may be used very rare and only for a specific shortcode.
				 */
				'atts_string' => $shortcode[3],
			));

			$this->enqueue_shortcodes_static($shortcode[5]); // inner shortcodes
		}
	}
}
