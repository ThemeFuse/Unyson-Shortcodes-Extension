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

<div <?php echo fw_attr_to_html( $wrapper_attr ) ?>>

	<div class="fw-table">
		<!--start heading row -->
		<div class="fw-table-row fw-table-col-options">

			<div class="fw-table-cell fw-table-cell-options empty-cell">&nbsp;</div>

			<?php $j=0; ?>
			<?php foreach ( reset( $data['value']['content'] ) as $key_col => $val ) : ?>
				<?php $data_cols = array(
					'value'       => $data['value']['cols'][ $j ],
					'id_prefix'   => $option['attr']['id'] . '-cols-',
					'name_prefix' => $option['attr']['name'] . '[cols]'
				);
				?>

				<div class="fw-table-cell fw-table-col-option <?php echo $data['value']['cols'][ $j ] ?>"
				     data-col="<?php echo $j ?>">
					<?php echo fw()->backend->option_type( 'select' )->render( $j, $option['columns_options'], $data_cols ); ?>
					<a href="#"
					   class="fw-table-add-column button button-large"><?php echo __( 'Add Column', 'fw' ) ?></a>
				</div>
				<?php $j++; ?>
			<?php  endforeach; ?>

			<div class="fw-table-cell fw-table-row-delete empty-cell">&nbsp;</div>

		</div>
		<!--end heading row -->


		<!--start data rows -->
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
					<div class='fw-table-cell fw-table-cell-worksheet <?php echo $data['value']['cols'][ $j ] ?>'
					     data-col="<?php echo $j ?>">
						<div class="fw-table-cell-content"><?php echo $cell_value['textarea'] ?></div>

						<?php
						$popup_data = array(
							'id_prefix'   => $option['attr']['id'] . '-',
							'name_prefix' => $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . ']',
							'value'       => isset($cell_value['button']) ? json_encode($cell_value['button']) : ''
						);

						//set popup-button title
						$internal_options['button'] = __( 'Edit', 'fw' );
						if ( empty( $cell_value['button'] ) ) {
							$internal_options['button'] = __( 'Add', 'fw' );
						}

						?>

						<div
							class="fw-table-cell-button"><?php echo fw()->backend->option_type( 'popup' )->render( 'button', $internal_options, $popup_data ) ?></div>
						<?php echo '<textarea rows="5" id="' . $option['attr']['id'] . '-textarea-' . $i . '-' . $j . '" name="' . $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . '][textarea]" value="' . $cell_value['textarea'] . '">' . $cell_value['textarea'] . '</textarea>' ?>
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

			<?php $j=0; ?>
			<?php foreach ( reset( $data['value']['content'] ) as $key_col => $val ) : ?>
				<?php $data_cols = array(
					'value'       => '',
					'id_prefix'   => $option['attr']['id'] . '-cols-',
					'name_prefix' => $option['attr']['name'] . '[cols]'
				);
				?>
				<div class='fw-table-cell fw-table-cell-worksheet <?php echo $data['value']['cols'][ $j ] ?>'
				     data-col="<?php echo $j ?>">
					<div class="fw-table-cell-content"></div>

					<?php

					$popup_data = array(
						'id_prefix'   => $option['attr']['id'] . '-',
						'name_prefix' => $option['attr']['name'] . '[content][_template_key_row_][_template_key_col_]',
						'value'       => ''
					);

					$internal_options['button'] = __( 'Add', 'fw' );
					?>
					<div
						class="fw-table-cell-button"><?php echo fw()->backend->option_type( 'popup' )->render( 'button', $internal_options, $popup_data ) ?></div>

					<?php echo '<textarea rows="5" id="' . $option['attr']['id'] . '-textarea-_template_key_row_-_template_key_col_" name="' . $option['attr']['name'] . '[content][_template_key_row_][_template_key_col_][textarea]" value=""></textarea>' ?>
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
			<?php $j=0 ?>
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