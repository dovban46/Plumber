<?php
/**
 * Blog page section (flexible layout: blog_page).
 *
 * @package Plumber
 */

$section = get_sub_field( 'blog_page_section' );
if ( ! is_array( $section ) ) {
	return;
}

$section_title = isset( $section['blog_page_title'] ) ? trim( (string) $section['blog_page_title'] ) : '';
$icon_url      = get_template_directory_uri() . '/assets/images/right-rounded.svg';

$posts_query = new WP_Query(
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

<section class="blog-page-section" aria-label="<?php echo esc_attr( $section_title ? $section_title : __( 'Latest articles', 'plumber' ) ); ?>">
	<div class="blog-page-section__inner">
		<?php if ( $section_title ) : ?>
			<h2 class="blog-page-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<?php if ( $posts_query->have_posts() ) : ?>
			<div class="blog-page-list">
				<?php
				while ( $posts_query->have_posts() ) :
					$posts_query->the_post();
					$post_id = get_the_ID();

						$title_raw = trim( (string) get_the_title() );
						$title     = '' !== $title_raw ? $title_raw : __( 'Untitled post', 'plumber' );

						$thumb_id  = get_post_thumbnail_id( $post_id );
						$image_url = '';
						$image_alt = '';
						if ( $thumb_id ) {
							$mime_type = (string) get_post_mime_type( $thumb_id );
							$is_gif    = ( false !== strpos( strtolower( $mime_type ), 'gif' ) );
							$image_url = get_the_post_thumbnail_url( $post_id, $is_gif ? 'full' : 'large' );
							$image_alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
						}
						if ( ! $image_alt ) {
							$image_alt = $title;
						}

						$description = '';
						if ( function_exists( 'get_field' ) ) {
							$description = trim( (string) get_field( 'description', $post_id ) );
						}
						if ( '' === $description ) {
							$description = trim( wp_strip_all_tags( (string) get_the_excerpt( $post_id ) ) );
						}
					?>
					<article class="blog-page-card">
							<div class="blog-page-card__media">
								<?php if ( $image_url ) : ?>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async">
								<?php endif; ?>
							</div>

							<div class="blog-page-card__content">
								<p class="blog-page-card__date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
								<h3 class="blog-page-card__title"><?php echo esc_html( $title ); ?></h3>
								<?php if ( '' !== $description ) : ?>
									<p class="blog-page-card__description"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>

								<a class="blog-page-card__button" href="<?php the_permalink(); ?>">
									<span><?php esc_html_e( 'Read More', 'plumber' ); ?></span>
									<span class="blog-page-card__button-icon" aria-hidden="true" style="--blog-button-icon:url('<?php echo esc_url( $icon_url ); ?>')"></span>
								</a>
							</div>
					</article>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="blog-page-section__empty"><?php esc_html_e( 'No posts found.', 'plumber' ); ?></p>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
