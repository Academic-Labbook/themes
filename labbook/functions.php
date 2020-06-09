<?php
/**
 * Labbook functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Labbook
 */

// Theme version.
define( 'LABBOOK_VERSION', '1.2.1' );

// Required PHP version.
define( 'LABBOOK_MINIMUM_PHP_VERSION', '7.0.0' );

// Allowed content layout modes.
$allowed_content_layout_modes = array( 'excerpt', 'full' );

if ( ! function_exists( 'labbook_version_too_low_admin_notice' ) ) :
	/**
	 * Notify admin that their PHP version is too low and return to the previous theme.
	 */
	function labbook_version_too_low_admin_notice() {
		echo '<div class="update-nag">';
		esc_html_e( 'Labbook cannot run on the currently installed PHP version.', 'labbook' );
		echo '<br/>';
		printf(
			/* translators: 1: current PHP version, 2: required PHP version */
			esc_html__( 'Actual version is: %1$s, required version is: %2$s.', 'labbook' ),
			esc_html( phpversion() ),
			esc_html( LABBOOK_MINIMUM_PHP_VERSION )
		);
		echo '</div>';
	}
endif;

if ( ! function_exists( 'labbook_check_php_version' ) ) :
	/**
	 * Detect current PHP version and prevent theme switch if not recent enough.
	 *
	 * @param string        $old_name  Old theme name.
	 * @param WP_Theme|null $old_theme Old theme object. Note: not always passed.
	 */
	function labbook_check_php_version( $old_name, $old_theme = null ) {
		// Compare versions.
		if ( version_compare( phpversion(), LABBOOK_MINIMUM_PHP_VERSION, '<' ) ) {
			// Theme not activated info message.
			add_action( 'admin_notices', 'labbook_version_too_low_admin_notice' );

			// Switch back to previous theme.
			switch_theme( $old_name );
		}
	}
endif;
add_action( 'after_switch_theme', 'labbook_check_php_version' );

