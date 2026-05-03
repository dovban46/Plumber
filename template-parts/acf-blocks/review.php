<?php
/**
 * Reviews / testimonials block (flexible content layout: review).
 *
 * @package Plumber
 */

$review_section = get_sub_field( 'review_section' );

if ( ! $review_section || ! is_array( $review_section ) ) {
	return;
}

$review_title = isset( $review_section['review_title'] ) ? $review_section['review_title'] : '';
$items        = isset( $review_section['reviews_items'] ) && is_array( $review_section['reviews_items'] )
	? $review_section['reviews_items']
	: array();
$review_text  = isset( $review_section['review_text'] ) ? $review_section['review_text'] : '';

/** @var array<int, array<string, mixed>> $reviews_rows */
$reviews_rows = array();
foreach ( $items as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$item_name        = isset( $row['item_name'] ) ? trim( (string) $row['item_name'] ) : '';
	$item_text        = isset( $row['item_text'] ) ? trim( (string) $row['item_text'] ) : '';
	$item_description = isset( $row['item_description'] ) ? trim( (string) $row['item_description'] ) : '';
	$item_img         = isset( $row['item_img'] ) ? $row['item_img'] : null;
	$has_img          = false;
	if ( is_array( $item_img ) && ! empty( $item_img['url'] ) ) {
		$has_img = true;
	} elseif ( is_int( $item_img ) || ctype_digit( (string) $item_img ) ) {
		$has_img = wp_get_attachment_image_url( (int) $item_img, 'thumbnail' );
	} elseif ( is_string( $item_img ) && '' !== trim( $item_img ) ) {
		$has_img = true;
	}

	if ( ! $item_name && ! $item_text && ! $has_img ) {
		continue;
	}
	$reviews_rows[] = $row;
}

if ( empty( $reviews_rows ) ) {
	return;
}

$star_icon_url = get_template_directory_uri() . '/assets/images/Star.svg';
?>

<section class="reviews-section" data-reviews-autoplay-ms="4000" aria-label="<?php echo esc_attr( $review_title ? $review_title : __( 'Customer reviews', 'plumber' ) ); ?>">
	<div class="reviews-section__inner">
		<?php if ( $review_title ) : ?>
			<h2 class="reviews-section__title"><?php echo esc_html( $review_title ); ?></h2>
		<?php endif; ?>

		<div class="reviews-section__carousel-bleed" aria-hidden="false">
			<div class="reviews-section__carousel">
				<div class="reviews-section__viewport">
					<div class="reviews-section__track">
						<?php foreach ( $reviews_rows as $item ) : ?>
							<?php
							$item_name        = isset( $item['item_name'] ) ? trim( (string) $item['item_name'] ) : '';
							$item_description = isset( $item['item_description'] ) ? trim( (string) $item['item_description'] ) : '';
							$item_text        = isset( $item['item_text'] ) ? trim( (string) $item['item_text'] ) : '';
							$item_rating_raw  = isset( $item['item_rating'] ) ? $item['item_rating'] : 0;
							$item_rating      = min( 5, max( 0, (int) round( (float) $item_rating_raw ) ) );
							$item_img         = isset( $item['item_img'] ) ? $item['item_img'] : null;

							$img_url = '';
							$img_alt = '';

							if ( is_array( $item_img ) ) {
								$img_url = isset( $item_img['url'] ) ? (string) $item_img['url'] : '';
								$img_alt = isset( $item_img['alt'] ) ? (string) $item_img['alt'] : '';
							} elseif ( is_int( $item_img ) || ctype_digit( (string) $item_img ) ) {
								$img_url = (string) wp_get_attachment_image_url( (int) $item_img, 'thumbnail' );
								$img_alt = (string) get_post_meta( (int) $item_img, '_wp_attachment_image_alt', true );
							} elseif ( is_string( $item_img ) && $item_img !== '' ) {
								$img_url = $item_img;
							}

							if ( ! $item_name && ! $item_text && ! $img_url ) {
								continue;
							}
							?>
							<article class="reviews-card">
								<div class="reviews-card__header">
									<?php if ( $img_url ) : ?>
										<div class="reviews-card__avatar">
											<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ? $img_alt : $item_name ); ?>" width="55" height="55" loading="lazy" decoding="async">
										</div>
									<?php endif; ?>

									<div class="reviews-card__meta">
										<?php if ( $item_name ) : ?>
											<h3 class="reviews-card__name"><?php echo esc_html( $item_name ); ?></h3>
										<?php endif; ?>
										<?php if ( $item_description ) : ?>
											<p class="reviews-card__description"><?php echo esc_html( $item_description ); ?></p>
										<?php endif; ?>
									</div>
								</div>

								<?php if ( $item_rating > 0 ) : ?>
									<div class="reviews-card__rating" role="img" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'plumber' ), $item_rating ) ); ?>">
										<?php
										for ( $s = 0; $s < $item_rating; $s += 1 ) :
											?>
											<img class="reviews-card__star" src="<?php echo esc_url( $star_icon_url ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async">
										<?php endfor; ?>
									</div>
								<?php endif; ?>

								<?php if ( $item_text ) : ?>
									<p class="reviews-card__text"><?php echo esc_html( $item_text ); ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ( $review_text ) : ?>
			<div class="reviews-section__footer">
				<?php echo wp_kses_post( $review_text ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
