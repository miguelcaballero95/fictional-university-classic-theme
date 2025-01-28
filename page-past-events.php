<?php get_header(); ?>
<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>)"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title">
            Past Events
        </h1>
        <div class="page-banner__intro">
            <p>a recap of our past events.</p>
        </div>
    </div>
</div>
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
        $event_date = new DateTime(get_field('event_date')); ?>
        <div class="event-summary">
            <a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
                <span class="event-summary__month">
                    <?php echo $event_date->format('M'); ?>
                </span>
                <span class="event-summary__day">
                    <?php echo $event_date->format('d'); ?>
                </span>
            </a>
            <div class="event-summary__content">
                <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                <p><?php echo wp_trim_words(get_the_content(), 18) ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
            </div>
        </div>
    <?php
    }
    echo paginate_links([
        'total'     => $past_events->max_num_pages
    ]); ?>
</div>
<?php get_footer();
