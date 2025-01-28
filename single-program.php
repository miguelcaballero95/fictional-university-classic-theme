<?php

get_header();

while (have_posts()) {
    the_post(); ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php the_title(); ?></h1>
            <div class="page-banner__intro">
                <p>DON'T FORGET TO REPLACE ME LATER.</p>
            </div>
        </div>
    </div>

    <div class="container container--narrow page-section">
        <div class="generic-content">
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        All Programs
                    </a>
                    <span class="metabox__main">
                        <?php the_title(); ?>
                    </span>
                </p>
            </div>
            <?php the_content(); ?>
        </div>
        <?php
        $events = new WP_Query([
            'post_type'         => 'event',
            'posts_per_page'    => 2,
            'orderby'           => 'meta_value',
            'meta_key'          => 'event_date',
            'order'             => 'ASC',
            'meta_query'        => [
                [
                    'key'       => 'event_date',
                    'compare'   => '>=',
                    'value'     => date('Ymd'),
                    'type'      => 'numeric'
                ],
                [
                    'key'       => 'related_programs',
                    'compare'   => 'LIKE',
                    'value'     => '"' . get_the_ID() . '"'
                ]
            ]
        ]);

        if ($events->have_posts()): ?>
            <hr class="section-break">
            <h2 class="headline headline--medium">Upcoming <?php the_title(); ?> Events</h2>
            <?php
            while ($events->have_posts()) {
                $events->the_post();
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
                        <p><?php echo has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
                    </div>
                </div>
            <?php
            }
            wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
<?php
}
get_footer(); ?>