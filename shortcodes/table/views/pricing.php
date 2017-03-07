<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

$class_width = 'fw-col-sm-' . ceil( 12 / count( $atts['table']['cols'] ) );

/** @var FW_Extension_Shortcodes $shortcodes */
$shortcodes = fw_ext( 'shortcodes' );
/** @var FW_Shortcode_Table $table */
$table = $shortcodes->get_shortcode( 'table' );

?>
<div class="fw-pricing">
	<?php foreach ( $atts['table']['cols'] as $col_key => $col ): ?>
        <div class="fw-package-wrap <?php echo esc_attr( $class_width . ' ' . $col['name'] ); ?> ">
            <div class="fw-package">
				<?php foreach ( $atts['table']['rows'] as $row_key => $row ): ?>
					<?php if ( $col['name'] == 'desc-col' ) : ?>
                        <div class="fw-default-row">
							<?php echo fw_akg( 'textarea', $atts['table']['content'][ $row_key ][ $col_key ], '' ); ?>
                        </div>
						<?php continue; endif; ?>
					<?php if ( $row['name'] === 'heading-row' ): ?>
                        <div class="fw-heading-row">
                            <span>
								<?php echo fw_akg(
									'textarea',
									$atts['table']['content'][ $row_key ][ $col_key ],
									$col['name'] === 'desc-col' ? '&nbps;' : ''
								); ?>
							</span>
                        </div>
					<?php elseif ( $row['name'] === 'pricing-row' ): ?>
                        <div class="fw-pricing-row">
                            <span><?php echo fw_akg(
		                            'amount',
		                            $atts['table']['content'][ $row_key ][ $col_key ],
		                            $col['name'] === 'desc-col' ? '&nbps;' : ''
	                            ) ?></span>
                            <small><?php echo fw_akg(
									'description',
									$atts['table']['content'][ $row_key ][ $col_key ],
									$col['name'] === 'desc-col' ? '&nbps;' : ''
								) ?></small>
                        </div>
					<?php elseif ( $row['name'] == 'button-row' ) : ?>
						<?php if ( $button = $table->get_button_shortcode() ): ?>
                            <div class="fw-button-row">
								<?php if ( false === empty( $atts['table']['content'][ $row_key ][ $col_key ]['button'] ) and false === empty( $button ) ) : ?>
									<?php echo $button->render( $atts['table']['content'][ $row_key ][ $col_key ]['button'] ); ?>
								<?php else : ?>
                                    <span>&nbsp;</span>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
					<?php elseif ( $row['name'] === 'switch-row' ) : ?>
                        <div class="fw-switch-row">
							<?php $value = $atts['table']['content'][ $row_key ][ $col_key ]['switch']; ?>
                            <span>
								<i class="fa fw-price-icon-<?php echo esc_attr( $value ) ?>"></i>
							</span>
                        </div>
					<?php elseif ( $row['name'] === 'default-row' ) : ?>
                        <div class="fw-default-row"><?php
							echo fw_akg( "textarea", $atts['table']['content'][ $row_key ][ $col_key ] )
							?></div>
					<?php endif; ?>
				<?php endforeach; ?>
            </div>
        </div>
	<?php endforeach; ?>
</div>