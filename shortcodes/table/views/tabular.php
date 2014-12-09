<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
?>
<?php
/*
 * you may add the following classes to the `.fw-table` div:
 * `fw-table-bordered`, `fw-table-hover`, `fw-table-striped`
 */
?>
<div class="fw-table">
	<table>
		<?php foreach ( $atts['table']['rows'] as $row_key => $row ) : ?>
			<?php if ( $row['name'] == 'heading-row' ) : ?>
				<thead>
					<tr class="<?php echo $row['name']; ?>">
						<?php foreach ( $atts['table']['cols'] as $col_key => $col ) : ?>
							<th class="<?php echo $col['name']; ?>">
								<?php echo $atts['table']['content'][ $row_key ][ $col_key ]['textarea']; ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
			<?php elseif ( $row['name'] == 'button-row' ) : ?>
				<tr class="<?php echo $row['name'] ?>">
					<?php foreach ( $atts['table']['cols'] as $col_key => $col ) : ?>
						<td class="<?php echo $col['name'] ?>">
							<?php $button = fw_ext( 'shortcodes' )->get_shortcode( 'button' ); ?>
							<?php if ( false === empty( $atts['table']['content'][ $row_key ][ $col_key ]['button'] ) and false === empty($button) ) : ?>
								<?php echo fw_render_view( $button->locate_path( '/views/view.php' ) , array( 'atts' => $atts['table']['content'][ $row_key ][ $col_key ]['button'] ) ); ?>
							<?php endif; ?>
						</td>
					<?php endforeach ?>
				</tr>
			<?php elseif ( $row['name'] == 'default-row' ) : ?>
				<tr class="<?php echo $row['name']; ?>">
					<?php foreach ( $atts['table']['cols'] as $col_key => $col ) : ?>
						<td class="<?php echo $col['name']; ?>">
							<?php echo $atts['table']['content'][ $row_key ][ $col_key ]['textarea']; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php elseif ( $row['name'] == 'pricing-row' ) : ?>
				<tr class="<?php echo $row['name']; ?>">
					<?php foreach ( $atts['table']['cols'] as $col_key => $col ) : ?>
						<td class="<?php echo $col['name']; ?>">
							<?php echo $atts['table']['content'][ $row_key ][ $col_key ]['amount']; ?>
							<?php echo $atts['table']['content'][ $row_key ][ $col_key ]['description']; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php elseif ( $row['name'] == 'switch-row' ) : ?>
				<tr class="<?php echo $row['name']; ?>">
					<?php foreach ( $atts['table']['cols'] as $col_key => $col ) : ?>
						<td class="<?php echo $col['name']; ?>">
							<?php echo $atts['table']['content'][ $row_key ][ $col_key ]['switch']; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php
			else : ?>
				<tr class="<?php echo $row['name'] ?>">
					<?php foreach ( $atts['table']['cols'] as $col_key => $col ) : ?>
						<td class="<?php echo $col['name'] ?>">
							<?php echo json_encode($atts['table']['content'][ $row_key ][ $col_key ]); ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</table>
</div>