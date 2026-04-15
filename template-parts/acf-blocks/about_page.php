<?php
$about_page_section = get_sub_field( 'about_page_section' );

if ( ! $about_page_section || ! is_array( $about_page_section ) ) {
	return;
}

$about_page_title = isset( $about_page_section['about_page_title'] ) ? $about_page_section['about_page_title'] : '';
$about_page_text  = isset( $about_page_section['about_page_text'] ) ? $about_page_section['about_page_text'] : '';
$about_page_image = isset( $about_page_section['about_page_image'] ) ? $about_page_section['about_page_image'] : null;
$about_page_items = isset( $about_page_section['about_page_items'] ) && is_array( $about_page_section['about_page_items'] ) ? $about_page_section['about_page_items'] : array();

$image_url = '';
$image_alt = '';

if ( is_array( $about_page_image ) ) {
	$image_url = isset( $about_page_image['url'] ) ? $about_page_image['url'] : '';
	$image_alt = isset( $about_page_image['alt'] ) ? $about_page_image['alt'] : '';
} elseif ( is_int( $about_page_image ) || ctype_digit( (string) $about_page_image ) ) {
	$image_url = wp_get_attachment_image_url( (int) $about_page_image, 'full' );
	$image_alt = (string) get_post_meta( (int) $about_page_image, '_wp_attachment_image_alt', true );
} elseif ( is_string( $about_page_image ) ) {
	$image_url = $about_page_image;
}

if ( ! $about_page_title && ! $about_page_text && ! $image_url && empty( $about_page_items ) ) {
	return;
}
?>

<section class="about-page-section" aria-label="<?php esc_attr_e( 'About page section', 'plumber' ); ?>">
	<div class="about-page-section__container">
		<div class="about-page-section__top">
			<div class="about-page-section__content">
				<?php if ( $about_page_title ) : ?>
					<h2 class="about-page-section__title"><?php echo esc_html( $about_page_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $about_page_text ) : ?>
					<div class="about-page-section__text"><?php echo wp_kses_post( wpautop( $about_page_text ) ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( $image_url ) : ?>
				<div class="about-page-section__image-wrap">
					<img class="about-page-section__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ? $image_alt : $about_page_title ); ?>">
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $about_page_items ) ) : ?>
			<div class="about-page-section__items" aria-label="<?php esc_attr_e( 'About page stats', 'plumber' ); ?>">
				<?php foreach ( $about_page_items as $item ) : ?>
					<?php
					$item_prefix = isset( $item['item_prefix'] ) ? (string) $item['item_prefix'] : '';
					$item_number = isset( $item['item_number'] ) ? (string) $item['item_number'] : '0';
					$item_suffix = isset( $item['item_suffix'] ) ? (string) $item['item_suffix'] : '';
					$item_text   = isset( $item['item_text'] ) ? (string) $item['item_text'] : '';
					?>
					<article class="about-page-item">
						<h3 class="about-page-item__value">
							<?php if ( '' !== $item_prefix ) : ?>
								<span class="about-page-item__prefix"><?php echo esc_html( $item_prefix ); ?></span>
							<?php endif; ?>
							<span class="about-page-item__number" data-count-to="<?php echo esc_attr( trim( $item_number ) ); ?>">0</span>
							<?php if ( '' !== $item_suffix ) : ?>
								<span class="about-page-item__suffix"><?php echo esc_html( $item_suffix ); ?></span>
							<?php endif; ?>
						</h3>
						<?php if ( '' !== trim( $item_text ) ) : ?>
							<p class="about-page-item__text"><?php echo esc_html( $item_text ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
