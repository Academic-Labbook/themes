<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Labbook
 */

if ( ! defined( 'WPINC' ) ) {
	// Prevent direct access.
	exit;
}

if ( ! function_exists( 'labbook_the_post_title' ) ) :
	/**
	 * Print the post title.
	 *
	 * @param int|WP_Post|null $post   Post ID or post object. Defaults to global $post.
	 * @param bool             $url    Make title into permalink.
	 * @param bool             $icon   Add post icon, if present.
	 * @param bool             $anchor Add hover anchor with permalink.
	 */
	function labbook_the_post_title( $post = null, $url = true, $icon = true, $anchor = false ) {
		$post = get_post( $post );
		$title = get_the_title( $post );

		echo '<h2 class="entry-title">';

		if ( $icon ) {
			// Display icon, if present.
			if ( 'status' === get_post_format( $post ) ) {
				$icon_class = 'fa fa-info-circle';
			} else {
				// Don't show icon.
				$icon_class = '';
			}

			if ( ! empty( $icon_class ) ) {
				printf(
					'<i class="%1$s"></i>',
					esc_attr( $icon_class )
				);
			}
		}

		if ( $url ) {
			// Wrap title in its permalink.
			printf(
				'<a href="%1$s" rel="bookmark">%2$s</a>',
				esc_url( get_permalink( $post ) ),
				esc_html( $title )
			);
		} else {
			// Just display title.
			echo esc_html( $title );
		}

		if ( $anchor ) {
			// Add hover anchor with permalink.
			labbook_the_post_anchor( $post );
		}

		echo '</h2>';
	}
endif;

if ( ! function_exists( 'labbook_the_post_anchor' ) ) :
	/**
	 * Print the post anchor.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_post_anchor( $post = null ) {
		$post = get_post( $post );

		if ( is_null( $post ) ) {
			return;
		}

		printf(
			'<a class="entry-link" href="%1$s" title="%2$s"><i class="fa fa-link"></i></a>',
			esc_url( get_permalink( $post ) ),
			esc_attr__( 'Permalink', 'labbook' )
		);
	}
endif;

if ( ! function_exists( 'labbook_get_post_date' ) ) :
	/**
	 * Get formatted post date.
	 *
	 * @param int|WP_Post|null $post     Post ID or post object. Defaults to global $post.
	 * @param bool             $modified Show the modified date.
	 * @param bool             $time     Show the time of day.
	 * @param bool             $icon     Show calendar icon.
	 * @param bool             $url      Wrap date in URL to date archive page.
	 * @return string
	 */
	function labbook_get_post_date( $post = null, $modified = false, $time = true, $icon = true, $url = true ) {
		$datetime_fmt = labbook_get_date_format( $time );

		// ISO 8601 formatted date.
		$date_iso = $modified ? get_the_modified_date( 'c', $post ) : get_the_date( 'c', $post );

		// Date formatted to WordPress preference.
		$date_str = $modified ? get_the_modified_date( $datetime_fmt, $post ) : get_the_date( $datetime_fmt, $post );

		// How long ago.
		$human_date = $modified ? labbook_get_human_date( $post->post_modified ) : labbook_get_human_date( $post->post_date );

		// Different time class defending on whether we're showing publication or modification date.
		$time_class = $modified ? 'updated' : 'entry-date published';

		$time_str = sprintf(
			'<time class="%1$s" datetime="%2$s" title="%3$s">%4$s</time>',
			esc_attr( $time_class ),
			esc_attr( $date_iso ),
			esc_attr( $human_date ),
			esc_attr( $date_str )
		);

		if ( $url ) {
			// Year/month/day dates.
			$year = $modified ? get_the_modified_time( 'Y', $post ) : get_post_time( 'Y', $post );
			$month = $modified ? get_the_modified_time( 'm', $post ) : get_post_time( 'm', $post );
			$day = $modified ? get_the_modified_time( 'j', $post ) : get_post_time( 'j', $post );

			$day_url = get_day_link( $year, $month, $day );

			// Wrap URL to date page.
			$time_str = sprintf(
				'<a href="%1$s" rel="bookmark">%2$s</a>',
				esc_url( $day_url ),
				$time_str
			);
		}

		if ( $icon ) {
			if ( $modified ) {
				$title = __( 'Modification date', 'labbook' );
			} else {
				$title = __( 'Publication date', 'labbook' );
			}

			// Add icons.
			$time_str = sprintf(
				'<i class="fa fa-calendar" title="%1$s" aria-hidden="true"></i>%2$s',
				esc_attr( $title ),
				$time_str
			);
		}

		return $time_str;
	}
