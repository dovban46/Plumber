<?php
/**
 * Single template for blog posts.
 *
 * @package Plumber
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$post_id      = get_the_ID();
		$post_title   = trim( (string) get_the_title() );
		$hero_video   = function_exists( 'get_field' ) ? get_field( 'video', $post_id ) : '';
		$hero_vid_url = '';
		if ( is_array( $hero_video ) ) {
			$hero_vid_url = isset( $hero_video['url'] ) ? $hero_video['url'] : '';
		} elseif ( is_int( $hero_video ) || ctype_digit( (string) $hero_video ) ) {
			$hero_vid_url = wp_get_attachment_url( (int) $hero_video );
		} elseif ( is_string( $hero_video ) ) {
			$hero_vid_url = trim( $hero_video );
		}

		$thumb_id     = get_post_thumbnail_id( $post_id );
		$hero_img_url = ( ! $hero_vid_url && $thumb_id ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : '';
		$hero_img_alt = $thumb_id ? (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '';
		if ( ! $hero_img_alt ) {
			$hero_img_alt = $post_title;
		}

		$bottom_text = function_exists( 'get_field' ) ? trim( (string) get_field( 'bottom_text', $post_id ) ) : '';
		$button_1    = function_exists( 'get_field' ) ? get_field( 'button_1', $post_id ) : null;
		$button_2    = function_exists( 'get_field' ) ? get_field( 'button_2', $post_id ) : null;

		$default_contact_page = get_page_by_path( 'contact' );
		$default_contact_url  = $default_contact_page instanceof WP_Post ? get_permalink( $default_contact_page ) : home_url( '/contact/' );
		$blog_page            = get_page_by_path( 'blog' );
		$blog_page_url        = $blog_page instanceof WP_Post ? get_permalink( $blog_page ) : home_url( '/blog/' );

		$cta_button_1 = ( is_array( $button_1 ) && ! empty( $button_1['url'] ) )
			? $button_1
			: array(
				'title'  => __( 'Call +1 (347) 216-2800', 'plumber' ),
				'url'    => 'tel:+13472162800',
				'target' => '',
			);

		$cta_button_2 = ( is_array( $button_2 ) && ! empty( $button_2['url'] ) )
			? $button_2
			: array(
				'title'  => __( 'Book Now', 'plumber' ),
				'url'    => $default_contact_url,
				'target' => '',
			);

		$other_posts = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => -1,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => true,
			)
		);
		?>
		<main id="primary" class="site-main">
			<section class="single-blog-hero" aria-label="<?php esc_attr_e( 'Post hero', 'plumber' ); ?>">
				<div class="single-blog-hero__container">
					<div class="single-blog-hero__media">
						<?php if ( $hero_vid_url ) : ?>
							<video class="single-blog-hero__bg" src="<?php echo esc_url( $hero_vid_url ); ?>" autoplay loop muted playsinline></video>
						<?php elseif ( $hero_img_url ) : ?>
							<img class="single-blog-hero__bg" src="<?php echo esc_url( $hero_img_url ); ?>" alt="<?php echo esc_attr( $hero_img_alt ); ?>">
						<?php endif; ?>
						<h1 class="single-blog-hero__title"><?php echo esc_html( $post_title ); ?></h1>
					</div>
				</div>
			</section>

			<section class="single-blog-content">
				<div class="single-blog-content__container">
					<a class="single-blog-content__back" href="<?php echo esc_url( $blog_page_url ); ?>">
						<?php esc_html_e( '← Back to Blog', 'plumber' ); ?>
					</a>

					<p class="single-blog-content__date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>

					<div class="single-blog-content__body">
						<?php the_content(); ?>
					</div>
				</div>
			</section>

			<?php if ( ! empty( $other_posts ) ) : ?>
				<section class="single-blog-other">
					<div class="single-blog-other__container">
						<div class="single-blog-other__head">
							<h2 class="single-blog-other__title"><?php esc_html_e( 'Other Articles', 'plumber' ); ?></h2>
						</div>

						<div class="single-blog-other__slider swiper">
							<div class="swiper-wrapper">
								<?php foreach ( $other_posts as $item ) : ?>
									<?php
									$item_id      = (int) $item->ID;
									$item_title   = trim( (string) get_the_title( $item_id ) );
									$item_video   = function_exists( 'get_field' ) ? get_field( 'video', $item_id ) : '';
									$item_vid_url = '';
									if ( is_array( $item_video ) ) {
										$item_vid_url = isset( $item_video['url'] ) ? $item_video['url'] : '';
									} elseif ( is_int( $item_video ) || ctype_digit( (string) $item_video ) ) {
										$item_vid_url = wp_get_attachment_url( (int) $item_video );
									} elseif ( is_string( $item_video ) ) {
										$item_vid_url = trim( $item_video );
									}

									$item_thumb   = get_post_thumbnail_id( $item_id );
									$item_img_url = '';
									$item_img_alt = '';

									if ( ! $item_vid_url && $item_thumb ) {
										$item_mime    = (string) get_post_mime_type( $item_thumb );
										$item_is_gif  = ( false !== strpos( strtolower( $item_mime ), 'gif' ) );
										$item_size    = ( $item_is_gif && $item_id === $post_id ) ? 'full' : 'large';
										$item_img_url = (string) get_the_post_thumbnail_url( $item_id, $item_size );
										$item_img_alt = (string) get_post_meta( $item_thumb, '_wp_attachment_image_alt', true );
									}

									if ( ! $item_img_alt ) {
										$item_img_alt = $item_title;
									}
									?>
									<article class="swiper-slide single-blog-other-card">
										<p class="single-blog-other-card__date"><?php echo esc_html( get_the_date( 'F j, Y', $item_id ) ); ?></p>
										<a class="single-blog-other-card__media" href="<?php echo esc_url( get_permalink( $item_id ) ); ?>">
											<?php if ( $item_vid_url ) : ?>
												<video src="<?php echo esc_url( $item_vid_url ); ?>" autoplay loop muted playsinline></video>
											<?php elseif ( $item_img_url ) : ?>
												<img src="<?php echo esc_url( $item_img_url ); ?>" alt="<?php echo esc_attr( $item_img_alt ); ?>" loading="lazy" decoding="async">
											<?php endif; ?>
											<h3 class="single-blog-other-card__title"><?php echo esc_html( $item_title ); ?></h3>
										</a>
									</article>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="single-blog-other__controls">
							<button type="button" class="single-blog-other__arrow single-blog-other__arrow--prev" aria-label="<?php esc_attr_e( 'Previous article', 'plumber' ); ?>">
								<span style="--arrow-icon:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/right-rounded.svg' ); ?>')" aria-hidden="true"></span>
							</button>
							<button type="button" class="single-blog-other__arrow single-blog-other__arrow--next" aria-label="<?php esc_attr_e( 'Next article', 'plumber' ); ?>">
								<span style="--arrow-icon:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/right-rounded.svg' ); ?>')" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<section class="single-blog-cta services-page-section__cta">
				<div class="single-blog-cta__container">
					<?php if ( '' !== $bottom_text ) : ?>
						<h2 class="services-page-section__cta-title"><?php echo esc_html( $bottom_text ); ?></h2>
					<?php endif; ?>
					<div class="services-page-section__cta-buttons">
						<a class="services-page-section__cta-button services-page-section__cta-button--filled" href="<?php echo esc_url( $cta_button_1['url'] ); ?>" target="<?php echo esc_attr( ! empty( $cta_button_1['target'] ) ? $cta_button_1['target'] : '_self' ); ?>" <?php echo ( ! empty( $cta_button_1['target'] ) && '_blank' === $cta_button_1['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
							<span class="services-page-section__cta-text services-page-section__cta-text--desktop"><?php echo esc_html( ! empty( $cta_button_1['title'] ) ? $cta_button_1['title'] : __( 'Call +1 (347) 216-2800', 'plumber' ) ); ?></span>
							<span class="services-page-section__cta-text services-page-section__cta-text--mobile"><?php esc_html_e( 'Residential', 'plumber' ); ?></span>
						</a>
						<a class="services-page-section__cta-button services-page-section__cta-button--outline" href="<?php echo esc_url( $cta_button_2['url'] ); ?>" target="<?php echo esc_attr( ! empty( $cta_button_2['target'] ) ? $cta_button_2['target'] : '_self' ); ?>" <?php echo ( ! empty( $cta_button_2['target'] ) && '_blank' === $cta_button_2['target'] ) ? 'rel="noopener noreferrer"' : ''; ?>>
							<?php echo esc_html( ! empty( $cta_button_2['title'] ) ? $cta_button_2['title'] : __( 'Book Now', 'plumber' ) ); ?>
						</a>
					</div>
				</div>
			</section>
		</main>
		<?php
	endwhile;
endif;

get_footer();
