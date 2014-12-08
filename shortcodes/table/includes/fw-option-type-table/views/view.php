<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  string $id
 * @var  array $option
 * @var  array $data
 * @var  array $internal_options
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
	<div class="fw-table">
		<br class="fw-cell-template"
		    data-worksheet-cell-template='<?php echo fw_htmlspecialchars( fw_render_view( $worksheet_cell_template, array(
			    'internal_options'  => $internal_options,
			    'option'            => $option,
			    'data'              => $data,
			    'i'                 => '_template_key_row_',
			    'j'                 => '_template_key_col_',
			    'cell_value'        => array(
				    'button'   => array(),
		            'textarea' =>'',
		            'switch'   => array()
			        )
			    )
		    )) ?>'
		    data-header-cell-template='<?php echo fw_htmlspecialchars( fw_render_view( $header_cell_template, array(
			    'internal_options' => $internal_options,
			    'option' => $option,
			    'j' => '_template_key_col_',
			    'data' => array()
		    ) ) ) ?>'
			/>

		<?php /** Start heading row */ ?>

		<div class="fw-table-row fw-table-col-options">
			<div class="fw-table-cell fw-table-cell-options empty-cell">&nbsp;</div>

			<?php $j = 0; ?>
			<?php foreach ( reset( $data['value']['content'] ) as $key_col => $val ) : ?>

				<div class="fw-table-cell fw-table-col-option <?php echo $data['value']['cols'][ $j ]['class'] ?>"
				     data-col="<?php echo $j ?>">
					<?php echo fw_render_view( $header_cell_template, compact( 'internal_options', 'option', 'data', 'option', 'j' ) );  ?>
				</div>
				<?php $j++; ?>
			<?php  endforeach; ?>

			<div class="fw-table-cell fw-table-row-delete empty-cell">&nbsp;</div>

		</div>

		<?php /** End heading row */ ?>


		<?php /** Start data rows */?>
		<?php $i = 0; ?>
		<?php foreach ( $data['value']['content'] as $key_row => $row ) : ?>

			<?php $data_rows = array(
				'value'       => $data['value']['rows'][ $key_row ],
				'id_prefix'   => $option['attr']['id'] . '-rows-',
				'name_prefix' => $option['attr']['name'] . '[rows]'
			);?>

			<div class="fw-table-row <?php echo $data['value']['rows'][ $key_row ] ?>"
			     data-row="<?php echo $i ?>">
				<div class='fw-table-cell fw-table-cell-options <?php echo $data['value']['rows'][ $key_row ] ?>'>
					<i class="fa fa-unsorted fw-table-gripper"></i>
					<?php echo fw()->backend->option_type( 'select' )->render( $i, $option['row_options'], $data_rows ); ?>
				</div>

				<?php $j = 0; ?>
				<?php foreach ( $row as $key_col => $cell_value ): ?>
					<div class='fw-table-cell fw-table-cell-worksheet <?php echo $data['value']['cols'][ $key_col ]['class'] ?>'
					     data-col="<?php echo $j ?>">

						<?php $worksheet_cell_template = fw_ext( 'shortcodes' )->get_shortcode( 'table' )->get_declared_path() . '/includes/fw-option-type-table/views/cell-worksheet-template.php'; ?>
						<?php echo fw_render_view( $worksheet_cell_template, compact( 'internal_options', 'option', 'data', 'j', 'i', 'cell_value' ) );  ?>

					</div>
					<?php $last_col = $j; ?>
					<?php $j++; ?>
				<?php endforeach; ?>

				<div class="fw-table-cell fw-table-row-delete">
					<i class="fw-table-row-delete-btn fw-x-button dashicons fw-x"></i>
				</div>

			</div>
			<?php $last_row = $i; ?>
			<?php $i++; ?>
		<?php endforeach; ?>
		<!--end data rows -->

		<!--start template row-->
		<div class="fw-table-row fw-template-row">

			<div class='fw-table-cell fw-table-cell-options'>
				<i class="fa fa-unsorted fw-table-gripper"></i>
				<?php $data_rows = array(
					'value'       => '',
					'id_prefix'   => $option['attr']['id'] . '-rows-',
					'name_prefix' => $option['attr']['name'] . '[rows]'
				);

				?>
				<?php echo fw()->backend->option_type( 'select' )->render( '_template_key_row_', $option['row_options'], $data_rows ); ?>
			</div>

			<?php $j = 0; ?>
			<?php foreach ( reset( $data['value']['content'] ) as $key_col => $val ) : ?>
				<?php $data_cols = array(
					'value'       => '',
					'id_prefix'   => $option['attr']['id'] . '-cols-',
					'name_prefix' => $option['attr']['name'] . '[cols]'
				);
				?>
				<div class='fw-table-cell fw-table-cell-worksheet <?php echo $data['value']['cols'][ $key_col ]['class'] ?>'
				     data-col="<?php echo $j ?>">
				</div>

				<?php $j++; ?>
			<?php endforeach; ?>

			<div class="fw-table-cell fw-table-row-delete">
				<i class="dashicons fw-x fw-table-row-delete-btn"></i>
			</div>

		</div>
		<!--end template row-->

		<!--start delete buttons row -->
		<div class="fw-table-row fw-table-cols-delete">

			<div class="fw-table-cell fw-table-cell-options"><a href="#"
			                                                    class="fw-table-add-row button button-large"><?php echo __( 'Add Row', 'fw' ) ?></a>
			</div>
			<?php $j = 0 ?>
			<?php foreach ( reset( $data['value']['content'] ) as $val ) : ?>
				<?php $data_cols = array(
					'value'       => '',
					'id_prefix'   => $option['attr']['id'] . '-cols-',
					'name_prefix' => $option['attr']['name'] . '[cols]'
				);
				?>
				<div class="fw-table-cell fw-table-col-delete"
				     data-col="<?php echo $j ?>">
					<i class="dashicons fw-x fw-table-col-delete-btn"></i>
				</div>
				<?php $j++; ?>
			<?php endforeach; ?>

			<div class="fw-table-cell fw-table-row-delete empty-cell">&nbsp;</div>

		</div>
		<!--end delete buttons row -->
		<input type="hidden" class="fw-table-last-row" value="<?php echo $last_row ?>"/>
		<input type="hidden" class="fw-table-last-col" value="<?php echo $last_col ?>"/>
	</div>

</div>