endif;

if ( ! function_exists( 'labbook_get_date_format' ) ) :
	/**
	 * Get date and optional time format strings to pass to get_the_date or get_the_modified_date.
	 *
	 * @param  bool $time Add time format string.
	 * @return string
	 */
	function labbook_get_date_format( $time = true ) {
		$datetime_fmt = get_option( 'date_format' );

		if ( $time ) {
			// Combined date and time formats.
			$datetime_fmt = sprintf(
				/* translators: 1: date, 2: time; note that "\a\t" escapes "at" in PHP's date() function */
				__( '%1$s \a\t %2$s', 'labbook' ),
				esc_html( $datetime_fmt ),
				get_option( 'time_format' )
			);
		}

		return $datetime_fmt;
	}
endif;

if ( ! function_exists( 'labbook_get_human_date' ) ) :
	/**
	 * Get human formatted date, e.g. "3 hours ago".
	 *
	 * @param string $date_str Date format string.
	 * @return string
	 */
	function labbook_get_human_date( $date_str ) {
		$timestamp = strtotime( $date_str );

		return sprintf(
			/* translators: 1: time ago */
			__( '%s ago', 'labbook' ),
			human_time_diff( $timestamp )
		);
	}
endif;

if ( ! function_exists( 'labbook_the_post_meta' ) ) :
	/**
	 * Print HTML with meta information about post.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_post_meta( $post = null ) {
		$post = get_post( $post );

		echo '<div class="byline">';

		// Print post ID.
		labbook_the_post_id_icon( $post );
		echo '&nbsp;&nbsp;';

		if ( 'post' === $post->post_type ) {
			labbook_the_authors( $post );
			echo '&nbsp;&nbsp;';
		}

		if ( labbook_get_option( 'show_edit_summaries' ) && labbook_get_post_edit_count( $post ) > 0 ) {
			// Print revisions link.
			labbook_the_revisions_link( $post );
			echo '&nbsp;&nbsp;';
		}

		if ( 'page' === $post->post_type ) {
			$permission = 'edit_page';
		} else {
			$permission = 'edit_post';
		}

		if ( current_user_can( $permission, $post ) ) {
			// Print edit post link.
			labbook_the_post_edit_link( $post );
			echo '&nbsp;&nbsp;';
		}

		echo '</div>';

		// Allowed tags in date HTML.
		$allowed_date_html = array(
			'a'		=> array(
				'href'	=> array(),
				'rel'	=> array(),
			),
			'time'	=> array(
				'class'		=> array(),
				'datetime'	=> array(),
				'title'		=> array(),
			),
			'i'		=> array(
				'class'			=> array(),
				'title'			=> array(),
				'aria-hidden'	=> array(),
			),
		);

		if ( 'post' === $post->post_type ) {
			echo '<div class="posted-on">';
			echo wp_kses( labbook_get_post_date( $post ), $allowed_date_html );

			// Check post timestamps to see if the post has been modified.
			if ( get_the_time( 'U', $post ) !== get_the_modified_time( 'U', $post ) ) {
				printf(
					/* translators: 1: post modification time */
					esc_html__( ' (last edited %1$s)', 'labbook' ),
					wp_kses( labbook_get_post_date( $post, true ), $allowed_date_html )
				);
			}

			echo '</div>';
		}
	}
