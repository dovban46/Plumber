<?php
$contact_page_section = get_sub_field( 'contact_page_section' );

if ( ! $contact_page_section || ! is_array( $contact_page_section ) ) {
	return;
}

$contact_page_title     = isset( $contact_page_section['contact_page_title'] ) ? trim( (string) $contact_page_section['contact_page_title'] ) : '';
$contact_page_title2    = isset( $contact_page_section['contact_page_title2'] ) ? trim( (string) $contact_page_section['contact_page_title2'] ) : '';
$contact_page_adress    = isset( $contact_page_section['contact_page_adress'] ) ? trim( (string) $contact_page_section['contact_page_adress'] ) : '';
$contact_page_links     = isset( $contact_page_section['contact_page_links'] ) && is_array( $contact_page_section['contact_page_links'] ) ? $contact_page_section['contact_page_links'] : array();
$contact_page_map_link  = isset( $contact_page_section['contact_page_map_link'] ) ? $contact_page_section['contact_page_map_link'] : null;
$contact_page_form      = isset( $contact_page_section['contact_page_form'] ) ? trim( (string) $contact_page_section['contact_page_form'] ) : '';

$map_link_url    = '';
$map_link_title  = '';
$map_link_target = '_self';

if ( is_array( $contact_page_map_link ) ) {
	$map_link_url    = isset( $contact_page_map_link['url'] ) ? (string) $contact_page_map_link['url'] : '';
	$map_link_title  = isset( $contact_page_map_link['title'] ) ? (string) $contact_page_map_link['title'] : '';
	$map_link_target = ! empty( $contact_page_map_link['target'] ) ? (string) $contact_page_map_link['target'] : '_self';
} elseif ( is_string( $contact_page_map_link ) ) {
	$map_link_url = trim( $contact_page_map_link );
}
?>

<section class="contact-page-section" aria-label="<?php esc_attr_e( 'Contact page section', 'plumber' ); ?>">
	<div class="contact-page-section__container">
		<div class="contact-page-section__grid">
			<div class="contact-page-section__info">
				<?php if ( $contact_page_title ) : ?>
					<h2 class="contact-page-section__title"><?php echo esc_html( $contact_page_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $contact_page_title2 ) : ?>
					<h3 class="contact-page-section__subtitle"><?php echo esc_html( $contact_page_title2 ); ?></h3>
				<?php endif; ?>

				<?php if ( $contact_page_adress ) : ?>
					<div class="contact-page-section__address"><?php echo wp_kses_post( wpautop( esc_html( $contact_page_adress ) ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $contact_page_links ) ) : ?>
					<div class="contact-page-section__links">
						<?php foreach ( $contact_page_links as $contact_link_item ) : ?>
							<?php
							$link_data   = isset( $contact_link_item['link'] ) && is_array( $contact_link_item['link'] ) ? $contact_link_item['link'] : array();
							$link_url    = isset( $link_data['url'] ) ? (string) $link_data['url'] : '';
							$link_title  = isset( $link_data['title'] ) ? (string) $link_data['title'] : '';
							$link_target = ! empty( $link_data['target'] ) ? (string) $link_data['target'] : '_self';
							?>
							<?php if ( $link_url ) : ?>
								<a class="contact-page-section__link" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" <?php echo '_blank' === $link_target ? 'rel="noopener noreferrer"' : ''; ?>>
									<?php echo esc_html( $link_title ? $link_title : $link_url ); ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $map_link_url ) : ?>
					<a class="contact-page-section__map-link" href="<?php echo esc_url( $map_link_url ); ?>" target="<?php echo esc_attr( $map_link_target ); ?>" <?php echo '_blank' === $map_link_target ? 'rel="noopener noreferrer"' : ''; ?>>
						<span class="contact-page-section__map-link-text"><?php echo esc_html( $map_link_title ? $map_link_title : __( 'Find us on map', 'plumber' ) ); ?></span>
						<span class="contact-page-section__map-link-icon" aria-hidden="true"></span>
					</a>
				<?php endif; ?>
			</div>

			<div class="contact-page-section__form-wrap">
				<?php if ( $contact_page_form ) : ?>
					<div class="contact-form-wrapper contact-page-section__form">
						<?php echo do_shortcode( $contact_page_form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
