<?php
$about_section = get_sub_field( 'about_section' );

if ( ! $about_section || ! is_array( $about_section ) ) {
	return;
}

$about_title = isset( $about_section['about_title'] ) ? $about_section['about_title'] : '';
$about_text  = isset( $about_section['about_text'] ) ? $about_section['about_text'] : '';
$about_image = isset( $about_section['about_image'] ) ? $about_section['about_image'] : null;
$about_text_mobile = '';

$image_url = '';
$image_alt = '';

if ( is_array( $about_image ) ) {
	$image_url = isset( $about_image['url'] ) ? $about_image['url'] : '';
	$image_alt = isset( $about_image['alt'] ) ? $about_image['alt'] : '';
} elseif ( is_int( $about_image ) || ctype_digit( (string) $about_image ) ) {
	$image_url = wp_get_attachment_image_url( (int) $about_image, 'full' );
	$image_alt = (string) get_post_meta( (int) $about_image, '_wp_attachment_image_alt', true );
} elseif ( is_string( $about_image ) ) {
	$image_url = $about_image;
}

if ( $about_text ) {
	$about_text_plain  = trim( wp_strip_all_tags( (string) $about_text ) );
	$about_text_mobile = $about_text_plain;

	if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
		if ( mb_strlen( $about_text_plain ) > 330 ) {
			$about_text_mobile = rtrim( mb_substr( $about_text_plain, 0, 330 ) ) . '...';
		}
	} elseif ( strlen( $about_text_plain ) > 330 ) {
		$about_text_mobile = rtrim( substr( $about_text_plain, 0, 330 ) ) . '...';
	}
}
?>

<section class="about-section" aria-label="<?php esc_attr_e( 'About our team', 'plumber' ); ?>">
	<div class="about-section__container">
		<?php if ( $about_title ) : ?>
			<h2 class="about-section__title"><?php echo esc_html( $about_title ); ?></h2>
		<?php endif; ?>

		<div class="about-section__media-wrap">
			<?php if ( $image_url ) : ?>
				<img class="about-section__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ? $image_alt : $about_title ); ?>">
			<?php endif; ?>

			<?php if ( $about_text ) : ?>
				<div class="about-section__text-box">
					<div class="about-section__text-desktop"><?php echo wp_kses_post( wpautop( $about_text ) ); ?></div>
					<div class="about-section__text-mobile"><p><?php echo esc_html( $about_text_mobile ); ?></p></div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
