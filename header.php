<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Plumber
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">

	<header id="masthead" class="site-header">
		<?php
		$header_logo   = get_field( 'header_logo', 'option' );
		$header_button = get_field( 'header_button', 'option' );
		$menu_location = has_nav_menu( 'primary' ) ? 'primary' : 'menu-1';

		$logo_url = '';
		$logo_alt = get_bloginfo( 'name' );

		if ( is_array( $header_logo ) ) {
			$logo_url = isset( $header_logo['url'] ) ? $header_logo['url'] : '';
			$logo_alt = ! empty( $header_logo['alt'] ) ? $header_logo['alt'] : $logo_alt;
		} elseif ( is_int( $header_logo ) || ctype_digit( (string) $header_logo ) ) {
			$logo_url = wp_get_attachment_image_url( (int) $header_logo, 'full' );
			$alt_text = get_post_meta( (int) $header_logo, '_wp_attachment_image_alt', true );
			$logo_alt = $alt_text ? $alt_text : $logo_alt;
		} elseif ( is_string( $header_logo ) ) {
			$logo_url = $header_logo;
		}

		$button_url    = '';
		$button_text   = '';
		$button_target = '_self';

		if ( is_array( $header_button ) ) {
			$button_url    = isset( $header_button['url'] ) ? $header_button['url'] : '';
			$button_text   = isset( $header_button['title'] ) ? $header_button['title'] : '';
			$button_target = ! empty( $header_button['target'] ) ? $header_button['target'] : '_self';
		} elseif ( is_string( $header_button ) ) {
			$button_url = $header_button;
		}
		?>

		<div class="header-inner">
			<div class="header-branding">
				<a class="header-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php if ( $logo_url ) : ?>
						<img class="header-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>">
					<?php else : ?>
						<span class="header-logo-text"><?php bloginfo( 'name' ); ?></span>
					<?php endif; ?>
				</a>
			</div>

			<button class="menu-toggle" type="button" aria-controls="site-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle menu', 'plumber' ); ?>">
				<span class="menu-toggle__line"></span>
				<span class="menu-toggle__line"></span>
				<span class="menu-toggle__line"></span>
			</button>

			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'plumber' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => $menu_location,
						'menu_id'        => 'primary-menu',
						'container'      => false,
					)
				);
				?>
			</nav>

			<div class="header-actions">
				<?php if ( $button_url ) : ?>
					<a class="header-button" href="<?php echo esc_url( $button_url ); ?>" target="<?php echo esc_attr( $button_target ); ?>" <?php echo '_blank' === $button_target ? 'rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( $button_text ? $button_text : __( 'Book now', 'plumber' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</header><!-- #masthead -->

	<?php
	$plumber_phone_fab = function_exists( 'plumber_get_floating_phone_link' ) ? plumber_get_floating_phone_link() : null;
	if ( is_array( $plumber_phone_fab ) && ! empty( $plumber_phone_fab['url'] ) ) :
		$plumber_phone_target = ! empty( $plumber_phone_fab['target'] ) ? $plumber_phone_fab['target'] : '_self';
		?>
		<a class="site-phone-fab hero-section__phone-link" href="<?php echo esc_url( $plumber_phone_fab['url'] ); ?>" target="<?php echo esc_attr( $plumber_phone_target ); ?>" aria-label="<?php echo esc_attr( ! empty( $plumber_phone_fab['title'] ) ? $plumber_phone_fab['title'] : __( 'Call us', 'plumber' ) ); ?>" <?php echo ( '_blank' === $plumber_phone_target ) ? 'rel="noopener noreferrer"' : ''; ?>>
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/phone.svg' ); ?>" alt="">
		</a>
	<?php endif; ?>
