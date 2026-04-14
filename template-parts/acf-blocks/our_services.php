<?php
/**
 * Our Services block: two category tabs with separate Swiper panels.
 * Tab slugs are taken from ACF Link URL hash (e.g. #residential) or fallbacks.
 *
 * @package Plumber
 */

$section = get_sub_field( 'our_services_section' );

if ( ! $section || ! is_array( $section ) ) {
	return;
}

$section_title = isset( $section['our_services_title'] ) ? $section['our_services_title'] : '';
$button_1      = isset( $section['our_services_button_1'] ) ? $section['our_services_button_1'] : null;
$button_2      = isset( $section['our_services_button_2'] ) ? $section['our_services_button_2'] : null;

if ( ! function_exists( 'plumber_our_services_slug_from_acf_link' ) ) {
	/**
	 * Get taxonomy term slug from ACF Link: URL fragment (#residential) or last path segment.
	 *
	 * @param array|null $link ACF link array.
	 * @param string     $fallback_slug Fallback if URL has no usable part.
	 * @return string
	 */
	function plumber_our_services_slug_from_acf_link( $link, $fallback_slug ) {
		if ( ! is_array( $link ) || empty( $link['url'] ) ) {
			return $fallback_slug;
		}

		$url = $link['url'];
		// Allow raw "#residential" stored as URL.
		if ( '#' === $url[0] ?? '' ) {
			$frag = substr( $url, 1 );
			$frag = rawurldecode( trim( $frag ) );
			if ( $frag !== '' ) {
				return sanitize_title( $frag );
			}
			return $fallback_slug;
		}

		$parts = wp_parse_url( $url );
		if ( ! empty( $parts['fragment'] ) ) {
			return sanitize_title( rawurldecode( $parts['fragment'] ) );
		}

		if ( ! empty( $parts['path'] ) ) {
			$path     = trim( $parts['path'], '/' );
			$segments = array_filter( explode( '/', $path ) );
			$last     = end( $segments );
			if ( $last !== false && $last !== '' ) {
				return sanitize_title( $last );
			}
		}

		return $fallback_slug;
	}
}

if ( ! function_exists( 'plumber_our_services_resolve_taxonomy' ) ) {
	/**
	 * Find which taxonomy on $post_type actually has the given term slugs.
	 *
	 * @param string   $post_type Post type name.
	 * @param string[] $slugs Term slugs to try.
	 * @return string Taxonomy name.
	 */
	function plumber_our_services_resolve_taxonomy( $post_type, array $slugs ) {
		$taxonomies = get_object_taxonomies( $post_type, 'names' );
		if ( empty( $taxonomies ) ) {
			return apply_filters( 'plumber_our_services_category_taxonomy', 'category' );
		}

		$slugs = array_filter( array_map( 'sanitize_title', $slugs ) );

		foreach ( $slugs as $slug ) {
			if ( $slug === '' ) {
				continue;
			}
			foreach ( $taxonomies as $tax ) {
				$term = get_term_by( 'slug', $slug, $tax );
				if ( $term && ! is_wp_error( $term ) ) {
					return $tax;
				}
			}
		}

		foreach ( $taxonomies as $tax ) {
			if ( is_taxonomy_hierarchical( $tax ) ) {
				return $tax;
			}
		}

		return $taxonomies[0];
	}
}

$slug_1 = plumber_our_services_slug_from_acf_link( $button_1, 'residential' );
$slug_2 = plumber_our_services_slug_from_acf_link( $button_2, 'commercial' );

if ( $slug_1 === $slug_2 ) {
	$slug_2 = 'commercial';
}

$category_taxonomy = plumber_our_services_resolve_taxonomy(
	'our-services',
	array( $slug_1, $slug_2 )
);
$category_taxonomy = apply_filters( 'plumber_our_services_category_taxonomy', $category_taxonomy );

$tabs = array(
	$slug_1 => array(
		'label' => ! empty( $button_1['title'] ) ? $button_1['title'] : __( 'Residential', 'plumber' ),
		'url'   => ( is_array( $button_1 ) && ! empty( $button_1['url'] ) ) ? $button_1['url'] : '#' . $slug_1,
		'target' => ( is_array( $button_1 ) && ! empty( $button_1['target'] ) ) ? $button_1['target'] : '',
	),
	$slug_2 => array(
		'label' => ! empty( $button_2['title'] ) ? $button_2['title'] : __( 'Commercial', 'plumber' ),
		'url'   => ( is_array( $button_2 ) && ! empty( $button_2['url'] ) ) ? $button_2['url'] : '#' . $slug_2,
		'target' => ( is_array( $button_2 ) && ! empty( $button_2['target'] ) ) ? $button_2['target'] : '',
	),
);

/**
 * @param string $slug Category slug.
 * @return WP_Query
 */
$plumber_our_services_query = static function ( $slug ) use ( $category_taxonomy ) {
	return new WP_Query(
		array(
			'post_type'      => 'our-services',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => array(
				array(
					'taxonomy' => $category_taxonomy,
					'field'    => 'slug',
					'terms'    => $slug,
				),
			),
		)
	);
};

