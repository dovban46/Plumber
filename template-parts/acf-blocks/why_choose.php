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
				$mobile_text       = '';
				$mobile_small_text = '';

				if ( is_array( $item_icon ) ) {
					$icon_url = isset( $item_icon['url'] ) ? $item_icon['url'] : '';
					$icon_alt = isset( $item_icon['alt'] ) ? $item_icon['alt'] : '';
				} elseif ( is_int( $item_icon ) || ctype_digit( (string) $item_icon ) ) {
					$icon_url = wp_get_attachment_image_url( (int) $item_icon, 'full' );
					$icon_alt = (string) get_post_meta( (int) $item_icon, '_wp_attachment_image_alt', true );
				} elseif ( is_string( $item_icon ) ) {
					$icon_url = $item_icon;
				}

				if ( $item_text ) {
					$item_text_plain = trim( wp_strip_all_tags( (string) $item_text ) );
					$mobile_text       = $item_text_plain;
					$mobile_small_text = $item_text_plain;

					if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
						if ( mb_strlen( $item_text_plain ) > 85 ) {
							$mobile_text = rtrim( mb_substr( $item_text_plain, 0, 85 ) ) . '...';
						}
						if ( mb_strlen( $item_text_plain ) > 75 ) {
							$mobile_small_text = rtrim( mb_substr( $item_text_plain, 0, 75 ) ) . '...';
						}
					} elseif ( strlen( $item_text_plain ) > 85 ) {
						$mobile_text = rtrim( substr( $item_text_plain, 0, 85 ) ) . '...';
						if ( strlen( $item_text_plain ) > 75 ) {
							$mobile_small_text = rtrim( substr( $item_text_plain, 0, 75 ) ) . '...';
						}
					} elseif ( strlen( $item_text_plain ) > 75 ) {
						$mobile_small_text = rtrim( substr( $item_text_plain, 0, 75 ) ) . '...';
					}
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
						<p class="why-choose-card__text">
							<span class="why-choose-card__text-desktop"><?php echo esc_html( $item_text ); ?></span>
							<span class="why-choose-card__text-mobile"><?php echo esc_html( $mobile_text ); ?></span>
							<span class="why-choose-card__text-mobile-small"><?php echo esc_html( $mobile_small_text ); ?></span>
						</p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
