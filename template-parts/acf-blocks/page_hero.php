<?php
$page_hero_section = get_sub_field( 'page_hero_section' );

if ( ! $page_hero_section || ! is_array( $page_hero_section ) ) {
	return;
}

$page_hero_title    = isset( $page_hero_section['page_hero_title'] ) ? $page_hero_section['page_hero_title'] : '';
$page_hero_image_bg = isset( $page_hero_section['page_hero_image_bg'] ) ? $page_hero_section['page_hero_image_bg'] : null;

$image_url = '';
$image_alt = '';

if ( is_array( $page_hero_image_bg ) ) {
	$image_url = isset( $page_hero_image_bg['url'] ) ? $page_hero_image_bg['url'] : '';
	$image_alt = isset( $page_hero_image_bg['alt'] ) ? $page_hero_image_bg['alt'] : '';
} elseif ( is_int( $page_hero_image_bg ) || ctype_digit( (string) $page_hero_image_bg ) ) {
	$image_url = wp_get_attachment_image_url( (int) $page_hero_image_bg, 'full' );
	$image_alt = (string) get_post_meta( (int) $page_hero_image_bg, '_wp_attachment_image_alt', true );
} elseif ( is_string( $page_hero_image_bg ) ) {
	$image_url = $page_hero_image_bg;
}

if ( ! $page_hero_title && ! $image_url ) {
	return;
}
?>

<section class="page-hero-section" aria-label="<?php esc_attr_e( 'Page hero section', 'plumber' ); ?>">
	<div class="page-hero-section__container">
		<div class="page-hero-section__media">
			<?php if ( $image_url ) : ?>
				<img class="page-hero-section__bg" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ? $image_alt : $page_hero_title ); ?>">
			<?php endif; ?>

			<?php if ( $page_hero_title ) : ?>
				<h1 class="page-hero-section__title"><?php echo esc_html( $page_hero_title ); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>
