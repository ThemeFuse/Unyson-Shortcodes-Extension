<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Calendar extends FW_Shortcode
{

	/**
	 * array(
	 *    'unique_id' => array(                               // some unique id (string)
	 *         'callback' => array($this, 'callable_method')  // callback array(stdClass, 'public_method')
	 *              'label' => 'label',                       // data provider label (string)
	 *              'options' => array()                      // extra options (array of options)
	 *         )
	 *    )
	 */
	private $data = array();

	private function load_data()
	{
		if (empty($this->data)) {
			$this->data = apply_filters('fw_shortcode_calendar_provider', array(
				'custom' => array(
					'callback' => false,
					'label'    => __('Custom', 'fw'),
					'desc'     => 'Add events to your calendar',
					'options'  => array(
						'custom_events' => array(
							'label' => __('Events', 'fw'),
							'popup-title' => __('Add/Edit Date & Time', 'fw'),
							'type' => 'addable-popup',
							'desc' => 'Add events to your calendar',
							'template' => '{{  if (calendar_date_range.from !== "") {  print(calendar_date_range.from + " - " + calendar_date_range.to)} else { print("' . __('Note: Please set start & end event datetime', 'fw') . '")} }}',
							'popup-options' => array(
								'title' => array(
									'type' => 'text',
									'label' =>__('Event Title','fw'),
									'desc' => __('Enter the event title', 'fw'),
								),
								'url' => array(
									'type' => 'text',
									'label' =>__('Event URL','fw'),
									'desc' => __('Enter the event URL (Ex: http://your-domain.com/event)', 'fw'),
								),
								'calendar_date_range' => array(
									'type'  => 'datetime-range',
									'label' => __('Date & Time','fw'),
									'desc'  => __('Enter the event date & time','fw'),
									'datetime-pickers' => array(
										'from' => array(
											'maxDate' => '2038/01/19',
											'minDate' => '1970/01/01',
											'timepicker' => true,
											'datepicker' => true,
											'defaultTime' => '08:00',
										),
										'to' => array(
											'maxDate' => '2038/01/19',
											'minDate' => '1970/01/01',
											'timepicker' => true,
											'datepicker' => true,
											'defaultTime' => '18:00',
										)
									),
									'value' => array(
										'from' => '',
										'to' => ''
									)
								)
							)
						)
					)
				)
			));
		}
	}

	/**
	 * Get the list of providers
	 * @internal
	 */
	public function _get_picker_dropdown_choices()
	{
		$this->load_data();
		$result = array();
		foreach($this->data as $unique_filter_key => $item ) {
			$result[$unique_filter_key] = $item['label'];
		}
		return $result;
	}

	/**
	 * Get the providers' options
	 * @internal
	 */
	public function _get_picker_choices()
	{
		$this->load_data();
		$result = array();
		foreach($this->data as $unique_filter_key => $item ) {
			$result[$unique_filter_key] = (isset($item['options']) && is_array($item['options'])) ? $item['options'] : array();
		}
		return $result;
	}

	public function _init()
	{
		$this->register_ajax();
	}

	private function register_ajax()
	{
		add_action( 'wp_ajax_shortcode_calendar_get_events',        array($this, '_ajax_get_results_json_ajax'));
		add_action( 'wp_ajax_nopriv_shortcode_calendar_get_events', array($this, '_ajax_get_results_json_ajax'));
	}

	public function _ajax_get_results_json_ajax()
	{
		$this->load_data();
		$data_provider = FW_Request::POST('data_provider');
		$result = call_user_func($this->data[$data_provider]['callback'], FW_Request::POST());
		wp_send_json_success($result);
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		if (!isset($atts['data_provider']['population_method'])) {
			trigger_error(
				__('No events provider specified for calendar shortcode', 'fw')
			);
			return '<b>Calendar Placeholder</b>';
		}

		$this->load_data();
		$provider = $atts['data_provider']['population_method'];
		if (!isset($this->data[$provider])) {
			trigger_error(
				sprintf(__('Unknown events provider "%s" specified for calendar shortcode', 'fw'), $provider)
			);
			return '<b>Calendar Placeholder</b>';
		}

		$ajax_params = apply_filters('fw_shortcode_calendar_ajax_params', array(), $provider, fw_akg( 'data_provider/' . $provider, $atts ) );
		if (is_array($ajax_params)) {
			$ajax_params = array_merge($ajax_params, array('data_provider' => $provider));
		} else {
			$ajax_params = array('data_provider' => $provider );
		}

		$wrapper_atts = array(
			'data-extends-ajax-params'  => json_encode($ajax_params),
			'data-ajax-url'             => admin_url( 'admin-ajax.php' ),
			'data-template'             => isset($atts['template']) ? $atts['template'] : 'month',
			'data-template-path'        => $this->get_declared_URI('/views/'),
			'data-first-day'            => $atts['first_week_day'],
		);

		if ($provider === 'custom'){
			$rows = fw_akg('data_provider/custom/custom_events', $atts, array());
			$event_sources = array();

			if (empty($rows) === false) {
				$key = 0;
				foreach($rows as $row) {
					if (empty($row['calendar_date_range']['from']) || empty($row['calendar_date_range']['to'])) {
						continue;
					}
					$event_sources[$key]['id'] = $key;
					$start = new DateTime($row['calendar_date_range']['from'], new DateTimeZone('GMT'));
					$end   = new DateTime($row['calendar_date_range']['to'], new DateTimeZone('GMT'));

					//set end of all_day event time 23:59:59
					if ($start == $end and $end->format('H:i') === '00:00') {
						$end->modify('+23 hour');
						$end->modify('+59 minutes');
						$end->modify('+59 second');
					}

					$event_sources[$key]['start'] = $start->format('U');
					$event_sources[$key]['end']   = $end->format('U');
					$event_sources[$key]['title'] = htmlspecialchars_decode($row['title']);
					$event_sources[$key]['url']   = $row['url'];
					$key++;
				}
			}

			$wrapper_atts['data-event-source'] = json_encode($event_sources);
		}

		$this->enqueue_static();
		return fw_render_view($this->locate_path('/views/view.php'), compact('atts', 'content', 'tag', 'wrapper_atts'));
	}
}