endif;

if ( ! function_exists( 'labbook_the_post_id_icon' ) ) :
	/**
	 * Print the post ID icon.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_post_id_icon( $post ) {
		$post = get_post( $post );

		printf(
			'<i class="fa fa-link" title="%1$s"></i>%2$s',
			esc_html__( 'ID', 'labbook' ),
			esc_html( $post->ID )
		);
	}
endif;

if ( ! function_exists( 'labbook_the_post_edit_link' ) ) :
	/**
	 * Print the post edit link.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_post_edit_link( $post = null ) {
		$post = get_post( $post );

		printf(
			'<i class="fa fa-edit" aria-hidden="true"></i><a href="%1$s">%2$s</a>',
			esc_url( get_edit_post_link( $post ) ),
			esc_html__( 'Edit', 'labbook' )
		);
	}
endif;

if ( ! function_exists( 'labbook_the_revisions_link' ) ) :
	/**
	 * Print the post revisions link.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_revisions_link( $post = null ) {
		global $ssl_alp;

		if ( ! is_plugin_active( 'ssl-alp/alp.php' ) ) {
			// Required functionality not available.
			return;
		}

		$post = get_post( $post );

		if ( ! $ssl_alp->revisions->edit_summary_allowed( $post ) ) {
			return;
		}

		$edit_count = labbook_get_post_edit_count( $post );

		if ( is_null( $edit_count ) ) {
			// Revisions not available.
			return;
		}

		/* translators: number of revisions */
		$edit_str = sprintf( _n( '%s revision', '%s revisions', $edit_count, 'labbook' ), $edit_count );

		printf(
			'<i class="fa fa-pencil" title="%1$s" aria-hidden="true"></i><a href="%2$s#post-revisions">%3$s</a>',
			esc_attr__( 'Number of edits made to the original post', 'labbook' ),
			esc_url( get_the_permalink( $post ) ),
			esc_html( $edit_str )
		);
	}
endif;

if ( ! function_exists( 'labbook_the_authors' ) ) :
	/**
	 * Print formatted author HTML.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 * @param bool             $icon Show author icon.
	 * @param bool             $url  Show author URLs.
	 */
	function labbook_the_authors( $post = null, $icon = true, $url = true ) {
		global $ssl_alp;

		$post = get_post( $post );

		if ( is_plugin_active( 'ssl-alp/alp.php' ) && get_option( 'ssl_alp_allow_multiple_authors' ) ) {
			$authors = $ssl_alp->coauthors->get_coauthors( $post );
		} else {
			// Fall back to the_author if plugin is disabled.
			$authors = array();

			// Get single author object.
			$author = get_user_by( 'id', $post->post_author );

			// If there is no author, $author == false.
			if ( $author ) {
				$authors[] = $author;
			}
		}

		$author_html = array();

		foreach ( $authors as $author ) {
			$author = labbook_format_author( $author, $url );

			if ( ! is_null( $author ) ) {
				$author_html[] = $author;
			}
		}

		if ( ! count( $author_html ) ) {
			// No authors.
			return;
		}

		echo '<span class="authors">';

		if ( count( $author_html ) > 1 ) {
			// There are multiple authors.
			$icon_class = 'fa fa-users';

			// Get delimiters.
			$delimiter_between = _x( ', ', 'delimiter between coauthors except last', 'labbook' );
			$delimiter_between_last = _x( ' and ', 'delimiter between last two coauthors', 'labbook' );

			// Pop last author off.
			$last_author = array_pop( $author_html );

			// Implode author list.
			$author_list_html = implode( __( ', ', 'labbook' ), $author_html ) . $delimiter_between_last . $last_author;
		} else {
			// Single author.
			$icon_class = 'fa fa-user';

			$author_list_html = $author_html[0];
		}

		if ( $icon ) {
			printf(
				'<i class="%1$s" title="%2$s" aria-hidden="true"></i>',
				esc_attr( $icon_class ),
				esc_html__( 'Authors', 'labbook' )
			);
		}

		echo wp_kses(
			$author_list_html,
			array(
				'span'	=> array(
					'class'	=> array(),
				),
				'a'		=> array(
					'href'	=> array(),
				),
			)
		);

		echo '</span>';
	}
