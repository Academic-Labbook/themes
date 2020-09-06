<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Labbook
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$labbook_comment_count = get_comments_number();
			if ( '1' === $labbook_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One comment on &ldquo;%1$s&rdquo;', 'labbook' ),
					'<span>' . get_the_title() . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', $labbook_comment_count, 'comments title', 'labbook' ) ),
					number_format_i18n( $labbook_comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<span>' . get_the_title() . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php
		the_comments_navigation(
			array(
				'next_text' => __( 'Newer comments', 'labbook' ) . ' <i class="fa fa-chevron-right" aria-hidden="true"></i>',
				'prev_text' => '<i class="fa fa-chevron-left" aria-hidden="true"></i> ' . __( 'Older comments', 'labbook' ),
			)
		);
		?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation(
			array(
				'next_text' => __( 'Newer comments', 'labbook' ) . ' <i class="fa fa-chevron-right" aria-hidden="true"></i>',
				'prev_text' => '<i class="fa fa-chevron-left" aria-hidden="true"></i> ' . __( 'Older comments', 'labbook' ),
			)
		);

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'labbook' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

	$labbook_allowed_comment_snippets = array();

	foreach ( labbook_allowed_comment_html() as $allowed_html ) {
		$labbook_allowed_comment_snippets[] = '<code>' . $allowed_html . '</code>';
	}

	$labbook_allowed_comment_html_tags = implode( ', ', $labbook_allowed_comment_snippets );

	comment_form(
		array(
			'comment_notes_after' => wp_kses_post(
				sprintf(
					__(
						'<span class="comment-hint"><p>Supported comment tags and attributes: %1$s.</p><p>Hint: media files can be attached to comments by first uploading them to the <a href="%3$s">media library</a>. A link can be made using <code>%2$s</code> where <code>...</code> is the URL copied from the details for the uploaded file.</p></span>',
						'labbook'
					),
					$labbook_allowed_comment_html_tags,
					esc_html__( '<a href="...">link text</a>', 'labbook' ),
					admin_url( 'upload.php' )
				)
			),
		)
	);
	?>

</div><!-- #comments -->
