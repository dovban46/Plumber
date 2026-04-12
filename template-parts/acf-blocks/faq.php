<?php
$faq_section = get_sub_field( 'faq_section' );

if ( ! $faq_section || ! is_array( $faq_section ) ) {
	return;
}

$faq_title     = isset( $faq_section['faq_title'] ) ? $faq_section['faq_title'] : '';
$faq_image_bg  = isset( $faq_section['faq_image_bg'] ) ? $faq_section['faq_image_bg'] : null;
$faq_questions = isset( $faq_section['faq_questions'] ) && is_array( $faq_section['faq_questions'] ) ? $faq_section['faq_questions'] : array();

if ( empty( $faq_questions ) ) {
	return;
}

$bg_image_url = '';

if ( is_array( $faq_image_bg ) ) {
	$bg_image_url = isset( $faq_image_bg['url'] ) ? $faq_image_bg['url'] : '';
} elseif ( is_int( $faq_image_bg ) || ctype_digit( (string) $faq_image_bg ) ) {
	$bg_image_url = wp_get_attachment_image_url( (int) $faq_image_bg, 'full' );
} elseif ( is_string( $faq_image_bg ) ) {
	$bg_image_url = $faq_image_bg;
}

$title_markup = '';

if ( $faq_title ) {
	$title_parts = preg_split( '/(questions)/i', (string) $faq_title, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( is_array( $title_parts ) ) {
		foreach ( $title_parts as $part ) {
			if ( preg_match( '/^questions$/i', $part ) ) {
				$title_markup .= '<span class="faq-section__title-highlight">' . esc_html( $part ) . '</span>';
			} else {
				$title_markup .= esc_html( $part );
			}
		}
	}
}

$schema_entities = array();
?>

<section class="faq-section" style="<?php echo $bg_image_url ? esc_attr( 'background-image: url(' . esc_url( $bg_image_url ) . ');' ) : ''; ?>">
	<div class="faq-section__container">
		<?php if ( $title_markup ) : ?>
			<h2 class="faq-section__title"><?php echo wp_kses( $title_markup, array( 'span' => array( 'class' => true ) ) ); ?></h2>
		<?php endif; ?>

		<div class="faq-list" role="list">
			<?php foreach ( $faq_questions as $index => $item ) : ?>
				<?php
				$question = isset( $item['question'] ) ? trim( (string) $item['question'] ) : '';
				$respond  = isset( $item['respond'] ) ? (string) $item['respond'] : '';

				if ( ! $question || ! $respond ) {
					continue;
				}

				$is_open      = 0 === $index;
				$item_id      = 'faq-item-' . $index . '-' . wp_rand( 1000, 9999 );
				$question_id  = $item_id . '-question';
				$answer_id    = $item_id . '-answer';
				$answer_plain = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $respond ) ) );

				$schema_entities[] = array(
					'@type'          => 'Question',
					'name'           => $question,
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $answer_plain,
					),
				);
				?>
				<article class="faq-item <?php echo $is_open ? 'is-open' : ''; ?>" role="listitem">
					<button id="<?php echo esc_attr( $question_id ); ?>" class="faq-item__trigger" type="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $answer_id ); ?>">
						<span class="faq-item__question"><?php echo esc_html( $question ); ?></span>
						<span class="faq-item__icon" aria-hidden="true">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/cancel.svg' ); ?>" alt="">
						</span>
					</button>
					<div id="<?php echo esc_attr( $answer_id ); ?>" class="faq-item__answer" aria-labelledby="<?php echo esc_attr( $question_id ); ?>" <?php echo $is_open ? '' : 'hidden'; ?>>
						<?php echo wp_kses_post( wpautop( $respond ) ); ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( ! empty( $schema_entities ) ) : ?>
	<script type="application/ld+json">
		<?php
		echo wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $schema_entities,
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		?>
	</script>
<?php endif; ?>