endif;

if ( ! function_exists( 'labbook_format_author' ) ) :
	/**
	 * Get formatted author name.
	 *
	 * @param WP_Author $author The author.
	 * @param bool      $url    Show author URL.
	 * @return string
	 */
	function labbook_format_author( $author, $url = true ) {
		if ( is_null( $author ) ) {
			return;
		}

		if ( $url ) {
			$author_url = get_author_posts_url( $author->ID );

			// Wrap author in link to their posts.
			$author_display = sprintf(
				'<span class="author vcard"><a href="%1$s">%2$s</a></span>',
				esc_url( $author_url ),
				esc_html( $author->display_name )
			);
		} else {
			$author_display = esc_html( $author->display_name );
		}

		return $author_display;
	}
endif;

if ( ! function_exists( 'labbook_the_footer' ) ) :
	/**
	 * Print the footer for the specified post.
	 *
	 * Cannot specify a custom post id here, as `get_comments_number_text` can't
	 * handle it. It always uses the current post.
	 */
	function labbook_the_footer() {
		/* translators: used between list items, there is a space after the comma. */
		$categories_list = get_the_category_list( __( ', ', 'labbook' ) );

		// Allowed category and tag HTML.
		$cat_tag_tags = array(
			'a'	=> array(
				'href'	=> array(),
				'rel'	=> array(),
			),
		);

		if ( $categories_list ) {
			printf(
				'<span class="cat-links"><i class="fa fa-folder-open" aria-hidden="true"></i>%1$s</span>',
				wp_kses( $categories_list, $cat_tag_tags )
			);
			echo '&nbsp;&nbsp;';
		}

		/* translators: used between list items, there is a space after the comma. */
		$tags_list = get_the_tag_list( '', __( ', ', 'labbook' ) );

		if ( $tags_list ) {
			printf(
				'<span class="tag-links"><i class="fa fa-tags" aria-hidden="true"></i>%1$s</span>',
				wp_kses( $tags_list, $cat_tag_tags )
			);
			echo '&nbsp;&nbsp;';
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			// Show comments link.
			printf(
				'<span class="comments-link"><i class="fa fa-comment" aria-hidden="true"></i><a href="%1$s">%2$s</a></span>',
				esc_url( get_comments_link() ),
				esc_html( get_comments_number_text( __( 'Leave a comment', 'labbook' ) ) )
			);
			echo '&nbsp;&nbsp;';
		}
	}
endif;

