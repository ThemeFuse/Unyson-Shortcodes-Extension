<?php if (!defined('FW')) die('Forbidden');

class FW_Extension_Shortcodes extends FW_Extension
{
	/**
	 * @var FW_Shortcode[]
	 */
	private $shortcodes;

	/**
	 * @var FW_Ext_Shortcodes_Attr_Coder[]
	 */
	private $coders = array();

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
		add_action('fw_extensions_init', array($this, '_action_fw_extensions_init'));
		add_action('init', array($this, '_action_init'),
			11 // register shortcodes later than other plugins (there were some problems with the `column` shortcode)
		);

		/**
		 * We need aggressive only for wp-editor, at least for now.
		 * https://github.com/ThemeFuse/Unyson/issues/1807#issuecomment-235243578
		 */
		add_action(
			'wp_enqueue_editor',
			array($this, '_action_editor_shortcodes')
		);

		// renders the shortcodes so that css will get in <head>
		add_action(
			'wp_enqueue_scripts',
			array($this, '_action_enqueue_shortcodes_static_in_frontend_head'),
			/**
			 * Enqueue later than theme styles
			 * https://github.com/ThemeFuse/Theme-Includes/blob/b1467714c8a3125f077f1251f01ba6d6ca38640f/init.php#L41
			 * to be able to wp_add_inline_style('theme-style-handle', ...) in 'fw_ext_shortcodes_enqueue_static:{name}' action
			 * http://manual.unyson.io/en/latest/extension/shortcodes/index.html#enqueue-shortcode-dynamic-css-in-page-head
			 * in case the shortcode doesn't have a style, needed in step 3.
			 */
			30
		);
	}

	/**
	 * @internal
	 */
	public function _action_fw_extensions_init()
	{
		$this->load_shortcodes();
	}

	public function _action_editor_shortcodes()
	{
		wp_enqueue_script('fw-ext-shortcodes-editor-integration');
	}

	public function _action_init() {
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
		do_action('fw:ext:shortcodes:enqueue_custom_content');

		/** @var WP_Post $post */
		global $post;

		if (!$post) {
			return;
		}

		$this->enqueue_shortcodes_static($post->post_content);
	}

	/**
	 * @see fw_ext_shortcodes_enqueue_shortcodes_static()
	 * @param string $content
	 */
	public function enqueue_shortcodes_static( $content ) {
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

			/** @var WP_Post $post */
			global $post;

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
				'post' => $post,
			));

			$this->enqueue_shortcodes_static($shortcode[5]); // inner shortcodes

			/**
			 * @since 1.3.18
			 */
			do_action(
				'fw_ext_shortcodes:after_shortcode_enqueue_static',
				$shortcode
			);
		}
	}

	/**
	 * @param string $coder_id
	 * @return null|FW_Ext_Shortcodes_Attr_Coder|FW_Ext_Shortcodes_Attr_Coder[]
	 */
	public function get_attr_coder($coder_id = null)
	{
		if (empty($this->coders)) {
			if (!class_exists('FW_Ext_Shortcodes_Attr_Coder')) {
				require_once dirname(__FILE__) . '/includes/coder/interface-fw-ext-shortcodes-attr-coder.php';
			}

			if (!class_exists('FW_Ext_Shortcodes_Attr_Coder_JSON')) {
				require_once dirname(__FILE__) . '/includes/coder/class-fw-ext-shortcodes-attr-coder-json.php';
			}

			if (!class_exists('FW_Ext_Shortcodes_Attr_Coder_Aggressive')) {
				require_once dirname(__FILE__) . '/includes/coder/class-fw-ext-shortcodes-attr-coder-aggressive.php';
			}

			$coder_json = new FW_Ext_Shortcodes_Attr_Coder_JSON();
			$this->coders[ $coder_json->get_id() ] = $coder_json;

			$coder_aggressive = new FW_Ext_Shortcodes_Attr_Coder_Aggressive();
			$this->coders[ $coder_aggressive->get_id() ] = $coder_aggressive;

			if (!class_exists('FW_Ext_Shortcodes_Attr_Coder_Post_Meta')) {
				require_once dirname(__FILE__) . '/includes/coder/class-fw-ext-shortcodes-attr-coder-post-meta.php';
			}
			$coder_post_meta = new FW_Ext_Shortcodes_Attr_Coder_Post_Meta();
			$this->coders[ $coder_post_meta->get_id() ] = $coder_post_meta;

			foreach (apply_filters('fw_ext_shortcodes_coders', array()) as $coder) {
				if (!($coder instanceof FW_Ext_Shortcodes_Attr_Coder)) {
					trigger_error(get_class($coder) .' must implement FW_Ext_Shortcodes_Attr_Coder', E_USER_WARNING);
					continue;
				}

				if (isset($this->coders[ $coder->get_id() ])) {
					trigger_error('Coder id='. $coder->get_id() .' is already defined', E_USER_WARNING);
					continue;
				}

				$this->coders[ $coder->get_id() ] = $coder;
			}
		}

		if (is_null($coder_id)) {
			return $this->coders;
		} else {
			if (isset($this->coders[$coder_id])) {
				return $this->coders[$coder_id];
			} else {
				return null;
			}
		}
	}
}
