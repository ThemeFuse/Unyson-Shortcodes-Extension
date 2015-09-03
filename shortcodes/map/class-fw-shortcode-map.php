<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Map extends FW_Shortcode {

	/**
	 *  @var $data = array(
	 *          'unique_id' => array(                               // some unique id (string)  required
	 *              'callback' => array($this, 'callable_method')  // array(stdClass, 'some_public_method') required
	 *              'label' => 'label',                            // data provider label (string) required
	 *              'options' => array()                           // extra options (array of options) optional
	 *          )
	 *       )
	 */
	private $data = array();

	private function load_data()
	{
		if (empty($this->data)) {
			$this->data = apply_filters('fw_shortcode_map_provider', array(
				'custom' => array(
					'callback'   => array($this, '_callback_get_custom_locations'),
					'label'      => __('Custom','fw'),
					'options'    => array(
						'locations' => array(
							'label' => __('Locations', 'fw'),
							'popup-title' => __('Add/Edit Location', 'fw'),
							'type' => 'addable-popup',
							'desc' => false,
							'template' => '{{  if (location.location !== "") {  print(location.location)} else { print("' . __('Note: Please set location', 'fw') . '")} }}',
							'popup-options' => array(
								'location' => array(
									'type' => 'map',
									'label' =>__('Location','fw'),
								),
								'title' => array(
									'type' => 'text',
									'label' => __('Location Title', 'fw'),
									'desc' => __('Set location title', 'fw'),
								),
								'description' => array(
									'type'  => 'textarea',
									'label' => __('Location Description', 'fw'),
									'desc'  => __('Set location description', 'fw')
								),
								'url' => array(
									'type'  => 'text',
									'label' => __('Location Url', 'fw'),
									'desc'  => __('Set page url (Ex: http://example.com)', 'fw'),
								),
								'thumb' => array(
									'label'       => __('Location Image', 'fw'),
									'desc'        => __('Add location image', 'fw'),
									'type'        => 'upload',
								)
							)
						)
					)
				)
			));
		}
	}

	public function _callback_get_custom_locations($atts) {
		$rows = fw_akg('data_provider/custom/locations', $atts, array());

		$result = array();
		if (!empty($rows)) {
			foreach($rows as $key => $row) {
				$result[$key]['title']       = fw_akg('title', $row);
				$result[$key]['url']         = fw_akg('url', $row);
				$result[$key]['thumb']       = fw_resize(wp_get_attachment_url(fw_akg('thumb/attachment_id', $row)), 100, 60, true);
				$result[$key]['coordinates'] = fw_akg('location/coordinates', $row);
				$result[$key]['description'] = fw_akg('description', $row);
			}
		}

		return $result;
	}

	/**
	 * Get the list of providers
	 * @internal
	 */
	public function _get_picker_dropdown_choices() {
		$this->load_data();
		$result = array();
		foreach($this->data as $unique_key => $item ) {
			$result[$unique_key] = $item['label'];
		}
		return $result;
	}

	/**
	 * Get the providers' options
	 * @internal
	 */
	public function _get_picker_choices() {
		$this->load_data();
		$result = array();
		foreach($this->data as $unique_key => $item ) {
			$result[$unique_key] = (isset($item['options']) && is_array($item['options'])) ? $item['options'] : array();
		}

		return $result;
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		if (!isset($atts['data_provider']['population_method'])) {
			trigger_error(
				__('No location provider specified for map shortcode', 'fw')
			);
			return '<b>' . __( 'Map Placeholder', 'fw' ) . '</b>';
		}

		$this->load_data();
		$provider = $atts['data_provider']['population_method'];
		if (!isset($this->data[$provider])) {
			return '<!-- WARNING: '
			       . sprintf(__('Unknown location provider "%s" specified for map shortcode', 'fw'), $provider)
			       . ' -->';
		}

		/**
		 * @var $locations array structure:
		 * array(
		 *      array(
		 *          'title' => 'some_string',              //some text  (string) optional
		 *          'url'   => 'http://example.com'        //some uri   (string) optional
		 *          'description' => 'some string'         //some text  (string) optional
		 *          'thumb' => array(
		 *              'attachment_id' => '1'             //Existing atachment id (int)  optional
		 *          )
		 *          'coordinates' => array(                //key 'coordinates'   required
		 *              'lat' => 150                       //latitude   (float)  required
		 *              'lng' => -33.5                     //longitude  (float)  required
		 *          )
		 *      )
		 * )
		 */
		$locations = call_user_func( $this->data[$provider]['callback'], $atts );
		if ( !empty($locations) && is_array($locations) ) {
			foreach( $locations as $key => $location ) {
				if (
					!isset($location['coordinates'])        ||
					!is_array($location['coordinates'])     ||
					!isset($location['coordinates']['lat']) ||
					!isset($location['coordinates']['lng']) ||
					empty($location['coordinates']['lat'])  ||
					empty($location['coordinates']['lng'])
				) {
					//remove locations which has wrong coordinates/empty
					unset($locations[$key]);
				}
			}
		}

		$map_data_attr = array(
			'data-locations'  => json_encode(array_values($locations)),
			'data-map-type'   => strtoupper( fw_akg('map_type', $atts, 'roadmap') ),
			'data-map-height' => fw_akg('map_height', $atts, false),
		);

		unset($atts['data_provider']);
		unset($atts['map_type']);
		unset($atts['map_height']);

		foreach ( $atts as $key => $att ) {
			$new_key = 'data-' . str_replace( '_', '-', $key );
			if ( is_array( $att ) || is_object( $att ) ) {
				$att = json_encode($att);
			}

			$map_data_attr[$new_key] = $att;
		}


		$this->enqueue_static();
		return fw_render_view( $this->locate_path('/views/view.php'), compact('atts', 'content', 'tag', 'map_data_attr') );
	}

	/**
	 * Just a wrapper for the method render
	 * @param $extra array
	 * @param $data array
	 * @return string Generated shortcode html
	 *
	 * @var $extra = arrray(
	 *          'map_type'   => 'roadmap' // string any of (roadmap | terrain | satellite | hybrid )
	 *          'map_height' => '300'     // int height for map canvas block
	 * )
	 *
	 * @var $data = array(
	 *                  array(
	 *                      'description' => 'some desc'   //string
	 *                      'thumb' => array(
	 *                             'attachment_id' => '1'  //int any existing attachment id
	 *                       )
	 *                      'title' =>  'some title',      //string
	 *                      'url'   =>  'http://link.com', //string
	 *                      'location' => array(
	 *                            'coordinates' => array(
	 *                                  'lat' =>  -12,     //int
	 *                                  'lng' => 10        //int
	 *                                  )
	 *                             )
	 *                       )
	 *                   )
	 */
	public function render_custom($data, $extra = array()) {
		$atts = array(
			'map_height'    => fw_akg('map_height', $extra, false),
			'map_type'      => fw_akg('map_type', $extra, 'roadmap'),
			'data_provider' => array(
				'population_method' => 'custom',
				'custom' => array(
					'locations' => $data
				)
			)
		);
		return $this->_render($atts);
	}
}