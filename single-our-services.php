<?php
/**
 * Single template for Our Services post type.
 *
 * @package Plumber
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$custom_title   = get_field( 'title' );
		$custom_image   = get_field( 'image' );
		$custom_content = get_field( 'content' );
		$display_title  = $custom_title ? $custom_title : get_the_title();

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

		<section class="single-our-services">
			<div class="single-our-services__container">
				<article class="single-our-services__card">
					<?php if ( $image_url ) : ?>
						<div class="single-our-services__thumb">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
						</div>
					<?php endif; ?>

					<h1 class="single-our-services__title"><?php echo esc_html( $display_title ); ?></h1>

					<div class="single-our-services__content">
						<?php if ( $custom_content ) : ?>
							<?php echo wp_kses_post( wpautop( $custom_content ) ); ?>
						<?php else : ?>
							<?php the_content(); ?>
						<?php endif; ?>
					</div>
				</article>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
