<?php

add_action('rest_api_init', 'university_register_search');

function university_register_search() {
    register_rest_route('university/v1', 'search', [
        'methods' => WP_REST_Server::READABLE,
        'callback'  => 'university_search_results',
        'permission_callback'   => '__return_true'
    ]);
}
function university_search_results($data) {

    $main_query = new WP_Query([
        'post_type' => ['post', 'page', 'professor', 'program', 'campus', 'event'],
        's'         => sanitize_text_field($data['term']),
        'posts_per_page'    => -1
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
                'permalink' => get_the_permalink(),
                'post_type' => $post_type,
                'author_name'   => get_the_author()
            ];
        if ($post_type == 'professor')
            $results['professors'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(0, 'professor-landscape')
            ];
        if ($post_type == 'program') {
            $related_campuses = get_field('related_campus');
            if ($related_campuses)
                foreach ($related_campuses as $campus) {
                    $results['campuses'][] = [
                        'title'     => get_the_title($campus),
                        'permalink' => get_the_permalink($campus)
                    ];
                }
            $results['programs'][] = [
                'id'    => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
        }
        if ($post_type == 'campus')
            $results['campuses'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ];
        if ($post_type == 'event') {
            $event_date = new DateTime(get_field('event_date'));
            $results['events'][] = [
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $event_date->format('M'),
                'day'   => $event_date->format('d'),
                'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18)
            ];
        }
    }

    if (count($results['programs']) > 0) {
        $meta_query = ['relation' => 'OR'];

        foreach ($results['programs'] as $program) {
            $meta_query[] = [
                'key'   => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $program['id'] . '"'
            ];
        }

        $program_relationship_query = new WP_Query([
            'post_type'     => ['professor', 'event'],
            'meta_query'    => $meta_query
        ]);

        while ($program_relationship_query->have_posts()) {
            $program_relationship_query->the_post();
            $post_type = get_post_type();

            if ($post_type == 'professor')
                $results['professors'][] = [
                    'title'     => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(0, 'professor-landscape')
                ];
            if ($post_type == 'event') {
                $event_date = new DateTime(get_field('event_date'));
                $results['events'][] = [
                    'title'     => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $event_date->format('M'),
                    'day'   => $event_date->format('d'),
                    'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18)
                ];
            }
        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    $results['campuses'] = array_values(array_unique($results['campuses'], SORT_REGULAR));

    return $results;
}
