<?php
$why_choose_section = get_sub_field( 'why_choose_section' );

if ( ! $why_choose_section || ! is_array( $why_choose_section ) ) {
	return;
}

$section_title = isset( $why_choose_section['why_choose_title'] ) ? $why_choose_section['why_choose_title'] : '';
$items         = isset( $why_choose_section['why_choose_items'] ) ? $why_choose_section['why_choose_items'] : array();

if ( empty( $items ) ) {
	return;
}
?>

<section class="why-choose" aria-labelledby="why-choose-title">
	<div class="why-choose__container">
		<?php if ( $section_title ) : ?>
			<h2 id="why-choose-title" class="why-choose__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<div class="why-choose__grid">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$item_title = isset( $item['title'] ) ? $item['title'] : '';
				$item_text  = isset( $item['text'] ) ? $item['text'] : '';
				$item_icon  = isset( $item['icon'] ) ? $item['icon'] : null;
				$icon_url   = '';
				$icon_alt   = '';

				if ( is_array( $item_icon ) ) {
					$icon_url = isset( $item_icon['url'] ) ? $item_icon['url'] : '';
					$icon_alt = isset( $item_icon['alt'] ) ? $item_icon['alt'] : '';
				} elseif ( is_int( $item_icon ) || ctype_digit( (string) $item_icon ) ) {
					$icon_url = wp_get_attachment_image_url( (int) $item_icon, 'full' );
					$icon_alt = (string) get_post_meta( (int) $item_icon, '_wp_attachment_image_alt', true );
				} elseif ( is_string( $item_icon ) ) {
					$icon_url = $item_icon;
				}
				?>

				<article class="why-choose-card">
					<div class="why-choose-card__head">
						<?php if ( $item_title ) : ?>
							<h3 class="why-choose-card__title"><?php echo esc_html( $item_title ); ?></h3>
						<?php endif; ?>

						<?php if ( $icon_url ) : ?>
							<div class="why-choose-card__icon-wrap">
								<img class="why-choose-card__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $icon_alt ? $icon_alt : $item_title ); ?>">
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $item_text ) : ?>
						<p class="why-choose-card__text"><?php echo esc_html( $item_text ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
