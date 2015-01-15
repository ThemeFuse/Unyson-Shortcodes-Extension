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
		if ($this->shortcodes) {
			return;
		}
		$disabled_shortcodes = apply_filters('fw_ext_shortcodes_disable_shortcodes', array());
		$this->shortcodes    = _FW_Shortcodes_Loader::load(array(
			'disabled_shortcodes' => $disabled_shortcodes
		));
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

		do_shortcode($post->post_content);
	}
}
