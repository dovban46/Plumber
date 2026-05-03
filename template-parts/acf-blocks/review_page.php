<?php
/**
 * Reviews page section (flexible layout: review_page).
 *
 * @package Plumber
 */

if ( ! function_exists( 'plumber_normalize_review_rating_value' ) ) {
	/**
	 * Normalize rating value from custom field/range into 1..5.
	 *
	 * @param mixed $raw Raw value.
	 * @return int
	 */
	function plumber_normalize_review_rating_value( $raw ) {
		$value = (float) $raw;

		if ( $value <= 0 ) {
			return 0;
		}

		// If range field stores 0..100 - convert to 0..5 scale.
		if ( $value > 5 ) {
			$value = $value / 20;
		}

		return (int) max( 1, min( 5, round( $value ) ) );
	}
}

$section = get_sub_field( 'review_page_section' );
if ( ! is_array( $section ) ) {
	return;
}

$section_title = isset( $section['review_page_title'] ) ? trim( (string) $section['review_page_title'] ) : '';

$form_status = array(
	'type'    => '',
	'message' => '',
);

if ( isset( $_GET['review_submitted'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['review_submitted'] ) ) ) {
	$form_status['type']    = 'success';
	$form_status['message'] = __( 'Thank you! Your review has been submitted.', 'plumber' );
}

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['plumber_review_submit'] ) ) {
	$nonce_ok = isset( $_POST['plumber_review_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plumber_review_nonce'] ) ), 'plumber_review_submit' );

	if ( ! $nonce_ok ) {
		$form_status['type']    = 'error';
		$form_status['message'] = __( 'Security check failed. Please try again.', 'plumber' );
	} else {
		$title    = isset( $_POST['review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['review_title'] ) ) : '';
		$text     = isset( $_POST['review_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_text'] ) ) : '';
		$name     = isset( $_POST['review_name'] ) ? sanitize_text_field( wp_unslash( $_POST['review_name'] ) ) : '';
		$email    = isset( $_POST['review_email'] ) ? sanitize_email( wp_unslash( $_POST['review_email'] ) ) : '';
		$location = isset( $_POST['review_location'] ) ? sanitize_text_field( wp_unslash( $_POST['review_location'] ) ) : '';
		$rating   = isset( $_POST['review_rating'] ) ? plumber_normalize_review_rating_value( wp_unslash( $_POST['review_rating'] ) ) : 0;

		if ( '' === $title || '' === $text || '' === $name || '' === $email || $rating < 1 ) {
			$form_status['type']    = 'error';
			$form_status['message'] = __( 'Please fill all required fields and select a rating.', 'plumber' );
		} else {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'review',
					'post_title'   => $title,
					'post_content' => $text,
					'post_status'  => 'publish',
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				$form_status['type']    = 'error';
				$form_status['message'] = __( 'Unable to submit review right now. Please try later.', 'plumber' );
			} else {
				update_post_meta( $post_id, 'text', $text );
				update_post_meta( $post_id, 'name', $name );
				update_post_meta( $post_id, 'email', $email );
				update_post_meta( $post_id, 'rating', $rating );
				update_post_meta( $post_id, 'location', $location );

				// Post-Redirect-Get: avoid duplicate form submission prompt on refresh.
				$redirect_url = add_query_arg( 'review_submitted', '1', get_permalink() );
				wp_safe_redirect( $redirect_url . '#review-form' );
				exit;
			}
		}
	}
}

$all_reviews = get_posts(
	array(
		'post_type'      => 'review',
		'post_status'    => 'publish',
		'numberposts'    => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'suppress_filters' => false,
	)
);

$rating_counts = array(
	1 => 0,
	2 => 0,
	3 => 0,
	4 => 0,
	5 => 0,
);
$rating_sum   = 0;
$rating_total = 0;

foreach ( $all_reviews as $review_post ) {
	$rating_raw = get_post_meta( $review_post->ID, 'rating', true );
	if ( '' === $rating_raw && function_exists( 'get_field' ) ) {
		$rating_raw = get_field( 'rating', $review_post->ID );
	}

	$rating = plumber_normalize_review_rating_value( $rating_raw );
	if ( $rating < 1 ) {
		continue;
	}

	$rating_counts[ $rating ] += 1;
	$rating_sum             += $rating;
	$rating_total           += 1;
}

