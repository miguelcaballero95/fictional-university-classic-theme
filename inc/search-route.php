<?php

add_action('rest_api_init', 'university_register_search');

function university_register_search() {
    register_rest_route('university/v1', 'search', [
        'methods' => WP_REST_Server::READABLE,
        'callback'  => 'university_search_results'
    ]);
}
function university_search_results($data) {

    $main_query = new WP_Query([
        'post_type' => ['post', 'page', 'professor', 'program', 'campus', 'event'],
        's'         => sanitize_text_field($data['term'])
    ]);

    $results = [
        'general_info'  => [],
        'professors'    => [],
        'programs'      => [],
        'events'        => [],
        'campuses'      => []
    ];

    while ($main_query->have_posts()) {
        $main_query->the_post();
        $post_type = get_post_type();

        if ($post_type == 'post' || $post_type == 'page')
            $results['general_info'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
        if ($post_type == 'professor')
            $results['professors'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
        if ($post_type == 'program')
            $results['programs'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
        if ($post_type == 'campus')
            $results['campuses'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
        if ($post_type == 'event')
            $results['events'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
    }

    return $results;
}
