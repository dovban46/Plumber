<?php
/**
 * Taxonomy archive template.
 *
 * @package Plumber
 */

get_header();

$queried_term       = get_queried_object();
$service_taxonomies = get_object_taxonomies( 'our-services', 'names' );
$is_service_term    = $queried_term instanceof WP_Term && in_array( $queried_term->taxonomy, $service_taxonomies, true );
?>

<main id="primary" class="site-main">
	<?php if ( $is_service_term ) : ?>
		<?php
		$term_image_raw = get_field( 'image', $queried_term );
		if ( ! $term_image_raw ) {
			$term_image_raw = get_field( 'image', $queried_term->taxonomy . '_' . $queried_term->term_id );
		}

		$term_image_url = '';
		$term_image_alt = $queried_term->name;

		if ( is_array( $term_image_raw ) ) {
			$term_image_url = isset( $term_image_raw['url'] ) ? (string) $term_image_raw['url'] : '';
			if ( ! empty( $term_image_raw['alt'] ) ) {
				$term_image_alt = (string) $term_image_raw['alt'];
			}
		} elseif ( is_int( $term_image_raw ) || ctype_digit( (string) $term_image_raw ) ) {
			$term_image_id  = (int) $term_image_raw;
			$term_image_url = (string) wp_get_attachment_image_url( $term_image_id, 'full' );
			$meta_alt       = (string) get_post_meta( $term_image_id, '_wp_attachment_image_alt', true );
			if ( '' !== $meta_alt ) {
				$term_image_alt = $meta_alt;
			}
		} elseif ( is_string( $term_image_raw ) ) {
			$term_image_url = $term_image_raw;
		}

		$services_query = new WP_Query(
			array(
				'post_type'      => 'our-services',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'tax_query'      => array(
					array(
						'taxonomy' => $queried_term->taxonomy,
						'field'    => 'term_id',
						'terms'    => array( (int) $queried_term->term_id ),
					),
				),
			)
		);
		?>

		<section class="page-hero-section" aria-label="<?php esc_attr_e( 'Service category hero', 'plumber' ); ?>">
			<div class="page-hero-section__container">
				<div class="page-hero-section__media">
					<?php if ( $term_image_url ) : ?>
						<img class="page-hero-section__bg" src="<?php echo esc_url( $term_image_url ); ?>" alt="<?php echo esc_attr( $term_image_alt ); ?>">
					<?php endif; ?>
					<h1 class="page-hero-section__title"><?php echo esc_html( $queried_term->name ); ?></h1>
				</div>
			</div>
		</section>

		<section class="services-page-section" aria-label="<?php esc_attr_e( 'Service category posts', 'plumber' ); ?>">
			<div class="services-page-section__container">
				<div class="services-page-section__slider-wrap">
					<div class="services-page-section__head">
						<div class="services-page-section__slider-controls">
							<button class="services-page-section__arrow services-page-section__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous service', 'plumber' ); ?>">
								<span style="--arrow-icon:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/right-rounded.svg' ); ?>')" aria-hidden="true"></span>
							</button>
							<button class="services-page-section__arrow services-page-section__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next service', 'plumber' ); ?>">
								<span style="--arrow-icon:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/right-rounded.svg' ); ?>')" aria-hidden="true"></span>
							</button>
						</div>
					</div>

					<?php if ( $services_query->have_posts() ) : ?>
						<div class="services-page-slider swiper">
							<div class="swiper-wrapper">
								<?php
								while ( $services_query->have_posts() ) :
									$services_query->the_post();

									$custom_title   = get_field( 'title' );
									$custom_image   = get_field( 'image' );
									$custom_video   = get_field( 'video' );
									$custom_content = get_field( 'content' );
									$display_title  = $custom_title ? $custom_title : get_the_title();
									$display_text   = $custom_content ? wp_strip_all_tags( (string) $custom_content ) : wp_strip_all_tags( (string) get_the_excerpt() );
									$display_text   = trim( preg_replace( '/\s+/', ' ', $display_text ) );

									$video_url = '';
									if ( is_array( $custom_video ) ) {
										$video_url = isset( $custom_video['url'] ) ? (string) $custom_video['url'] : '';
									} elseif ( is_int( $custom_video ) || ctype_digit( (string) $custom_video ) ) {
										$video_url = (string) wp_get_attachment_url( (int) $custom_video );
									} elseif ( is_string( $custom_video ) ) {
										$video_url = trim( $custom_video );
									}

									$image_url = '';
									$image_alt = '';

									if ( ! $video_url ) {
										if ( is_array( $custom_image ) ) {
											$image_url = isset( $custom_image['url'] ) ? (string) $custom_image['url'] : '';
											$image_alt = isset( $custom_image['alt'] ) ? (string) $custom_image['alt'] : '';
										} elseif ( is_int( $custom_image ) || ctype_digit( (string) $custom_image ) ) {
											$image_id  = (int) $custom_image;
											$image_url = (string) wp_get_attachment_image_url( $image_id, 'large' );
											$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
										} elseif ( is_string( $custom_image ) ) {
											$image_url = $custom_image;
										}

										if ( ! $image_url && has_post_thumbnail() ) {
											$image_url = (string) get_the_post_thumbnail_url( get_the_ID(), 'large' );
										}

										if ( ! $image_alt ) {
											$image_alt = $display_title;
										}
									}
									?>
									<article class="swiper-slide services-page-card">
										<div class="services-page-card__media">
											<?php if ( $video_url ) : ?>
												<video src="<?php echo esc_url( $video_url ); ?>" autoplay loop muted playsinline></video>
											<?php elseif ( $image_url ) : ?>
												<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
											<?php endif; ?>
										</div>
										<div class="services-page-card__content">
											<h2 class="services-page-card__title"><?php echo esc_html( $display_title ); ?></h2>
											<p class="services-page-card__text"><?php echo esc_html( $display_text ); ?></p>
											<a class="services-page-card__button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Learn More', 'plumber' ); ?></a>
										</div>
									</article>
								<?php endwhile; ?>
							</div>
						</div>
					<?php else : ?>
						<p class="services-page-section__empty"><?php esc_html_e( 'No services found in this category.', 'plumber' ); ?></p>
					<?php endif; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php else : ?>
		<div class="site-content">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/content', get_post_type() ); ?>
				<?php endwhile; ?>
				<?php the_posts_navigation(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();
