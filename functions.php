<?php

/**
 * Functions File
 *
 * Contains functions for enqueueing scripts and styles,
 * registering theme features, and setting up navigation menus.
 */

/**
 * Enqueue theme scripts and styles.
 *
 * This function registers and enqueues all JavaScript and CSS files
 * used by the theme, including external resources such as Google Fonts
 * and Font Awesome icons.
 */
function university_files() {
    // Enqueue the main JavaScript file
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), ['jquery'], '1.0', true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    // Enqueue main and additional styles
    wp_enqueue_style('university-main-styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university-extra-styles', get_theme_file_uri('/build/index.css'));
}
add_action('wp_enqueue_scripts', 'university_files');

/**
 * Add theme features and functionality.
 *
 * This function registers navigation menu locations and enables support
 * for dynamic title tags.
 */
function university_features() {
    // Register navigation menus
    register_nav_menu('header-menu-location', 'Header Menu Location');
    register_nav_menu('footer-location-one', 'Footer Location One');
    register_nav_menu('footer-location-two', 'Footer Location Two');

    // Enable support for dynamic title tags
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'university_features');


/**
 * Adjusts the main query
 *
 * @param WP_Query $query The current query object.
 */
function university_adjust_queries(WP_Query $query) {
    // Ensure this runs only on the front end, for the 'event' post type archive, and the main query
    if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
        $query->set('orderby', 'meta_value');
        $query->set('order', 'ASC');
        $query->set('meta_key', 'event_date');
        $query->set('meta_query', [
            [
                'key'       => 'event_date',
                'compare'   => '>=',
                'value'     => date('Ymd'),
                'type'      => 'numeric'
            ]
        ]);
    }
}
add_action('pre_get_posts', 'university_adjust_queries');
