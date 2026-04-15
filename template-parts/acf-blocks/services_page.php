<?php
$services_page_section = get_sub_field( 'services_page_section' );

if ( ! $services_page_section || ! is_array( $services_page_section ) ) {
	return;
}

$services_page_title   = isset( $services_page_section['services_page_title'] ) ? trim( (string) $services_page_section['services_page_title'] ) : '';
$services_page_button1 = isset( $services_page_section['services_page_button1'] ) ? $services_page_section['services_page_button1'] : null;
$services_page_button2 = isset( $services_page_section['services_page_button2'] ) ? $services_page_section['services_page_button2'] : null;

$services_query = new WP_Query(
	array(
		'post_type'      => 'our-services',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>

<section class="services-page-section" aria-label="<?php esc_attr_e( 'Services page section', 'plumber' ); ?>">
	<div class="services-page-section__container">
		<div class="services-page-section__slider-wrap">
			<div class="services-page-section__slider-controls">
				<button class="services-page-section__arrow services-page-section__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous service', 'plumber' ); ?>"></button>
				<button class="services-page-section__arrow services-page-section__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next service', 'plumber' ); ?>"></button>
			</div>

			<?php if ( $services_query->have_posts() ) : ?>
				<div class="services-page-slider swiper">
					<div class="swiper-wrapper">
						<?php
						while ( $services_query->have_posts() ) :
							$services_query->the_post();

							$custom_title   = get_field( 'title' );
							$custom_image   = get_field( 'image' );
							$custom_content = get_field( 'content' );
							$display_title  = $custom_title ? $custom_title : get_the_title();
							$display_text   = $custom_content ? wp_strip_all_tags( (string) $custom_content ) : wp_strip_all_tags( (string) get_the_excerpt() );
							$display_text   = trim( preg_replace( '/\s+/', ' ', $display_text ) );

							if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
								if ( mb_strlen( $display_text ) > 150 ) {
									$display_text = rtrim( mb_substr( $display_text, 0, 150 ) ) . '...';
								}
							} elseif ( strlen( $display_text ) > 150 ) {
								$display_text = rtrim( substr( $display_text, 0, 150 ) ) . '...';
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
							<article class="swiper-slide services-page-card">
								<div class="services-page-card__media">
									<?php if ( $image_url ) : ?>
										<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
									<?php endif; ?>
								</div>

								<div class="services-page-card__content">
									<h3 class="services-page-card__title"><?php echo esc_html( $display_title ); ?></h3>
									<p class="services-page-card__text"><?php echo esc_html( $display_text ); ?></p>
									<a class="services-page-card__button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Learn More', 'plumber' ); ?></a>
								</div>
							</article>
							<?php
						endwhile;
						?>
					</div>
				</div>
			<?php else : ?>
				<p class="services-page-section__empty"><?php esc_html_e( 'No services found.', 'plumber' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>

		<div class="services-page-section__cta">
			<?php if ( $services_page_title ) : ?>
				<h2 class="services-page-section__cta-title"><?php echo esc_html( $services_page_title ); ?></h2>
			<?php endif; ?>

			<div class="services-page-section__cta-buttons">
				<?php if ( is_array( $services_page_button1 ) && ! empty( $services_page_button1['url'] ) ) : ?>
					<a class="services-page-section__cta-button services-page-section__cta-button--filled" href="<?php echo esc_url( $services_page_button1['url'] ); ?>" target="<?php echo esc_attr( ! empty( $services_page_button1['target'] ) ? $services_page_button1['target'] : '_self' ); ?>" <?php echo ( ! empty( $services_page_button1['target'] ) && '_blank' === $services_page_button1['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( ! empty( $services_page_button1['title'] ) ? $services_page_button1['title'] : __( 'Call us', 'plumber' ) ); ?>
					</a>
				<?php endif; ?>

				<?php if ( is_array( $services_page_button2 ) && ! empty( $services_page_button2['url'] ) ) : ?>
					<a class="services-page-section__cta-button services-page-section__cta-button--outline" href="<?php echo esc_url( $services_page_button2['url'] ); ?>" target="<?php echo esc_attr( ! empty( $services_page_button2['target'] ) ? $services_page_button2['target'] : '_self' ); ?>" <?php echo ( ! empty( $services_page_button2['target'] ) && '_blank' === $services_page_button2['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( ! empty( $services_page_button2['title'] ) ? $services_page_button2['title'] : __( 'Schedule service', 'plumber' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
