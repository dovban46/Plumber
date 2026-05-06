<?php
/**
 * Single template for Our Services post type.
 *
 * @package Plumber
 */

get_header();
?>

<main id="primary" class="site-main">
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$custom_title   = get_field( 'title' );
		$custom_image   = get_field( 'image' );
		$hero_image     = get_field( 'image_page_bg' );
		$custom_content = get_field( 'content' );
		$display_title  = $custom_title ? $custom_title : get_the_title();
		$call_label     = __( 'Call  +1 (347) 216-2800', 'plumber' );
		$call_href      = 'tel:13472162800';
		$review_section = array();
		$faq_section    = array();
		$contact_section = array();
		$service_form   = (string) get_field( 'from', get_the_ID() );
		$problems_group = get_field( 'problems_section', get_the_ID() );
		$top_title      = (string) get_field( 'top_title', get_the_ID() );
		$top_text       = (string) get_field( 'top_text', get_the_ID() );
		$text_1         = (string) get_field( 'text_1', get_the_ID() );
		$text_2         = (string) get_field( 'text_2', get_the_ID() );
		$text_3         = (string) get_field( 'text_3', get_the_ID() );
		$icon_1         = get_field( 'icon_1', get_the_ID() );
		$icon_2         = get_field( 'icon_2', get_the_ID() );
		$icon_3         = get_field( 'icon_3', get_the_ID() );
		$default_step_icons = array(
			get_template_directory_uri() . '/assets/images/phone-in-talk-outline-sharp.svg',
			get_template_directory_uri() . '/assets/images/free.svg',
			get_template_directory_uri() . '/assets/images/light_verified_2.svg',
		);

		$image_url = '';
		$image_alt = '';
		$hero_image_url = '';
		$hero_image_alt = '';

		if ( is_array( $hero_image ) ) {
			$hero_image_url = isset( $hero_image['url'] ) ? $hero_image['url'] : '';
			$hero_image_alt = isset( $hero_image['alt'] ) ? $hero_image['alt'] : '';
		} elseif ( is_int( $hero_image ) || ctype_digit( (string) $hero_image ) ) {
			$hero_image_url = wp_get_attachment_image_url( (int) $hero_image, 'full' );
			$hero_image_alt = (string) get_post_meta( (int) $hero_image, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $hero_image ) ) {
			$hero_image_url = $hero_image;
		}

		if ( ! $hero_image_url && has_post_thumbnail() ) {
			$hero_image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		}

		if ( ! $hero_image_alt ) {
			$hero_image_alt = $display_title;
		}

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

		$resolve_icon = static function ( $raw_icon ) use ( $display_title ) {
			$url = '';
			$alt = $display_title;
			if ( is_array( $raw_icon ) ) {
				$url = isset( $raw_icon['url'] ) ? (string) $raw_icon['url'] : '';
				$alt = isset( $raw_icon['alt'] ) && '' !== trim( (string) $raw_icon['alt'] ) ? (string) $raw_icon['alt'] : $alt;
			} elseif ( is_int( $raw_icon ) || ctype_digit( (string) $raw_icon ) ) {
				$url = (string) wp_get_attachment_image_url( (int) $raw_icon, 'full' );
				$meta_alt = (string) get_post_meta( (int) $raw_icon, '_wp_attachment_image_alt', true );
				if ( '' !== $meta_alt ) {
					$alt = $meta_alt;
				}
			} elseif ( is_string( $raw_icon ) ) {
				$url = $raw_icon;
			}
			return array(
				'url' => $url,
				'alt' => $alt,
			);
		};

		$steps = array(
			array_merge( array( 'text' => trim( $text_1 ) ), $resolve_icon( $icon_1 ) ),
			array_merge( array( 'text' => trim( $text_2 ) ), $resolve_icon( $icon_2 ) ),
			array_merge( array( 'text' => trim( $text_3 ) ), $resolve_icon( $icon_3 ) ),
		);
		foreach ( $steps as $index => $step ) {
			if ( '' === (string) $step['url'] && isset( $default_step_icons[ $index ] ) ) {
				$steps[ $index ]['url'] = $default_step_icons[ $index ];
			}
		}

		$problems_map_raw = is_array( $problems_group ) ? ( $problems_group['map'] ?? null ) : null;
		$problems_items   = is_array( $problems_group ) && isset( $problems_group['problems'] ) && is_array( $problems_group['problems'] )
			? $problems_group['problems']
			: array();
		$problems_map_url = '';
		$problems_map_alt = __( 'Service area map', 'plumber' );

		if ( is_array( $problems_map_raw ) ) {
			$problems_map_url = isset( $problems_map_raw['url'] ) ? (string) $problems_map_raw['url'] : '';
			if ( ! empty( $problems_map_raw['alt'] ) ) {
				$problems_map_alt = (string) $problems_map_raw['alt'];
			}
		} elseif ( is_int( $problems_map_raw ) || ctype_digit( (string) $problems_map_raw ) ) {
			$problems_map_url = (string) wp_get_attachment_image_url( (int) $problems_map_raw, 'full' );
			$problems_alt_raw = (string) get_post_meta( (int) $problems_map_raw, '_wp_attachment_image_alt', true );
			if ( '' !== $problems_alt_raw ) {
				$problems_map_alt = $problems_alt_raw;
			}
		} elseif ( is_string( $problems_map_raw ) ) {
			$problems_map_url = $problems_map_raw;
		}

		$front_page_id = (int) get_option( 'page_on_front' );
		if ( $front_page_id > 0 ) {
			$front_blocks = get_field( 'blocks', $front_page_id );
			if ( is_array( $front_blocks ) ) {
				foreach ( $front_blocks as $block ) {
					if ( ! is_array( $block ) ) {
						continue;
					}
					if ( 'review' !== (string) ( $block['acf_fc_layout'] ?? '' ) ) {
						continue;
					}
					if ( isset( $block['review_section'] ) && is_array( $block['review_section'] ) ) {
						$review_section = $block['review_section'];
					}
				}

				foreach ( $front_blocks as $block ) {
					if ( ! is_array( $block ) ) {
						continue;
					}
					if ( 'contact' !== (string) ( $block['acf_fc_layout'] ?? '' ) ) {
						continue;
					}
					if ( isset( $block['contact_section'] ) && is_array( $block['contact_section'] ) ) {
						$contact_section = $block['contact_section'];
						break;
					}
				}
			}
		}

		$current_blocks = get_field( 'blocks', get_the_ID() );
		if ( is_array( $current_blocks ) ) {
			foreach ( $current_blocks as $block ) {
				if ( ! is_array( $block ) ) {
					continue;
				}
				if ( 'faq' !== (string) ( $block['acf_fc_layout'] ?? '' ) ) {
					continue;
				}
				if ( isset( $block['faq_section'] ) && is_array( $block['faq_section'] ) ) {
					$faq_section = $block['faq_section'];
					break;
				}
			}
		}
		?>

		<section class="page-hero-section single-our-services-hero" aria-label="<?php esc_attr_e( 'Service hero', 'plumber' ); ?>">
			<div class="page-hero-section__container">
				<div class="page-hero-section__media">
					<?php if ( $hero_image_url ) : ?>
						<img class="page-hero-section__bg" src="<?php echo esc_url( $hero_image_url ); ?>" alt="<?php echo esc_attr( $hero_image_alt ); ?>">
					<?php endif; ?>
					<div class="single-our-services-hero__content">
						<h1 class="page-hero-section__title"><?php echo esc_html( $display_title ); ?></h1>
						<a class="single-our-services-hero__button" href="<?php echo esc_url( $call_href ); ?>">
							<?php echo esc_html( $call_label ); ?>
						</a>
					</div>
				</div>
			</div>
		</section>

		<?php if ( '' !== trim( $top_title ) || '' !== trim( $top_text ) || '' !== trim( $text_1 ) || '' !== trim( $text_2 ) || '' !== trim( $text_3 ) ) : ?>
			<section class="single-our-services-top">
				<div class="single-our-services-top__container">
					<?php if ( '' !== trim( $top_title ) ) : ?>
						<h2 class="single-our-services-top__title"><?php echo esc_html( $top_title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== trim( $top_text ) ) : ?>
						<p class="single-our-services-top__text"><?php echo esc_html( $top_text ); ?></p>
					<?php endif; ?>
					<div class="single-our-services-top__steps">
						<?php foreach ( $steps as $index => $step ) : ?>
							<?php if ( '' === $step['text'] ) { continue; } ?>
							<div class="single-our-services-top__step">
								<span class="single-our-services-top__icon-wrap">
									<?php if ( '' !== $step['url'] ) : ?>
										<img src="<?php echo esc_url( $step['url'] ); ?>" alt="<?php echo esc_attr( $step['alt'] ); ?>" width="20" height="20" loading="lazy" decoding="async">
									<?php endif; ?>
								</span>
								<span class="single-our-services-top__step-text"><?php echo esc_html( $step['text'] ); ?></span>
							</div>
							<?php if ( $index < 2 ) : ?>
								<span class="single-our-services-top__divider" aria-hidden="true"></span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $problems_items ) || '' !== $problems_map_url ) : ?>
			<section class="single-our-services-problems">
				<div class="single-our-services-problems__container">
					<div class="single-our-services-problems__content">
						<h2 class="single-our-services-problems__title"><?php esc_html_e( 'Problems This Service Solves', 'plumber' ); ?></h2>
						<?php if ( ! empty( $problems_items ) ) : ?>
							<ul class="single-our-services-problems__list">
								<?php foreach ( $problems_items as $problem ) : ?>
									<?php $problem_name = isset( $problem['problem_name'] ) ? trim( (string) $problem['problem_name'] ) : ''; ?>
									<?php if ( '' === $problem_name ) { continue; } ?>
									<li class="single-our-services-problems__item">
										<span class="single-our-services-problems__item-icon" aria-hidden="true">
											<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Vector.svg' ); ?>" alt="" width="32" height="32" loading="lazy" decoding="async">
										</span>
										<span class="single-our-services-problems__item-text"><?php echo esc_html( $problem_name ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<?php if ( '' !== $problems_map_url ) : ?>
						<div class="single-our-services-problems__map-wrap">
							<img class="single-our-services-problems__map" src="<?php echo esc_url( $problems_map_url ); ?>" alt="<?php echo esc_attr( $problems_map_alt ); ?>" loading="lazy" decoding="async">
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php
		if ( ! empty( $review_section ) ) {
			get_template_part(
				'template-parts/acf-blocks/review',
				null,
				array(
					'review_section' => $review_section,
				)
			);
		}

		if ( ! empty( $faq_section ) ) {
			get_template_part(
				'template-parts/acf-blocks/faq',
				null,
				array(
					'faq_section' => $faq_section,
				)
			);
		}

		if ( ! empty( $contact_section ) ) :
			$contact_items = isset( $contact_section['contact_items'] ) && is_array( $contact_section['contact_items'] )
				? $contact_section['contact_items']
				: array();
			?>
			<section class="contact-section contact-section--services" aria-label="<?php esc_attr_e( 'Contact section', 'plumber' ); ?>">
				<div class="contact-section__container">
					<h2 class="contact-section__title"><?php esc_html_e( 'Contact & Request Form', 'plumber' ); ?></h2>

					<div class="contact-section__content">
						<div class="contact-section__form">
							<?php if ( $service_form ) : ?>
								<div class="contact-form-wrapper">
									<?php echo do_shortcode( $service_form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="contact-section__service-items" role="list">
							<?php foreach ( $contact_items as $item ) : ?>
								<?php
								$item_title       = isset( $item['item_title'] ) ? trim( (string) $item['item_title'] ) : '';
								$item_text        = isset( $item['item_text'] ) ? trim( (string) $item['item_text'] ) : '';
								$item_text_link   = isset( $item['item_text_link'] ) ? trim( (string) $item['item_text_link'] ) : '';
								$item_bottom_text = isset( $item['item_bottom_text'] ) ? trim( (string) $item['item_bottom_text'] ) : '';
								?>
								<article class="contact-item" role="listitem">
									<?php if ( $item_title ) : ?>
										<h3 class="contact-item__title"><?php echo esc_html( $item_title ); ?></h3>
									<?php endif; ?>

									<?php if ( $item_text ) : ?>
										<?php if ( $item_text_link ) : ?>
											<a class="contact-item__text contact-item__text-link" href="<?php echo esc_url( $item_text_link ); ?>">
												<?php echo esc_html( $item_text ); ?>
											</a>
										<?php else : ?>
											<p class="contact-item__text"><?php echo esc_html( $item_text ); ?></p>
										<?php endif; ?>
									<?php endif; ?>

									<?php if ( $item_bottom_text ) : ?>
										<p class="contact-item__bottom-text"><?php echo esc_html( $item_bottom_text ); ?></p>
									<?php endif; ?>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>
		?>

		<?php
	endwhile;
endif;
?>
</main>
<?php
get_footer();
