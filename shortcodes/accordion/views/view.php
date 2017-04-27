<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
?>
<div class="fw-accordion">
	<?php foreach ( fw_akg( 'tabs', $atts, array() ) as $tab ) : ?>
		<h3 class="fw-accordion-title"><?php echo $tab['tab_title']; ?></h3>
		<div class="fw-accordion-content">
			<p><?php echo do_shortcode( $tab['tab_content'] ); ?></p>
		</div>
	<?php endforeach; ?>
</div>