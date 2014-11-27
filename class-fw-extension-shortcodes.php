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

		/*
		 * load shortcodes when on frontend or
		 * if it was requested via ajax
		 */
		if (
			!is_admin() ||
			(
				defined('DOING_AJAX') &&
				DOING_AJAX === true   &&
				FW_Request::POST('fw_load_shortcodes')
			)
		) {
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
		$all_shortcodes        = _FW_Shortcodes_Loader::load();
		$shortcode_tags        = array_keys($all_shortcodes);
		$shortcodes_to_disable = apply_filters(
			'fw_ext_shortcodes_disable_shortcodes',
			array(),
			array_combine($shortcode_tags, $shortcode_tags)
		);

		foreach ($shortcodes_to_disable as $to_disable) {
			unset($all_shortcodes[$to_disable]);
		}
		$this->shortcodes = $all_shortcodes;
	}

	private function register_shortcodes()
	{
		foreach ($this->shortcodes as $tag => $instance) {
			add_shortcode($tag, array($instance, 'render'));
		}
	}
}
