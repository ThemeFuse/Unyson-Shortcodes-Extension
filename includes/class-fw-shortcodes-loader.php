<?php if (!defined('FW')) die('Forbidden');

/**
 * @internal
 */
class _FW_Shortcodes_Loader
{
	/** @var FW_Shortcode[] $shortcodes */
	private static $shortcodes = array();

	private static $disabled_shortcodes = array();

	private static $extension_shortcodes = array();

	public static function load($data)
	{
		if (
			isset($data['disabled_shortcodes']) &&
			is_array($data['disabled_shortcodes'])
		) {
			self::$disabled_shortcodes = array_fill_keys($data['disabled_shortcodes'], true);
		}

		self::load_core_shortcodes();
		self::load_extensions_shortcodes();
		return self::$shortcodes;
	}

	// Core shortcodes are those located in the shortcodes extension
	private static function load_core_shortcodes()
	{
		$shortcode_extension = fw_ext('shortcodes');
		self::load_extension_shortcodes($shortcode_extension);
	}

	private static function load_extensions_shortcodes()
	{
		foreach (fw()->extensions->get_all() as $extension) {
			if ($extension->get_name() !== 'shortcodes') {
				self::load_extension_shortcodes($extension);
			}
		}
	}

	/**
	 * @param FW_Extension $extension
	 */
	private static function load_extension_shortcodes($extension)
	{
		$ext_name = $extension->get_name();

		if (version_compare(fw()->manifest->get_version(), '2.2', '<')) {
			$ext_rel_path = $extension->get_rel_path();

			if ($extension->get_declared_source() === 'framework') {
				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => $extension->get_declared_path('/shortcodes'),
						'uri'  => $extension->get_declared_URI('/shortcodes')
					),
					array(
						'paths' => array(
							fw_get_stylesheet_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes'),
							fw_get_template_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes')
						),
						'uris' => array(
							fw_get_stylesheet_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes'),
							fw_get_template_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes')
						)
					)
				);
				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => fw_get_template_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes'),
						'uri'  => fw_get_template_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes')
					),
					array(
						'paths' => array(
							fw_get_stylesheet_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes')
						),
						'uris'  => array(
							fw_get_stylesheet_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes')
						)
					)
				);
				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => fw_get_stylesheet_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes'),
						'uri'  => fw_get_stylesheet_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes')
					)
				);
			} elseif ($extension->get_declared_source() === 'parent') {
				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => $extension->get_declared_path('/shortcodes'),
						'uri'  => $extension->get_declared_URI('/shortcodes')
					),
					array(
						'paths' => array(
							fw_get_stylesheet_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes')
						),
						'uris'  => array(
							fw_get_stylesheet_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes')
						)
					)
				);
				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => fw_get_stylesheet_customizations_directory('/extensions' . $ext_rel_path . '/shortcodes'),
						'uri'  => fw_get_stylesheet_customizations_directory_uri('/extensions' . $ext_rel_path . '/shortcodes')
					)
				);
			} else {
				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => $extension->get_declared_path('/shortcodes'),
						'uri'  => $extension->get_declared_URI('/shortcodes')
					)
				);
			}
		} else {
			$customizations_locations = array(
				'paths' => array(),
				'uris' => array(),
			);

			foreach ($extension->get_customizations_locations() as $path => $uri) {
				$customizations_locations['paths'][] = $path. '/shortcodes';
				$customizations_locations['uris'][] = $uri. '/shortcodes';
			}

			$path = $extension->get_path('/shortcodes');
			$uri = $extension->get_uri('/shortcodes');

			do {
				if (empty($customizations_locations['paths'])) {
					$customizations_locations = array();
				}

				self::load_folder_shortcodes(
					$ext_name,
					array(
						'path' => $path,
						'uri' => $uri,
					),
					$customizations_locations
				);

				if ($customizations_locations) {
					$path = array_pop($customizations_locations['paths']);
					$uri = array_pop($customizations_locations['uris']);
				}
			} while($customizations_locations);
		}
	}

	private static function load_folder_shortcodes($ext_name, $paths, $rewrites = array())
	{
		// if no shortcode folder don't do any work
		if (!file_exists($paths['path'])) {
			return;
		}

		$dirs = glob($paths['path'] .'/*', GLOB_ONLYDIR);

		if (empty($dirs)) {
			return;
		}

		// clean rewrite paths because it may contain nulls
		if (isset($rewrites['paths'])) {
			$cleared_rewrite_paths = array();
			$cleared_rewrite_uris  = array();
			foreach ($rewrites['paths'] as $key => $rewrite_path) {
				if ($rewrite_path && file_exists($rewrite_path)) {
					$cleared_rewrite_paths[] = $rewrites['paths'][$key];
					$cleared_rewrite_uris[]  = $rewrites['uris'][$key];
				}
			}
		}

		foreach ($dirs as $shortcode_path) {
			$shortcode_dir = strtolower(basename($shortcode_path));
			$shortcode_tag = str_replace('-', '_', $shortcode_dir);

			if (isset(self::$disabled_shortcodes[$shortcode_tag])) {
				continue;
			}

			if (isset(self::$extension_shortcodes[$ext_name][$shortcode_tag])) {
				continue;
			}

			if (isset(self::$shortcodes[$shortcode_tag])) {
				trigger_error(
					sprintf(
						__('Shortcode "%s" from %s was already defined at %s', 'fw'),
						$shortcode_tag,
						$paths['path'] . '/' . $shortcode_dir,
						self::$shortcodes[$shortcode_tag]->get_declared_path()
					),
					E_USER_WARNING
				);
				continue;
			}

			// shortcode path & uri
			$shortcode_data = array(
				'tag'  => $shortcode_tag,
				'path' => $paths['path'] . '/' . $shortcode_dir,
				'uri'  => $paths['uri']  . '/' . $shortcode_dir
			);

			// shortcode rewrite paths
			if (isset($cleared_rewrite_paths)) {
				$shortcode_rewrite_paths = $cleared_rewrite_paths;
				$shortcode_rewrite_uris  = $cleared_rewrite_uris;
				foreach ($shortcode_rewrite_paths as $key => $rewrite_path) {
					$shortcode_rewrite_paths[$key] .= '/' . $shortcode_dir;
					$shortcode_rewrite_uris[$key]  .= '/' . $shortcode_dir;
				}
				$shortcode_data['rewrite_paths'] = $shortcode_rewrite_paths;
				$shortcode_data['rewrite_uris']  = $shortcode_rewrite_uris;
			}

			self::$extension_shortcodes[$ext_name][$shortcode_tag] = true;
			self::$shortcodes[$shortcode_tag] = self::load_shortcode($shortcode_data);
		}
	}

	private static function load_shortcode( $data ) {
		$tag        = $data['tag'];
		$path       = $data['path'];
		$uri        = $data['uri'];
		$dir_name   = strtolower( basename( $path ) );
		$class_file = "{$path}/class-fw-shortcode-{$dir_name}.php";

		$args = array(
			'tag'           => $tag,
			'path'          => $path,
			'uri'           => $uri,
			'rewrite_paths' => ! empty( $data['rewrite_paths'] ) ? $data['rewrite_paths'] : array(),
			'rewrite_uris'  => ! empty( $data['rewrite_uris'] ) ? $data['rewrite_uris'] : array()
		);
		$custom_class_found = false;

		// try to find a custom class for the shortcode
		if ( file_exists( $class_file ) ) {
			require $class_file;

			$class_name = explode( '_', $tag );
			$class_name = array_map( 'ucfirst', $class_name );
			$class_name = 'FW_Shortcode_' . implode( '_', $class_name );

			if ( ! class_exists( $class_name ) ) {
				trigger_error(
					sprintf( __( 'Class file found for shortcode %s but no class %s found', 'fw' ), $tag, $class_name ),
					E_USER_WARNING
				);
			} elseif ( ! is_subclass_of( $class_name, 'FW_Shortcode' ) ) {
				trigger_error(
					sprintf( __( 'The class %s must extend from FW_Shortcode', 'fw' ), $class_name ),
					E_USER_WARNING
				);
			} else {
				$shortcode_instance = new $class_name( $args );
				$custom_class_found = true;
			}
		}

		// if no custom shortcode class found instantiate a default one
		if ( ! $custom_class_found ) {
			$shortcode_instance = new FW_Shortcode( $args );
		}

		return $shortcode_instance;
	}
}
