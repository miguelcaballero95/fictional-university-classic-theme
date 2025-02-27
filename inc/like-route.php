<?php

add_action('rest_api_init', 'university_like_routes');
function university_like_routes() {
    register_rest_route('university/v1', 'manageLike', [
        [
            'methods' => WP_REST_Server::CREATABLE,
            'callback'  => 'create_like',
            'permission_callback'   => '__return_true'
        ],
        [
            'methods' => WP_REST_Server::DELETABLE,
            'callback'  => 'delete_like',
            'permission_callback'   => '__return_true'
        ]

    ]);
}

function create_like($data) {

    if (!is_user_logged_in())
        die("Only logged in users can create a like");

    $professor_id = sanitize_text_field($data['professorId']);

    $exist_query = new WP_Query([
        'post_type' => 'like',
        'author'    => get_current_user_id(),
        'meta_query' => [
            [
                'key'   => 'liked_professor_id',
                'compare' => '=',
                'value' => $professor_id
            ]
        ]
    ]);

    if ($exist_query->found_posts || get_post_type($professor_id) != 'professor')
        die("Invalid professor id");

    return wp_insert_post([
        'post_type'     => 'like',
        'post_status'   => 'publish',
        'post_title'    => 'Test',
        'meta_input'    => [
            'liked_professor_id'    => $professor_id
        ]
    ]);
}

function delete_like($data) {
    $like_id = sanitize_text_field($data['like']);

    if (!is_user_logged_in())
        die("Only logged in users can delete a like");

    if (get_current_user_id() != get_post_field('post_author', $like_id) || get_post_type($like_id) != 'like')
        die("You do not have permission to delete that.");

    return wp_delete_post($like_id, true);
}
