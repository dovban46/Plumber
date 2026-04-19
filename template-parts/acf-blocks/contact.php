<?php
$contact_section = get_sub_field( 'contact_section' );

if ( ! $contact_section || ! is_array( $contact_section ) ) {
	return;
}

$contact_title = isset( $contact_section['contact_title'] ) ? $contact_section['contact_title'] : '';
$contact_items = isset( $contact_section['contact_items'] ) && is_array( $contact_section['contact_items'] )
	? $contact_section['contact_items']
	: array();
$contact_form = isset( $contact_section['contact_form'] ) ? $contact_section['contact_form'] : '';
$contact_map  = isset( $contact_section['contact_map'] ) ? $contact_section['contact_map'] : '';
?>

<section class="contact-section" aria-label="<?php esc_attr_e( 'Contact section', 'plumber' ); ?>">
	<div class="contact-section__container">
		<?php if ( $contact_title ) : ?>
			<h2 class="contact-section__title"><?php echo esc_html( $contact_title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $contact_items ) ) : ?>
			<div class="contact-section__items" role="list">
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
		<?php endif; ?>

		<div class="contact-section__content">
			<div class="contact-section__form">
				<?php if ( $contact_form ) : ?>
					<div class="contact-form-wrapper">
						<?php echo do_shortcode( $contact_form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="contact-section__map">
				<?php
				if ( $contact_map ) {
					$map_image_html = '';
					$map_alt        = $contact_title ? $contact_title : __( 'Map', 'plumber' );

					if ( is_array( $contact_map ) && isset( $contact_map['ID'] ) ) {
						$map_image_html = wp_get_attachment_image(
							(int) $contact_map['ID'],
							'full',
							false,
							array(
								'class'   => 'contact-section__map-image',
								'loading' => 'lazy',
								'alt'     => $map_alt,
							)
						);
					} elseif ( is_numeric( $contact_map ) ) {
						$map_image_html = wp_get_attachment_image(
							(int) $contact_map,
							'full',
							false,
							array(
								'class'   => 'contact-section__map-image',
								'loading' => 'lazy',
								'alt'     => $map_alt,
							)
						);
					} elseif ( is_string( $contact_map ) ) {
						$map_image_html = sprintf(
							'<img class="contact-section__map-image" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
							esc_url( $contact_map ),
							esc_attr( $map_alt )
						);
					}

					if ( $map_image_html ) {
						echo $map_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
				?>
			</div>
		</div>
	</div>
</section>