if ( ! function_exists( 'labbook_the_revisions' ) ) :
	/**
	 * Print revisions for the specified post.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_revisions( $post = null ) {
		if ( ! labbook_get_option( 'show_edit_summaries' ) ) {
			// Display is unavailable.
			return;
		}

		$post = get_post( $post );

		if ( ! post_type_supports( $post->post_type, 'revisions' ) ) {
			// Post type not supported.
			return;
		}

		$current_page = ( get_query_var( 'revision_page' ) ) ? get_query_var( 'revision_page' ) : 1;

		// Total revisions.
		$count = labbook_get_post_revision_count( $post );

		if ( is_null( $count ) ) {
			// Revisions not available.
			return;
		}

		$per_page = labbook_get_option( 'edit_summaries_per_page' );
		$pages = ceil( $count / $per_page );

		// Get list of revisions to this post.
		$revisions = labbook_get_revisions( $post, $current_page, $per_page );

		if ( is_null( $revisions ) || ! is_array( $revisions ) || 0 === count( $revisions ) ) {
			// No revisions to show.
			return;
		}

		echo '<div id="post-revisions">';
		echo '<h3>';
		esc_html_e( 'History', 'labbook' );
		echo '</h3>';

		echo '<ul>';

		foreach ( $revisions as $revision ) {
			labbook_the_revision_description_list_item( $revision );
		}

		echo '</ul>';

		if ( $pages > 1 ) {
			echo paginate_links( array(
				'format'		=> '?revision_page=%#%',
				'current'  		=> $current_page,
				'total'    		=> $pages,
			) );
		}

		echo '</div>';
	}
endif;

if ( ! function_exists( 'labbook_the_revision_description_list_item' ) ) :
	/**
	 * Print description for the specified revision in a list item.
	 *
	 * @param int|WP_Post $revision The revision.
	 */
	function labbook_the_revision_description_list_item( $revision ) {
		global $ssl_alp;

		// Get revision object if id is specified.
		$revision = wp_get_post_revision( $revision );

		if ( is_null( $revision ) ) {
			return;
		}

		if ( 'revision' !== $revision->post_type ) {
			return;
		}

		// Get revision's edit summary.
		$revision_edit_summary = get_post_meta( $revision->ID, 'ssl_alp_edit_summary', true );
		$revision_edit_summary_revert_id = get_post_meta( $revision->ID, 'ssl_alp_edit_summary_revert_id', true );

		// Revision abbreviation, e.g. r101, with link to diff.
		$abbr = labbook_get_revision_abbreviation( $revision );

		if ( is_null( $abbr ) ) {
			// Invalid.
			return;
		}

		// The revision author display name.
		$author_display_name = get_the_author_meta( 'display_name', $revision->post_author );

		echo '<li>';

		// Print avatar.
		echo get_avatar( $revision->post_author, 18, null, $author_display_name );
		echo '&nbsp;&nbsp;';

		printf(
			'<span title="%1$s">%2$s</span>',
			esc_attr( get_the_modified_date( labbook_get_date_format( true ), $revision ) ),
			esc_html( labbook_get_human_date( $revision->post_modified ) )
		);
		echo '&nbsp;';

		// Allowed revision abbreviation tags.
		$allowed_abbr_tags = array(
			'a'	=> array(
				'href'	=> array(),
			),
		);

		// Print revision abbreviation.
		echo wp_kses( $abbr, $allowed_abbr_tags );

		if ( wp_is_post_autosave( $revision ) ) {
			// This is an autosave.
			echo '&nbsp;';
			echo '<strong>';
			esc_html_e( '(autosave)', 'labbook' );
			echo '</strong>';
		} elseif ( labbook_revision_was_autogenerated_on_publication( $revision ) ) {
			// This revision was created when the post was published.
			echo '&nbsp;';
			echo '<strong>';
			esc_html_e( '(published)', 'labbook' );
			echo '</strong>';
		} elseif ( ! empty( $revision_edit_summary_revert_id ) ) {
			// Revision was a revert.
			$source_abbr = labbook_get_revision_abbreviation( $revision_edit_summary_revert_id );

			if ( is_null( $source_abbr ) ) {
				// Source revision is invalid.
				/* translators: reverted to unknown revision */
				esc_html_e( ': reverted', 'labbook' );
			} else {
				printf(
					/* translators: 1: reverted revision ID/URL */
					esc_html__( ': reverted to %1$s', 'labbook' ),
					wp_kses( $source_abbr, $allowed_abbr_tags )
				);
			}

			// Get original source revision.
			$source_revision = $ssl_alp->revisions->get_source_revision( $revision );
			$source_edit_summary = get_post_meta( $source_revision->ID, 'ssl_alp_edit_summary', true );

			if ( ! empty( $source_edit_summary ) ) {
				// Add original edit summary.
				echo '&nbsp;';
				echo '<em>';
				printf(
					/* translators: 1: edit summary of post reverted to */
					esc_html__( '("%1$s")', 'labbook' ),
					esc_html( $source_edit_summary )
				);
				echo '</em>';
			}
		} elseif ( ! empty( $revision_edit_summary ) ) {
			printf(
				wp_kses(
					/* translators: 1: edit summary */
					__( ': <em>"%1$s"</em>', 'labbook' ),
					array(
						'em'	=> array(),
					)
				),
				esc_html( $revision_edit_summary )
			);
		} // End if().

		// Check if this revision is the current one.
		if ( get_the_time( 'U', $revision ) === get_the_modified_time( 'U', $revision->parent ) ) {
			echo '&nbsp';
			echo '<strong>';
			/* translators: current revision */
			esc_html_e( '(current)', 'labbook' );
			echo '</strong>';
		}

		echo '</li>';
	}
