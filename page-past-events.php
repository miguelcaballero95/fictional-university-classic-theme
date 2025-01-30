<?php
get_header();
page_banner([
    'title'     => 'Past Events',
    'subtitle'  => 'A recap of our past events.'
]); ?>
<div class="container container--narrow page-section">
    <?php
    $past_events = new WP_Query([
        'post_type'         => 'event',
        'orderby'           => 'meta_value',
        'meta_key'          => 'event_date',
        'order'             => 'ASC',
        'meta_query'        => [
            [
                'key'       => 'event_date',
                'compare'   => '<',
                'value'     => date('Ymd'),
                'type'      => 'numeric'
            ]
        ],
        'paged'         => get_query_var('paged', 1)
    ]);
    while ($past_events->have_posts()) {
        $past_events->the_post();
        get_template_part('template-parts/content', get_post_type());
    }
    echo paginate_links([
        'total'     => $past_events->max_num_pages
    ]); ?>
</div>
<?php get_footer();
