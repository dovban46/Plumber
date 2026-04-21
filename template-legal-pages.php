<?php
/**
 * Template Name: Legal Pages
 * Template Post Type: page
 *
 * @package Plumber
 */

get_header();
?>

<main id="primary" class="site-main legal-page">
	<section class="legal-page__section" aria-label="<?php esc_attr_e( 'Legal content', 'plumber' ); ?>">
		<div class="legal-page__container">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<h1 class="legal-page__title"><?php the_title(); ?></h1>
				<div class="legal-page__content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div>
	</section>
</main>

<?php
get_footer();