endif;

if ( ! function_exists( 'labbook_get_revision_abbreviation' ) ) :
	/**
	 * Gets abbreviated revision ID, with optional URL.
	 *
	 * If the specified revision doesn't exist, it may have been deleted but is still referenced by
	 * another revision edit summary. In this case, this function will will still show the
	 * non-existent revision ID but will not provide a URL to the diff screen.
	 *
	 * @param int  $revision Revision ID.
	 * @param bool $url      Print revision URL.
	 */
	function labbook_get_revision_abbreviation( $revision, $url = true ) {
		global $ssl_alp;

		$revision = wp_get_post_revision( $revision );

		/* translators: abbreviated revision ID text */
		$abbr_format = __( 'r%1$s', 'labbook' );

		if ( is_null( $revision ) ) {
			if ( ! is_numeric( $revision ) ) {
				// Invalid.
				return;
			}

			// Revision post ID.
			return sprintf( $abbr_format, $revision_id );
		}

		if ( 'revision' !== $revision->post_type ) {
			return;
		}

		// Revision post ID.
		$abbr = sprintf( $abbr_format, $revision->ID );

		// Add URL to diff if user can view.
		if ( $url ) {
			/**
			 * Note: interns are not shown the edit link below (it is empty) because they fail
			 * the edit_post permission check against the *revision* here. This is a subtle bug
			 * that would take a lot of effort to fix.
			 *
			 * Instead, interns simply aren't shown the revision link (but they still see the edit
			 * link).
			 */
			$edit_link = get_edit_post_link( $revision->ID );

			if ( ! empty( $edit_link ) && $ssl_alp->revisions->current_user_can_view_revision( $revision ) ) {
				$abbr = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $edit_link ),
					$abbr
				);
			}
		}

		return $abbr;
	}
endif;

if ( ! function_exists( 'labbook_the_references' ) ) :
	/**
	 * Print post references.
	 *
	 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_references( $post = null ) {
		global $ssl_alp;

		if ( ! labbook_get_option( 'show_crossreferences' ) ) {
			// Display is unavailable.
			return;
		} elseif ( ! is_plugin_active( 'ssl-alp/alp.php' ) ) {
			// Plugin is disabled.
			return;
		} elseif ( ! get_option( 'ssl_alp_enable_crossreferences' ) ) {
			// Tracking of cross-references are disabled.
			return;
		}

		$post = get_post( $post );

		if ( ! $ssl_alp->references->is_supported( $post ) ) {
			// Post type not supported.
			return;
		}

		$ref_to_posts = $ssl_alp->references->get_reference_to_posts( $post );
		$ref_from_posts = $ssl_alp->references->get_reference_from_posts( $post );

		if ( ( ! is_array( $ref_to_posts ) || ! count( $ref_to_posts ) ) && ( ! is_array( $ref_from_posts ) || ! count( $ref_from_posts ) ) ) {
			// No references.
			return;
		}

		echo '<div id="post-references">';
		echo '<h3>';
		esc_html_e( 'Cross-references', 'labbook' );
		echo '</h3>';

		if ( $ref_to_posts ) {
			echo '<h4>';
			esc_html_e( 'Links to', 'labbook' );
			echo '</h4>';
			labbook_the_referenced_post_list( $ref_to_posts );
		}

		if ( $ref_from_posts ) {
			echo '<h4>';
			esc_html_e( 'Linked from', 'labbook' );
			echo '</h4>';
			labbook_the_referenced_post_list( $ref_from_posts );
		}

		echo '</div>';
	}
endif;

if ( ! function_exists( 'labbook_the_referenced_post_list' ) ) {
	/**
	 * Print list of reference links.
	 *
	 * @param array $referenced_posts The referenced posts.
	 */
	function labbook_the_referenced_post_list( $referenced_posts ) {
		echo '<ul>';

		foreach ( $referenced_posts as $referenced_post ) {
			// Get post.
			$referenced_post = get_post( $referenced_post );

			// Print reference post information.
			labbook_referenced_post_list_item( $referenced_post );
		}

		echo '</ul>';
	}
}