$queries = array(
	$slug_1 => $plumber_our_services_query( $slug_1 ),
	$slug_2 => $plumber_our_services_query( $slug_2 ),
);

$default_tab_slug = $slug_1;
foreach ( $queries as $slug => $query_obj ) {
	if ( (int) $query_obj->found_posts > 0 ) {
		$default_tab_slug = $slug;
		break;
	}
}

if ( ! function_exists( 'plumber_output_our_services_slides' ) ) {
	/**
	 * Echo swiper slides for current loop post (call inside while).
	 *
	 * @return void
	 */
	function plumber_output_our_services_slides() {
		$custom_title   = get_field( 'title' );
		$custom_image   = get_field( 'image' );
		$custom_content = get_field( 'content' );
		$display_title  = $custom_title ? $custom_title : get_the_title();
		$display_text   = $custom_content ? wp_strip_all_tags( (string) $custom_content ) : wp_strip_all_tags( (string) get_the_excerpt() );
		$display_text   = trim( preg_replace( '/\s+/', ' ', $display_text ) );

		if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
			if ( mb_strlen( $display_text ) > 300 ) {
				$display_text = rtrim( mb_substr( $display_text, 0, 300 ) ) . '...';
			}
		} elseif ( strlen( $display_text ) > 300 ) {
			$display_text = rtrim( substr( $display_text, 0, 300 ) ) . '...';
		}

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

		<article class="swiper-slide our-services-slide">
			<div class="our-services-slide__media">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
				<?php endif; ?>
			</div>

			<div class="our-services-slide__content">
				<h3 class="our-services-slide__title"><?php echo esc_html( $display_title ); ?></h3>
				<p class="our-services-slide__text"><?php echo esc_html( $display_text ); ?></p>
				<a class="our-services-slide__button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View Details', 'plumber' ); ?></a>
			</div>
		</article>
		<?php
	}
}
?>

<section class="our-services" aria-label="<?php esc_attr_e( 'Our services', 'plumber' ); ?>">
	<div class="our-services__container">
		<?php if ( $section_title ) : ?>
			<h2 class="our-services__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<div class="our-services__top-buttons" role="group" aria-label="<?php esc_attr_e( 'Filter services by category', 'plumber' ); ?>">
			<?php
			foreach ( $tabs as $slug => $tab ) :
				$is_active_tab = ( $slug === $default_tab_slug );
				$button_class  = $is_active_tab
					? 'our-services__filter-button our-services__filter-button--filled'
					: 'our-services__filter-button our-services__filter-button--outline';
				$tab_url    = isset( $tab['url'] ) ? $tab['url'] : '#' . $slug;
				$tab_target = ! empty( $tab['target'] ) ? $tab['target'] : '_self';
				// esc_url() strips bare #fragment URLs; keep hash links for ACF.
				$href_attr = ( is_string( $tab_url ) && '#' === ( $tab_url[0] ?? '' ) ) ? esc_attr( $tab_url ) : esc_url( $tab_url );
				?>
				<a
					id="our-services-tab-<?php echo esc_attr( $slug ); ?>"
					class="<?php echo esc_attr( $button_class ); ?>"
					href="<?php echo $href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
					data-our-services-tab="<?php echo esc_attr( $slug ); ?>"
					role="button"
					aria-pressed="<?php echo $is_active_tab ? 'true' : 'false'; ?>"
					<?php echo ( '_blank' === $tab_target ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
				>
					<?php echo esc_html( $tab['label'] ); ?>
				</a>
				<?php
			endforeach;
			?>
		</div>

		<div class="our-services__panels">
			<?php
			foreach ( $queries as $slug => $services_query ) :
				$is_active = ( $slug === $default_tab_slug );
				$panel_id  = 'our-services-panel-' . sanitize_title( $slug );
				?>
				<div
					class="our-services__panel<?php echo $is_active ? ' is-active' : ''; ?>"
					id="<?php echo esc_attr( $panel_id ); ?>"
					data-our-services-panel="<?php echo esc_attr( $slug ); ?>"
					role="region"
					aria-labelledby="our-services-tab-<?php echo esc_attr( $slug ); ?>"
					<?php echo $is_active ? '' : ' hidden'; ?>
				>
					<?php if ( $services_query->have_posts() ) : ?>
						<div class="our-services-slider swiper">
							<div class="swiper-wrapper">
								<?php
								while ( $services_query->have_posts() ) :
									$services_query->the_post();
									plumber_output_our_services_slides();
								endwhile;
								?>
							</div>
							<div class="our-services-pagination swiper-pagination"></div>
						</div>
					<?php else : ?>
						<p class="our-services__empty"><?php esc_html_e( 'No services in this category yet.', 'plumber' ); ?></p>
					<?php endif; ?>
				</div>
				<?php
				wp_reset_postdata();
			endforeach;
			?>
		</div>
	</div>
</section>
