<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
$class_width = 'fw-col-sm-' . floor(12 / count($atts['table']['cols']));

?>
<div class="fw-pricing">
	<?php foreach ($atts['table']['cols'] as $col_key => $col): ?>
		<div class="fw-package-wrap <?php echo $class_width . ' ' . $col['name']; ?> ">
			<div class="fw-package">
				<?php foreach ($atts['table']['rows'] as $row_key => $row): ?>
					<?php if ($row['name'] === 'heading-row'): ?>
						<div class="fw-heading-row">
							<?php $value = $atts['table']['content'][$row_key][$col_key]['textarea']; ?>
							<span>
								<?php echo (empty($value) && $col['name'] === 'desc-col') ? '&nbps;' : $value; ?>
							</span>
						</div>
					<?php elseif ($row['name'] === 'pricing-row'): ?>
						<div class="fw-pricing-row">
							<?php $value = $atts['table']['content'][$row_key][$col_key]['amount'] . $atts['table']['content'][$row_key][$col_key]['description']; ?>
							<span>
								<?php echo (empty($value) && $col['name'] === 'desc-col') ? '&nbps;' : $value; ?>
							</span>
						</div>
					<?php elseif ( $row['name'] == 'button-row' ) : ?>
						<?php $button = fw_ext( 'shortcodes' )->get_shortcode( 'button' ); ?>
						<?php if ( false === empty( $atts['table']['content'][ $row_key ][ $col_key ]['button'] ) and false === empty($button) ) : ?>
							<div class="fw-button-row">
								<?php echo fw_render_view(  $button->locate_path( '/views/view.php' ), array( 'atts' => $atts['table']['content'][ $row_key ][ $col_key ]['button'] ) ); ?>
							</div>
						<?php endif; ?>
					<?php elseif ($row['name'] === 'switch-row'): ?>
						<div class="fw-switch-row">
							<?php $value = $atts['table']['content'][$row_key][$col_key]['switch']; ?>
							<span>
								<?php echo ($value === 'yes') ? '+' : '-' ?>
							</span>
						</div>
					<?php else : ?>
						<div class="col-row">
							<?php echo $atts['table']['content'][$row_key][$col_key]['textarea']; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>