if ( ! function_exists( 'labbook_referenced_post_list_item' ) ) {
	/**
	 * Print link to the specified reference post.
	 *
	 * @param int|WP_Post|null $referenced_post The referenced post.
	 */
	function labbook_referenced_post_list_item( $referenced_post = null ) {
		global $ssl_alp;

		$referenced_post = get_post( $referenced_post );

		if ( is_null( $referenced_post ) ) {
			// Post doesn't exist.
			return;
		}

		echo '<li>';

		// Post title.
		$post_title = $referenced_post->post_title;

		// Wrap URL.
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( get_permalink( $referenced_post ) ),
			esc_html( $post_title )
		);

		// Post date (only used if post type supports it).
		if ( $ssl_alp->references->show_date( $referenced_post ) ) {
			echo '&nbsp;';
			printf(
				'<span class="post-date">%1$s</span>',
				esc_html( get_the_date( get_option( 'date_format' ), $referenced_post ) )
			);
		}

		echo '</li>';
	}
} // End if().

if ( ! function_exists( 'labbook_the_page_breadcrumbs' ) ) :
	/**
	 * Print page breadcrumbs.
	 *
	 * @param int|WP_Post|null $page Post ID or post object. Defaults to global $post.
	 */
	function labbook_the_page_breadcrumbs( $page = null ) {
		if ( ! is_page( $page ) ) {
			// Not a page.
			return;
		}

		if ( ! labbook_get_option( 'show_page_breadcrumbs' ) ) {
			// Display is unavailable.
			return;
		}

		$breadcrumbs = labbook_get_page_breadcrumbs( $page );

		if ( ! count( $breadcrumbs ) ) {
			return;
		}

		echo '<ul>';

		foreach ( $breadcrumbs as $breadcrumb ) {
			echo '<li>';

			if ( ! empty( $breadcrumb['url'] ) ) {
				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $breadcrumb['url'] ),
					esc_html( $breadcrumb['title'] )
				);
			} else {
				echo esc_html( $breadcrumb['title'] );
			}

			echo '</li>';
		}

		echo '</ul>';
	}
endif;

if ( ! function_exists( 'labbook_the_toc' ) ) :
	/**
	 * Print the table of contents.
	 *
	 * @param TOC_Menu_Level $contents   The table of contents hierarchy.
	 * @param int            $max_levels Maximum heading level to display.
	 */
	function labbook_the_toc( $contents, $max_levels ) {
		if ( ! is_int( $max_levels ) || $max_levels < 0 ) {
			// Invalid.
			return;
		}

		if ( empty( $contents ) ) {
			// Invalid.
			return;
		}

		$menu_data = $contents->get_menu_data();

		if ( is_array( $menu_data ) ) {
			printf(
				'<a href="%1$s">%2$s</a>',
				esc_attr( '#' . $menu_data['id'] ),
				esc_html( $menu_data['title'] )
			);
		}

		if ( $max_levels > 0 ) {
			// Next level still visible - get children.
			$children = $contents->get_child_menus();

			if ( count( $children ) ) {
				echo '<ul>';

				foreach ( $children as $child ) {
					// Show sublevel.
					echo '<li>';
					labbook_the_toc( $child, $max_levels - 1 );
					echo '</li>';
				}

				echo '</ul>';
			}
		}
	}
endif;