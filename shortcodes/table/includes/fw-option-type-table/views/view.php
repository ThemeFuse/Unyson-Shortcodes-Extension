<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  string $id
 * @var  array $option
 * @var  array $data
 */

$last_row = 0;
$last_col = 0;

$wrapper_attr = $option['attr'];
unset(
	$wrapper_attr['name'],
	$wrapper_attr['value']
);

?>

<?php $header_cell_template = fw_ext( 'shortcodes' )->get_shortcode( 'table' )->get_declared_path() . '/includes/fw-option-type-table/views/cell-head-template.php'; ?>
<?php $worksheet_cell_template = fw_ext( 'shortcodes' )->get_shortcode( 'table' )->get_declared_path() . '/includes/fw-option-type-table/views/cell-worksheet-template.php'; ?>

<div <?php echo fw_attr_to_html( $wrapper_attr ) ?>>

	<?php $data_header = array(
		'name_prefix' => $option['attr']['name'] . '[header_options]',
		'id_prefix'   => $option['attr']['id'] . '-header-options',
	) ?>

	<?php $values_header = isset( $data['value']['header_options'] ) ? $data['value']['header_options'] : array() ?>

	<?php echo fw()->backend->render_options( $option['header_options'], $values_header, $data_header ) ?>

	<div class="fw-table">
		<br class="fw-cell-template"
		    data-worksheet-cell-template='<?php echo fw_htmlspecialchars( fw_render_view( $worksheet_cell_template,
			    array(
				    'option'           => $option,
				    'data'             => $data,
				    'current_row_name' => 'default-row',
				    'i'                => '_template_key_row_',
				    'j'                => '_template_key_col_',
				    'desc_col'         => - 1,
				    'cell_value'       => array(
					    'button'   => array(),
					    'textarea' => '',
					    'switch'   => array()
				    )
			    )
		    ) ) ?>'
		    data-header-cell-template='<?php echo fw_htmlspecialchars( fw_render_view( $header_cell_template, array(
			    'option' => $option,
			    'j'      => '_template_key_col_',
			    'data'   => array()
		    ) ) ) ?>'
			/>

		<?php /** Start heading row */ ?>

		<div class="fw-table-row fw-table-col-options">
			<div class="fw-table-cell fw-table-cell-options empty-cell">&nbsp;</div>

			<?php $j = 0; ?>
			<?php foreach ( $data['value']['cols'] as $col ) : ?>

				<div class="fw-table-cell fw-table-col-option <?php echo esc_attr($col['name']) ?>"
				     data-col="<?php echo esc_attr($j) ?>">
					<?php echo fw_render_view( $header_cell_template, compact( 'option', 'data', 'j' ) ); ?>
				</div>
				<?php $j ++; ?>
			<?php endforeach; ?>

			<div class="fw-table-cell fw-table-row-delete empty-cell">&nbsp;</div>

		</div>

		<?php /** End heading row */ ?>


		<?php /** Start data rows */ ?>
		<?php $i = 0; ?>
		<?php foreach ( $data['value']['content'] as $key_row => $row ) : ?>
			<?php $current_row_name = $data['value']['rows'][ $key_row ]['name']; ?>
			<?php $data_rows = array(
				'value'       => $data['value']['rows'][ $key_row ]['name'],
				'id_prefix'   => $option['attr']['id'] . '-rows-',
				'name_prefix' => $option['attr']['name'] . '[rows][' . $i . ']'
			); ?>

			<div class="fw-table-row <?php echo esc_attr($current_row_name) ?>"
			     data-row="<?php echo esc_attr($i) ?>">
				<div
					class='fw-table-cell fw-table-cell-options <?php echo esc_attr($data['value']['rows'][ $key_row ]['name']) ?>'>
					<i class="fa fa-unsorted fw-table-gripper"></i>
					<?php echo fw()->backend->option_type( 'select' )->render( 'name', $option['row_options']['name'],
						$data_rows ); ?>
				</div>

				<?php $j = 0; ?>
				<?php foreach ( $row as $key_col => $cell_value ): ?>
					<div
						class='fw-table-cell fw-table-cell-worksheet <?php echo esc_attr($data['value']['cols'][ $key_col ]['name']); ?>'
						data-col="<?php echo esc_attr($j) ?>">

						<?php $desc_col = $data['value']['cols'][ $key_col ]['name'] == 'desc-col' ? $j : - 1 ?>

						<?php $worksheet_cell_template = fw_ext( 'shortcodes' )->get_shortcode( 'table' )->get_declared_path() . '/includes/fw-option-type-table/views/cell-worksheet-template.php'; ?>
						<?php echo fw_render_view( $worksheet_cell_template,
							compact( 'option', 'data', 'j', 'i', 'cell_value', 'current_row_name', 'desc_col' ) ); ?>

					</div>
					<?php $last_col = $j; ?>
					<?php $j ++; ?>
				<?php endforeach; ?>

				<div class="fw-table-cell fw-table-row-delete">
					<i class="fw-table-row-delete-btn fw-x-button dashicons fw-x"></i>
				</div>

			</div>
			<?php $last_row = $i; ?>
			<?php $i ++; ?>
		<?php endforeach; ?>
		<?php /** End data rows */ ?>


		<?php /** Start template row */ ?>
		<div class="fw-table-row fw-template-row fw-filter-from-serialization">
			<div class='fw-table-cell fw-table-cell-options'>
				<i class="fa fa-unsorted fw-table-gripper"></i>
				<?php $data_rows = array(
					'value'       => '',
					'id_prefix'   => $option['attr']['id'] . '-rows-',
					'name_prefix' => $option['attr']['name'] . '[rows][_template_key_row_]'
				);

				?>
				<?php echo fw()->backend->option_type( 'select' )->render( 'name', $option['row_options']['name'],
					$data_rows ); ?>
			</div>

			<?php for ( $j = 0; $j <= $last_col; $j ++ )  : ?>
				<div class="fw-table-cell fw-table-cell-worksheet <?php echo esc_attr($data['value']['cols'][ $j ]['name']) ?>"
				     data-col="<?php echo esc_attr($j) ?>"></div>
			<?php endfor; ?>

			<div class="fw-table-cell fw-table-row-delete">
				<i class="dashicons fw-x fw-table-row-delete-btn"></i>
			</div>

		</div>
		<?php /** End template row */ ?>


		<?php /** Start delete buttons row **/ ?>
		<div class="fw-table-row fw-table-cols-delete">

			<div class="fw-table-cell fw-table-cell-options">
				<a href="#" class="fw-table-add-row button button-large"><?php echo __( 'Add Row', 'fw' ) ?></a>
			</div>

			<?php for ( $j = 0; $j <= $last_col; $j ++ )  : ?>
				<div class="fw-table-cell fw-table-col-delete"
				     data-col="<?php echo esc_attr($j) ?>">
					<i class="dashicons fw-x fw-table-col-delete-btn"></i>
				</div>
			<?php endfor; ?>

			<div class="fw-table-cell fw-table-row-delete empty-cell">&nbsp;</div>

		</div>
		<?php /** End delete buttons row **/ ?>

		<input type="hidden" class="fw-table-last-row" value="<?php echo fw_htmlspecialchars($last_row) ?>"/>
		<input type="hidden" class="fw-table-last-col" value="<?php echo fw_htmlspecialchars($last_col) ?>"/>
	</div>

</div>
