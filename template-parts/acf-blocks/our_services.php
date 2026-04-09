<?php
$section = get_sub_field( 'our_services_section' );

if ( ! $section || ! is_array( $section ) ) {
	return;
}

$section_title  = isset( $section['our_services_title'] ) ? $section['our_services_title'] : '';
$button_1       = isset( $section['our_services_button_1'] ) ? $section['our_services_button_1'] : null;
$button_2       = isset( $section['our_services_button_2'] ) ? $section['our_services_button_2'] : null;
$services_query = new WP_Query(
	array(
		'post_type'      => 'our-services',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

if ( ! $services_query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>

<section class="our-services" aria-label="<?php esc_attr_e( 'Our services', 'plumber' ); ?>">
	<div class="our-services__container">
		<?php if ( $section_title ) : ?>
			<h2 class="our-services__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<div class="our-services__top-buttons">
			<?php foreach ( array( $button_1, $button_2 ) as $index => $button ) : ?>
				<?php if ( is_array( $button ) && ! empty( $button['url'] ) ) : ?>
					<?php $button_class = 0 === $index ? 'our-services__filter-button our-services__filter-button--filled' : 'our-services__filter-button our-services__filter-button--outline'; ?>
					<a class="<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( $button['url'] ); ?>" target="<?php echo esc_attr( ! empty( $button['target'] ) ? $button['target'] : '_self' ); ?>" <?php echo ( ! empty( $button['target'] ) && '_blank' === $button['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( ! empty( $button['title'] ) ? $button['title'] : __( 'Service', 'plumber' ) ); ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<div class="our-services-slider swiper">
			<div class="swiper-wrapper">
				<?php while ( $services_query->have_posts() ) : ?>
					<?php
					$services_query->the_post();

					$custom_title   = get_field( 'title' );
					$custom_image   = get_field( 'image' );
					$custom_content = get_field( 'content' );
					$display_title  = $custom_title ? $custom_title : get_the_title();
					$display_text   = $custom_content ? wp_strip_all_tags( (string) $custom_content ) : wp_strip_all_tags( (string) get_the_excerpt() );
					$display_text   = trim( preg_replace( '/\s+/', ' ', $display_text ) );

					if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
						if ( mb_strlen( $display_text ) > 300 ) {
							$display_text = rtrim( mb_substr( $display_text, 0, 300 ) ) . '...';
						}
					} elseif ( strlen( $display_text ) > 300 ) {
						$display_text = rtrim( substr( $display_text, 0, 300 ) ) . '...';
					}

					$image_url = '';
					$image_alt = '';

					if ( is_array( $custom_image ) ) {
						$image_url = isset( $custom_image['url'] ) ? $custom_image['url'] : '';
						$image_alt = isset( $custom_image['alt'] ) ? $custom_image['alt'] : '';
					} elseif ( is_int( $custom_image ) || ctype_digit( (string) $custom_image ) ) {
						$image_url = wp_get_attachment_image_url( (int) $custom_image, 'large' );
						$image_alt = (string) get_post_meta( (int) $custom_image, '_wp_attachment_image_alt', true );
					} elseif ( is_string( $custom_image ) ) {
						$image_url = $custom_image;
					}

					if ( ! $image_url && has_post_thumbnail() ) {
						$image_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					}

					if ( ! $image_alt ) {
						$image_alt = $display_title;
					}
					?>

					<article class="swiper-slide our-services-slide">
						<div class="our-services-slide__media">
							<?php if ( $image_url ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
							<?php endif; ?>
						</div>

						<div class="our-services-slide__content">
							<h3 class="our-services-slide__title"><?php echo esc_html( $display_title ); ?></h3>
							<p class="our-services-slide__text"><?php echo esc_html( $display_text ); ?></p>
							<a class="our-services-slide__button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View Details', 'plumber' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="our-services-pagination swiper-pagination"></div>
		</div>
	</div>
</section>

<?php wp_reset_postdata(); ?>
