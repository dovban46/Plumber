<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Plumber
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function plumber_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Page template: our-services-template-default.php (any theme subfolder).
	if ( is_page() ) {
		$page_template = get_page_template_slug();
		if ( $page_template && false !== strpos( $page_template, 'our-services-template-default' ) ) {
			$classes[] = 'plumber-page-our-services-template';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'plumber_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function plumber_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'plumber_pingback_header' );
