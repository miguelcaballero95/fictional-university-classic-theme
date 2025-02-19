<?php

require_once get_theme_file_path('/inc/search-route.php');

function university_custom_rest() {
    register_rest_field('post', 'author_name', [
        'get_callback'  => function () {
            return get_the_author();
        }
    ]);
}
add_action('rest_api_init', 'university_custom_rest');

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

    wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key=' . GOOGLE_API, null, '1.0', true);

    // Enqueue the main JavaScript file
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), ['jquery'], '1.0', true);

    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

    // Enqueue main and additional styles
    wp_enqueue_style('university-main-styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university-extra-styles', get_theme_file_uri('/build/index.css'));

    wp_localize_script('main-university-js', 'universityData', [
        'root_url'  => get_site_url()
    ]);
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

    // Enable support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professor-landscape', 400, 260, true);
    add_image_size('professor-portrait', 480, 650, true);
    add_image_size('page-banner', 1500, 350, true);
}
add_action('after_setup_theme', 'university_features');


/**
 * Adjusts the main query
 *
 * @param WP_Query $query The current query object.
 */
function university_adjust_queries(WP_Query $query) {

    // Ensure this runs only on the front end, for the 'campus' post type archive, and the main query
    if (!is_admin() && is_post_type_archive('campus') && $query->is_main_query()) {
        $query->set('posts_per_page', -1);
    }

    // Ensure this runs only on the front end, for the 'program' post type archive, and the main query
    if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

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

function page_banner(array $args = []) { ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['background'] ?? get_field('page_banner_background_image')['sizes']['page-banner'] ?? get_theme_file_uri('/images/ocean.jpg'); ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?? get_the_title(); ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle'] ?? get_field('page_banner_subtitle'); ?></p>
            </div>
        </div>
    </div>
<?php
}

function university_map_key($api) {
    $api['key'] = GOOGLE_API;
    return $api;
}
add_filter('acf/fields/google_map/api', 'university_map_key');
