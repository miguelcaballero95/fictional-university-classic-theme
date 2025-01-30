<?php

get_header();

while (have_posts()) {
    the_post();
    page_banner(); ?>
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
        $related_professors = new WP_Query([
            'post_type'         => 'professor',
            'posts_per_page'    => -1,
            'orderby'           => 'title',
            'order'             => 'ASC',
            'meta_query'        => [
                [
                    'key'       => 'related_programs',
                    'compare'   => 'LIKE',
                    'value'     => '"' . get_the_ID() . '"'
                ]
            ]
        ]);

        if ($related_professors->have_posts()): ?>
            <hr class="section-break">
            <h2 class="headline headline--medium"><?php the_title(); ?> Profressors</h2>
            <ul class="professor-cards">
                <?php
                while ($related_professors->have_posts()) {
                    $related_professors->the_post(); ?>
                    <li class="professor-card__list-item">
                        <a class="professor-card" href="<?php the_permalink(); ?>">
                            <img class="professor-card__image" src="<?php the_post_thumbnail_url('professor-landscape'); ?>" alt="">
                            <span class="professor-card__name"><?php the_title(); ?></span>
                        </a>
                    </li>
                <?php
                }
                wp_reset_postdata(); ?>
            </ul>
        <?php endif; ?>
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
                get_template_part('template-parts/content', get_post_type());
            }
            wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
<?php
}
get_footer(); ?>