if ( ! function_exists( 'labbook_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function labbook_setup() {
		global $labbook_default_options;

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 */
		load_theme_textdomain( 'labbook', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'site-menu'    => esc_html__( 'Primary', 'labbook' ),
				'network-menu' => esc_html__( 'Network', 'labbook' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 */
		add_theme_support(
			'post-formats',
			array(
				'status',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'labbook_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 155, // Should match .site-branding CSS grid row height.
				'width'       => 400,
				'flex-width'  => true,
				'flex-height' => false,
			)
		);

		/**
		 * Add support for styling the post editor.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#editor-styles
		 */
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style-editor.css' );

		/**
		 * Add support for showing coauthors.
		 */
		add_theme_support( 'ssl-alp-coauthors' );

		/**
		 * Add support for showing edit summaries under posts.
		 */
		add_theme_support( 'ssl-alp-edit-summaries' );

		/**
		 * Add support for showing cross-references under posts.
		 */
		add_theme_support( 'ssl-alp-cross-references' );

		/**
		 * Add support for showing featured images on inventory items.
		 */
		if ( labbook_ssl_alp_inventory_enabled() ) {
			add_theme_support( 'post-thumbnails', array( 'ssl-alp-inventory' ) );
		}

		// Get default theme options.
		$labbook_default_options = labbook_get_theme_option_defaults();
	}
endif;
add_action( 'after_setup_theme', 'labbook_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function labbook_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	$GLOBALS['content_width'] = apply_filters( 'labbook_content_width', 640 );
}
add_action( 'after_setup_theme', 'labbook_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function labbook_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'labbook' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'labbook' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'labbook_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function labbook_scripts() {
	wp_enqueue_style(
		'fontawesome',
		get_template_directory_uri() . '/vendor/font-awesome/css/font-awesome.min.css',
		array(),
		LABBOOK_VERSION
	);

	wp_enqueue_style(
		'labbook-style',
		get_stylesheet_uri(),
		array(),
		LABBOOK_VERSION
	);

	wp_enqueue_script(
		'labbook-navigation',
		get_template_directory_uri() . '/js/navigation.js',
		array(),
		LABBOOK_VERSION,
		true
	);

	wp_enqueue_script(
		'labbook-skip-link-focus-fix',
		get_template_directory_uri() . '/js/skip-link-focus-fix.js',
		array(),
		LABBOOK_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( labbook_get_option( 'show_unread_flags' ) && labbook_ssl_alp_unread_flags_enabled() ) {
		// Add support for unread flags.
		wp_enqueue_script(
			'labbook-post-read-status',
			get_template_directory_uri() . '/js/post-read-status.js',
			array(
				'jquery',
				'wp-api',
			),
			LABBOOK_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'labbook_scripts' );

if ( ! function_exists( 'labbook_add_advanced_search_query_var' ) ) :
	/**
	 * Add support for the advanced search query var.
	 *
	 * @param string[] $query_vars Array of current query variables.
	 */
	function labbook_add_advanced_search_query_var( $query_vars ) {
		$query_vars[] = 'labbook_advanced_search';

		return $query_vars;
	}
endif;
add_filter( 'query_vars', 'labbook_add_advanced_search_query_var' );

if ( ! function_exists( 'labbook_show_advanced_search_form' ) ) :
	/**
	 * Show the advanced search form on the search page.
	 *
	 * This shows the advanced search form instead of search results. In any case, search results
	 * are followed by the advanced search form; this function exists to show the advanced search
	 * page without showing an empty search result alongside it.
	 *
	 * @param string $original_template Original template.
	 */
	function labbook_show_advanced_search_form( $original_template ) {
		if ( get_query_var( 'labbook_advanced_search' ) && labbook_ssl_alp_advanced_search_enabled() ) {
			// Show advanced search form instead of search results.
			define( 'LABBOOK_PAGE_SHOW_ADVANCED_SEARCH_FORM', true );

			return get_search_template();
		} else {
			return $original_template;
		}
	}
endif;
add_action( 'template_include', 'labbook_show_advanced_search_form' );

if ( ! function_exists( 'labbook_get_content_with_toc' ) ) :
	/**
	 * Insert table of contents into post.
	 *
	 * @param string $content The post content.
	 */
	function labbook_get_content_with_toc( $content ) {
		$post = get_post();

		if ( ! labbook_php_dom_extension_loaded() || ! labbook_get_option( 'show_page_table_of_contents' ) ) {
			return $content;
		}

		if ( is_null( $post ) || ! is_page( $post ) ) {
			return $content;
		}

		// Get contents hierarchy.
		$content = labbook_generate_post_contents( $content, $hierarchy );

		if ( is_null( $hierarchy ) || ! $hierarchy->count() ) {
			// Table of contents was not generated or has no entries.
			return $content;
		}

		$toc  = '<div class="entry-toc entry-toc-' . get_the_ID() . '">';
		$toc .= '<h3 class="entry-toc-title">' . esc_html__( 'Contents', 'labbook' ) . '</h3>';
		$toc .= labbook_get_toc( $hierarchy, labbook_get_option( 'table_of_contents_max_depth' ) );
		$toc .= '</div>';

		return $toc . $content;
	}
endif;
add_filter( 'the_content', 'labbook_get_content_with_toc' );

if ( ! function_exists( 'labbook_add_content_layout_mode_admin_bar_menu' ) ) :
	/**
	 * Add link to control the user's content layout mode in admin bar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The admin bar.
	 */
	function labbook_add_content_layout_mode_admin_bar_menu( $admin_bar ) {
		if ( ! labbook_is_content_layout_mode_applicable() ) {
			return;
		}

		$current_url  = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_user = ( is_user_logged_in() ) ? wp_get_current_user() : null;

		if ( is_null( $current_user ) ) {
			return;
		}

		$mode = labbook_content_layout_mode();

		if ( 'excerpt' === $mode ) {
			$set_mode = 'full';
			$title = __( 'Show Full Posts', 'labbook' );
			$description = __( 'Show full posts instead of short excerpts in the main post list', 'labbook' );
		} else {
			$set_mode = 'excerpt';
			$title = __( 'Show Post Excerpts', 'labbook' );
			$description = __( 'Show short excerpts instead of full posts in the main post list', 'labbook' );
		}

		$admin_bar->add_menu(
			array(
				'parent' => 'top-secondary', // On the right side.
				'id'     => 'labbook-theme-settings',
				'title'  => __( 'Display', 'labbook' ),
			)
		);

		// Toggle layout mode link.
		$admin_bar->add_node(
			array(
				'parent' => 'labbook-theme-settings',
				'id'     => 'labbook-toggle-content-layout-mode',
				'title'  => esc_html( $title ),
				'meta'   => array(
					'title' => esc_html( $description ),
				),
				'href'   => add_query_arg(
					array(
						'labbook_recent_posts_content_layout_mode' => $set_mode,
						'redirect_to'                              => $current_url,
					)
				),
			)
		);

		// Reset to default layout link.
		$admin_bar->add_node(
			array(
				'parent' => 'labbook-theme-settings',
				'id'     => 'labbook-reset-content-layout-mode',
				'title'  => esc_html__( 'Reset Layout Mode', 'labbook' ),
				'meta'   => array(
					'title' => esc_html__( 'Reset the layout mode to the default.', 'labbook' ),
				),
				'href'   => add_query_arg(
					array(
						'labbook_recent_posts_content_layout_mode' => 'default',
						'redirect_to'                              => $current_url,
					)
				),
			)
		);
	}
endif;
add_filter( 'admin_bar_menu', 'labbook_add_content_layout_mode_admin_bar_menu' );


if ( ! function_exists( 'labbook_handle_toggle_content_layout_mode' ) ) :
	/**
	 * Handle when the user requests to toggle the post display setting.
	 */
	function labbook_handle_toggle_content_layout_mode() {
		global $allowed_content_layout_modes;

		if ( ! labbook_is_content_layout_mode_applicable() ) {
			return;
		}

		if ( ! array_key_exists( 'labbook_recent_posts_content_layout_mode', $_REQUEST ) ) {
			return;
		}

		$current_user = ( is_user_logged_in() ) ? wp_get_current_user() : null;

		if ( is_null( $current_user ) ) {
			return;
		}

		$mode = sanitize_key( $_REQUEST['labbook_recent_posts_content_layout_mode'] );

		if ( in_array( $mode, $allowed_content_layout_modes, true ) ) {
			// Set a very long cookie.
			setcookie( 'labbook_recent_posts_content_layout_mode', $mode, time() + 10 * YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		} elseif ( 'default' === $mode ) {
			// User wants to reset to the theme default. Delete the cookie.
			if ( array_key_exists( 'labbook_recent_posts_content_layout_mode', $_COOKIE ) ) {
				unset( $_COOKIE['labbook_recent_posts_content_layout_mode'] );
			}

			// Use null cookie value and set to expire in the past.
			setcookie( 'labbook_recent_posts_content_layout_mode', '', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}

		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = remove_query_arg(
				wp_removable_query_args(),
				wp_unslash( $_REQUEST['redirect_to'] )
			);
		} else {
			$redirect_to = '';
		}

		$application = 'WordPress/Labbook Theme Toggle Post Display';

		if ( $redirect_to ) {
			// Send user back to where they were.
			wp_safe_redirect( $redirect_to, 302, $application );
		} else {
			// Send user to front page.
			wp_safe_redirect( home_url(), 302, $application );
		}
		exit;
	}
endif;
add_filter( 'init', 'labbook_handle_toggle_content_layout_mode' );

/**
 * Check if Academic Labbook Plugin is available on this site.
 *
 * Ideally we'd just use the core function is_plugin_active() but this is not
 * imported by default on the front end.
 */
function labbook_ssl_alp_active() {
	$plugin = 'ssl-alp/alp.php';

	$blog_plugins = (array) get_option( 'active_plugins', array() );
	$blog_active  = in_array( $plugin, $blog_plugins, true );

	if ( is_multisite() ) {
		$network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
		$network_active  = isset( $network_plugins[ $plugin ] );
	} else {
		$network_active = false;
	}

	return $blog_active || $network_active;
}

if ( ! function_exists( 'labbook_is_content_layout_mode_applicable' ) ) :
	/**
	 * Check if the current page is applicable to having a content layout mode.
	 *
	 * Content layout modes are applicable on post lists, but not search results,
	 * and not in the Customizer (where it would cause confusion to use the current
	 * user's setting and not the default theme setting).
	 */
	function labbook_is_content_layout_mode_applicable() {
		if ( is_single() || is_search() || is_customize_preview() || is_admin() || ! is_admin_bar_showing() || get_query_var( 'labbook_advanced_search' ) ) {
			return false;
		}

		return true;
	}
endif;

/**
 * Get the content layout mode, used to determine whether to show full posts or
 * excerpts in post lists.
 *
 * If the user has set their own setting, this returns that; otherwise, the
 * theme setting is used.
 */
function labbook_content_layout_mode() {
	global $allowed_content_layout_modes;

	if ( labbook_is_content_layout_mode_applicable() ) {
		$current_user = ( is_user_logged_in() ) ? wp_get_current_user() : null;

		if ( ! is_null( $current_user ) && array_key_exists( 'labbook_recent_posts_content_layout_mode', $_COOKIE ) ) {
			$mode = sanitize_key( $_COOKIE['labbook_recent_posts_content_layout_mode'] );

			if ( ! empty( $mode ) && in_array( $mode, $allowed_content_layout_modes, true ) ) {
				return $mode;
			}
		}
	}

	return labbook_get_option( 'content_layout' );
}

/**
 * Check if DOM extension is available in order to show tables of contents.
 */
function labbook_php_dom_extension_loaded() {
	return extension_loaded( 'dom' );
}

/**
 * Check if advanced search capabilities provided by the ALP plugin are available and enabled.
 */
function labbook_ssl_alp_advanced_search_enabled() {
	global $ssl_alp;

	if ( ! labbook_ssl_alp_active() ) {
		// Plugin is disabled.
		return false;
	}

	return $ssl_alp->search->current_user_can_advanced_search();
}

/**
 * Check if coauthors provided by the ALP plugin are available and enabled.
 */
function labbook_ssl_alp_coauthors_enabled() {
	if ( ! labbook_ssl_alp_active() ) {
		// Plugin is disabled.
		return false;
	} elseif ( ! get_option( 'ssl_alp_allow_multiple_authors' ) ) {
		// Coauthors are disabled.
		return false;
	}

	return true;
}

/**
 * Check if crossreferences provided by the ALP plugin are available and enabled.
 */
function labbook_ssl_alp_crossreferences_enabled() {
	if ( ! labbook_ssl_alp_active() ) {
		// Plugin is disabled.
		return false;
	} elseif ( ! get_option( 'ssl_alp_enable_crossreferences' ) ) {
		// Cross-references are disabled.
		return false;
	}

	return true;
}

/**
 * Check if edit summaries provided by the ALP plugin are available and enabled.
 */
function labbook_ssl_alp_edit_summaries_enabled() {
	if ( ! labbook_ssl_alp_active() ) {
		// Plugin is disabled.
		return false;
	} elseif ( ! get_option( 'ssl_alp_enable_edit_summaries' ) ) {
		// Tracking of edit summaries is disabled.
		return false;
	}

	return true;
}

/**
 * Check if unread flags provided by the ALP plugin are available and enabled.
 */
function labbook_ssl_alp_unread_flags_enabled() {
	if ( ! labbook_ssl_alp_active() ) {
		// Plugin is disabled.
		return false;
	} elseif ( ! get_option( 'ssl_alp_flag_unread_posts' ) ) {
		// Unread flags are disabled.
		return false;
	}

	return true;
}

/**
 * Check if inventory capabilities provided by the ALP plugin are available and enabled.
 */
function labbook_ssl_alp_inventory_enabled() {
	global $ssl_alp;

	if ( ! labbook_ssl_alp_active() ) {
		// Plugin is disabled.
		return false;
	} elseif ( ! get_option( 'ssl_alp_enable_inventory' ) ) {
		// Inventory is disabled.
		return false;
	}

	return true;
}

/**
 * Page table of contents generator.
 */
require get_template_directory() . '/inc/class-labbook-toc-menu-level.php';

/**
 * Hierarchical taxonomy term select list builder.
 */
require get_template_directory() . '/inc/class-labbook-search-term-walker.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
