<?php
$hero_section = get_sub_field( 'hero_section' );

if ( ! $hero_section || ! is_array( $hero_section ) ) {
	return;
}

$hero_image_1   = isset( $hero_section['hero_image_1'] ) ? $hero_section['hero_image_1'] : null;
$hero_image_2   = isset( $hero_section['hero_image_2'] ) ? $hero_section['hero_image_2'] : null;
$hero_button_1  = isset( $hero_section['hero_button_1'] ) ? $hero_section['hero_button_1'] : null;
$hero_button_2  = isset( $hero_section['hero_button_2'] ) ? $hero_section['hero_button_2'] : null;
$hero_link_icon = isset( $hero_section['hero_link_icon'] ) ? $hero_section['hero_link_icon'] : null;

$resolve_image = static function ( $image_field ) {
	$image_url = '';
	$image_alt = '';

	if ( is_array( $image_field ) ) {
		$image_url = isset( $image_field['url'] ) ? $image_field['url'] : '';
		$image_alt = isset( $image_field['alt'] ) ? $image_field['alt'] : '';
	} elseif ( is_int( $image_field ) || ctype_digit( (string) $image_field ) ) {
		$image_url = wp_get_attachment_image_url( (int) $image_field, 'full' );
		$image_alt = (string) get_post_meta( (int) $image_field, '_wp_attachment_image_alt', true );
	} elseif ( is_string( $image_field ) ) {
		$image_url = $image_field;
	}

	return array(
		'url' => $image_url,
		'alt' => $image_alt,
	);
};

$img_1 = $resolve_image( $hero_image_1 );
$img_2 = $resolve_image( $hero_image_2 );
?>

<section class="hero-section" aria-label="<?php esc_attr_e( 'Hero section', 'plumber' ); ?>">
	<div class="hero-section__container">
		<div class="hero-section__images">
			<?php if ( ! empty( $img_1['url'] ) ) : ?>
				<div class="hero-section__image-wrap hero-section__image-wrap--primary">
					<img src="<?php echo esc_url( $img_1['url'] ); ?>" alt="<?php echo esc_attr( $img_1['alt'] ); ?>">
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $img_2['url'] ) ) : ?>
				<div class="hero-section__image-wrap hero-section__image-wrap--secondary">
					<img src="<?php echo esc_url( $img_2['url'] ); ?>" alt="<?php echo esc_attr( $img_2['alt'] ); ?>">
				</div>
			<?php endif; ?>
		</div>

		<div class="hero-section__buttons">
			<?php foreach ( array( $hero_button_1, $hero_button_2 ) as $index => $button ) : ?>
				<?php if ( is_array( $button ) && ! empty( $button['url'] ) ) : ?>
					<?php $button_class = 0 === $index ? 'hero-section__button hero-section__button--filled' : 'hero-section__button hero-section__button--outline'; ?>
					<a class="<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( $button['url'] ); ?>" target="<?php echo esc_attr( ! empty( $button['target'] ) ? $button['target'] : '_self' ); ?>" <?php echo ( ! empty( $button['target'] ) && '_blank' === $button['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( ! empty( $button['title'] ) ? $button['title'] : __( 'Button', 'plumber' ) ); ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( is_array( $hero_link_icon ) && ! empty( $hero_link_icon['url'] ) ) : ?>
		<a class="hero-section__phone-link" href="<?php echo esc_url( $hero_link_icon['url'] ); ?>" target="<?php echo esc_attr( ! empty( $hero_link_icon['target'] ) ? $hero_link_icon['target'] : '_self' ); ?>" aria-label="<?php echo esc_attr( ! empty( $hero_link_icon['title'] ) ? $hero_link_icon['title'] : __( 'Call us', 'plumber' ) ); ?>" <?php echo ( ! empty( $hero_link_icon['target'] ) && '_blank' === $hero_link_icon['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/phone.svg' ); ?>" alt="">
		</a>
	<?php endif; ?>
</section>