$average_rating = $rating_total > 0 ? round( $rating_sum / $rating_total, 1 ) : 0;

$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$reviews_query = new WP_Query(
	array(
		'post_type'           => 'review',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'paged'               => $paged,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
	)
);

$star_icon_url = get_template_directory_uri() . '/assets/images/Star.svg';
?>

<section class="review-page-section" aria-label="<?php echo esc_attr( $section_title ? $section_title : __( 'Rating and reviews', 'plumber' ) ); ?>">
	<div class="review-page-section__inner">
		<div class="review-page-section__grid">
			<div class="review-page-section__left">
				<?php if ( $section_title ) : ?>
					<h2 class="review-page-section__title"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>

				<div class="review-page-stats" aria-label="<?php esc_attr_e( 'Review statistics', 'plumber' ); ?>">
					<div class="review-page-stats__average-line">
						<p class="review-page-stats__average">
							<?php echo esc_html( $average_rating ); ?>
							<span><?php esc_html_e( 'stars', 'plumber' ); ?></span>
						</p>
						<div class="review-page-stars review-page-stars--static" aria-hidden="true">
							<?php for ( $s = 1; $s <= 5; $s += 1 ) : ?>
								<span class="review-page-stars__star <?php echo esc_attr( $s <= round( $average_rating ) ? 'is-active' : '' ); ?>" style="--star-icon:url('<?php echo esc_url( $star_icon_url ); ?>')"></span>
							<?php endfor; ?>
						</div>
					</div>

					<div class="review-page-stats__rows">
						<?php for ( $star = 5; $star >= 1; $star -= 1 ) : ?>
							<?php
							$count   = (int) $rating_counts[ $star ];
							$percent = $rating_total > 0 ? ( $count / $rating_total ) * 100 : 0;
							?>
							<div class="review-page-stats__row">
								<span class="review-page-stats__row-stars" aria-hidden="true">
									<?php for ( $s = 1; $s <= 5; $s += 1 ) : ?>
										<span class="review-page-stars__star <?php echo esc_attr( $s <= $star ? 'is-active' : '' ); ?>" style="--star-icon:url('<?php echo esc_url( $star_icon_url ); ?>')"></span>
									<?php endfor; ?>
								</span>
								<span class="review-page-stats__row-track" aria-hidden="true">
									<span class="review-page-stats__row-fill" style="width: <?php echo esc_attr( number_format( $percent, 2, '.', '' ) ); ?>%"></span>
								</span>
								<span class="review-page-stats__row-count"><?php echo esc_html( $count ); ?></span>
							</div>
						<?php endfor; ?>
					</div>
				</div>

			</div>

			<div class="review-page-section__right">
				<?php if ( $reviews_query->have_posts() ) : ?>
					<div class="review-page-list">
						<?php
						while ( $reviews_query->have_posts() ) :
							$reviews_query->the_post();
							$post_id = get_the_ID();

							$name = (string) get_post_meta( $post_id, 'name', true );
							if ( '' === $name && function_exists( 'get_field' ) ) {
								$name = (string) get_field( 'name', $post_id );
							}

							$email = (string) get_post_meta( $post_id, 'email', true );
							if ( '' === $email && function_exists( 'get_field' ) ) {
								$email = (string) get_field( 'email', $post_id );
							}

							$location = (string) get_post_meta( $post_id, 'location', true );
							if ( '' === $location && function_exists( 'get_field' ) ) {
								$location = (string) get_field( 'location', $post_id );
							}

							$rating_raw = get_post_meta( $post_id, 'rating', true );
							if ( '' === $rating_raw && function_exists( 'get_field' ) ) {
								$rating_raw = get_field( 'rating', $post_id );
							}
							$rating = plumber_normalize_review_rating_value( $rating_raw );
							?>
							<article class="review-page-card">
								<div class="review-page-card__layout">
									<div class="review-page-card__meta">
										<?php if ( '' !== trim( $name ) ) : ?>
											<p class="review-page-card__name"><?php echo esc_html( $name ); ?></p>
										<?php endif; ?>
										<?php if ( '' !== trim( $location ) ) : ?>
											<p class="review-page-card__location"><?php echo esc_html( $location ); ?></p>
										<?php endif; ?>
										<p class="review-page-card__date"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></p>
									</div>

									<div class="review-page-card__content">
										<div class="review-page-stars review-page-stars--static" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'plumber' ), $rating ) ); ?>">
											<?php for ( $s = 1; $s <= 5; $s += 1 ) : ?>
												<span class="review-page-stars__star <?php echo esc_attr( $s <= $rating ? 'is-active' : '' ); ?>" style="--star-icon:url('<?php echo esc_url( $star_icon_url ); ?>')"></span>
											<?php endfor; ?>
										</div>

										<?php if ( '' !== trim( get_the_title() ) ) : ?>
											<h3 class="review-page-card__title"><?php the_title(); ?></h3>
										<?php endif; ?>
										<?php if ( '' !== trim( get_the_content() ) ) : ?>
											<div class="review-page-card__text">
												<?php echo wp_kses_post( wpautop( get_the_content() ) ); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>

					<?php
					$pagination = paginate_links(
						array(
							'total'      => (int) $reviews_query->max_num_pages,
							'current'    => $paged,
							'prev_text'  => '&lt;',
							'next_text'  => '&gt;',
							'type'       => 'list',
						)
					);
					if ( $pagination ) :
						?>
						<nav class="review-page-pagination" aria-label="<?php esc_attr_e( 'Reviews pagination', 'plumber' ); ?>">
							<?php echo wp_kses_post( $pagination ); ?>
						</nav>
					<?php endif; ?>
				<?php else : ?>
					<p class="review-page-empty"><?php esc_html_e( 'No reviews yet.', 'plumber' ); ?></p>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>

			<div class="review-page-form-wrap">
				<h3 class="review-page-form-wrap__title"><?php esc_html_e( 'Tell us, how was your experience with Local Expert Plumbing Services?', 'plumber' ); ?></h3>
				<p class="review-page-form-wrap__subtitle"><?php esc_html_e( 'We’d love to hear from you!', 'plumber' ); ?></p>

				<?php if ( $form_status['message'] ) : ?>
					<div class="review-page-form__notice is-<?php echo esc_attr( $form_status['type'] ); ?>">
						<?php echo esc_html( $form_status['message'] ); ?>
					</div>
				<?php endif; ?>

				<form class="review-page-form" id="review-form" method="post" action="">
					<?php wp_nonce_field( 'plumber_review_submit', 'plumber_review_nonce' ); ?>
					<input type="hidden" name="plumber_review_submit" value="1">
					<input type="hidden" name="review_rating" class="review-page-form__rating-input" value="0">

					<div class="review-page-stars review-page-stars--input" data-review-stars>
						<?php for ( $s = 1; $s <= 5; $s += 1 ) : ?>
							<button type="button" class="review-page-stars__star-button" data-star-value="<?php echo esc_attr( $s ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Rate %d stars', 'plumber' ), $s ) ); ?>">
								<span class="review-page-stars__star" style="--star-icon:url('<?php echo esc_url( $star_icon_url ); ?>')"></span>
							</button>
						<?php endfor; ?>
					</div>

					<label class="review-page-form__field">
						<span><?php esc_html_e( 'Title of your review', 'plumber' ); ?></span>
						<input type="text" name="review_title" required placeholder="<?php esc_attr_e( 'Summarize your review or highlight an interesting detail', 'plumber' ); ?>">
					</label>
					<label class="review-page-form__field">
						<span><?php esc_html_e( 'Your review', 'plumber' ); ?></span>
						<textarea name="review_text" rows="4" required placeholder="<?php esc_attr_e( 'Tell people your review', 'plumber' ); ?>"></textarea>
					</label>
					<label class="review-page-form__field">
						<span><?php esc_html_e( 'Your name', 'plumber' ); ?></span>
						<input type="text" name="review_name" required placeholder="<?php esc_attr_e( 'Enter your name', 'plumber' ); ?>">
					</label>
					<label class="review-page-form__field">
						<span><?php esc_html_e( 'Your email', 'plumber' ); ?></span>
						<input type="email" name="review_email" required placeholder="<?php esc_attr_e( 'Tell us your email', 'plumber' ); ?>">
					</label>
					<label class="review-page-form__field">
						<span><?php esc_html_e( 'Your city/country', 'plumber' ); ?></span>
						<input type="text" name="review_location" placeholder="<?php esc_attr_e( 'Chicago, USA', 'plumber' ); ?>">
					</label>

					<button type="submit" class="review-page-form__submit"><?php esc_html_e( 'Submit Review', 'plumber' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</section>
