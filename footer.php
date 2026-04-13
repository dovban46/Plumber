<?php
/**
 * The template for displaying the footer.
 *
 * @package Plumber
 */

$footer_logo          = get_field( 'footer_logo', 'option' );
$footer_address       = get_field( 'footer_address', 'option' );
$footer_contact_links = get_field( 'footer_contact_links', 'option' );
$footer_social_links  = get_field( 'footer_social_links', 'option' );
$footer_copyright     = get_field( 'footer_copyright', 'option' );
$footer_bottom_note   = get_field( 'footer_bottom_note', 'option' );
$footer_policy_link   = get_field( 'footer_policy_link', 'option' );
$footer_terms_link    = get_field( 'footer_terms_link', 'option' );

$allowed_break_html = array(
	'br' => array(),
);

$footer_policy_title = '';
$footer_terms_title  = '';

if ( ! empty( $footer_policy_link ) ) {
	$policy_page_id = url_to_postid( $footer_policy_link );
	if ( $policy_page_id ) {
		$footer_policy_title = get_the_title( $policy_page_id );
	}
}

if ( ! empty( $footer_terms_link ) ) {
	$terms_page_id = url_to_postid( $footer_terms_link );
	if ( $terms_page_id ) {
		$footer_terms_title = get_the_title( $terms_page_id );
	}
}
?>

	<footer id="colophon" class="site-footer footer">
		<div class="footer__container">
			<div class="footer__top">
				<div class="footer__brand">
					<?php if ( ! empty( $footer_logo ) && ! empty( $footer_logo['url'] ) ) : ?>
						<div class="footer__logo">
							<img src="<?php echo esc_url( $footer_logo['url'] ); ?>" alt="<?php echo esc_attr( $footer_logo['alt'] ?? get_bloginfo( 'name' ) ); ?>">
						</div>
					<?php endif; ?>

					<?php if ( $footer_address ) : ?>
						<div class="footer__address">
							<?php echo wp_kses( $footer_address, $allowed_break_html ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $footer_contact_links ) && is_array( $footer_contact_links ) ) : ?>
						<ul class="footer__contact-links">
							<?php foreach ( $footer_contact_links as $contact_item ) : ?>
								<?php $item_link = $contact_item['item_link'] ?? null; ?>
								<?php if ( ! empty( $item_link['url'] ) ) : ?>
									<?php
									$item_title = ! empty( $item_link['title'] )
										? wp_kses( $item_link['title'], $allowed_break_html )
										: esc_html( $item_link['url'] );
									?>
									<li class="footer__contact-item">
										<a class="footer__contact-link" href="<?php echo esc_url( $item_link['url'] ); ?>"<?php echo ! empty( $item_link['target'] ) ? ' target="' . esc_attr( $item_link['target'] ) . '"' : ''; ?>>
											<?php echo $item_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="footer__menus">
					<nav class="footer__menu footer__menu--quick" aria-label="<?php esc_attr_e( 'Footer quick links', 'plumber' ); ?>">
						<h3 class="footer__menu-title"><?php esc_html_e( 'Quick Links', 'plumber' ); ?></h3>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer_quick_links',
								'container'      => false,
								'menu_class'     => 'footer__menu-list',
								'fallback_cb'    => '__return_empty_string',
							)
						);
						?>
					</nav>

					<nav class="footer__menu footer__menu--services" aria-label="<?php esc_attr_e( 'Footer services', 'plumber' ); ?>">
						<h3 class="footer__menu-title"><?php esc_html_e( 'Services', 'plumber' ); ?></h3>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer_services',
								'container'      => false,
								'menu_class'     => 'footer__menu-list',
								'fallback_cb'    => '__return_empty_string',
							)
						);
						?>
					</nav>
				</div>

				<?php if ( ! empty( $footer_social_links ) && is_array( $footer_social_links ) ) : ?>
					<ul class="footer__social-links" aria-label="<?php esc_attr_e( 'Social links', 'plumber' ); ?>">
						<?php foreach ( $footer_social_links as $social_item ) : ?>
							<?php
							$social_icon = $social_item['social_icon'] ?? null;
							$social_link = $social_item['social_link'] ?? null;
							?>
							<?php if ( ! empty( $social_link['url'] ) ) : ?>
								<li class="footer__social-item">
									<a class="footer__social-link" href="<?php echo esc_url( $social_link['url'] ); ?>" aria-label="<?php echo esc_attr( $social_link['title'] ?: __( 'Social link', 'plumber' ) ); ?>"<?php echo ! empty( $social_link['target'] ) ? ' target="' . esc_attr( $social_link['target'] ) . '"' : ''; ?>>
										<?php if ( ! empty( $social_icon['url'] ) ) : ?>
											<img src="<?php echo esc_url( $social_icon['url'] ); ?>" alt="<?php echo esc_attr( $social_icon['alt'] ?? '' ); ?>">
										<?php else : ?>
											<span><?php echo esc_html( $social_link['title'] ?: __( 'Social', 'plumber' ) ); ?></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="footer__bottom">
				<div class="footer__copyright">
					<?php
					if ( $footer_copyright ) {
						echo wp_kses_post( do_shortcode( $footer_copyright ) );
					}
					?>
				</div>

				<?php if ( ! empty( $footer_policy_link ) ) : ?>
					<div class="footer__policy">
						<a class="footer__policy-link" href="<?php echo esc_url( $footer_policy_link ); ?>">
							<?php echo esc_html( $footer_policy_title ?: __( 'Privacy Policy', 'plumber' ) ); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php if ( $footer_bottom_note ) : ?>
					<div class="footer__note"><?php echo wp_kses( $footer_bottom_note, $allowed_break_html ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $footer_terms_link ) ) : ?>
					<div class="footer__terms">
						<a class="footer__terms-link" href="<?php echo esc_url( $footer_terms_link ); ?>">
							<?php echo esc_html( $footer_terms_title ?: __( 'Terms of Use', 'plumber' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
