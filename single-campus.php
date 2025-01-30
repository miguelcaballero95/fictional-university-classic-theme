<?php

get_header();

while (have_posts()) {
    the_post();
    page_banner();
    $map_location = get_field('map_location'); ?>
    <div class="container container--narrow page-section">
        <div class="generic-content">
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        All Campuses
                    </a>
                    <span class="metabox__main">
                        <?php the_title(); ?>
                    </span>
                </p>
            </div>
            <?php the_content(); ?>
        </div>
        <div class="acf-map">
            <div class="marker" data-lat="<?php echo $map_location['lat']; ?>" data-lng="<?php echo $map_location['lng'] ?>">
                <h3><?php the_title(); ?></h3>
                <?php echo $map_location['address']; ?>
            </div>
        </div>
        <?php
        $related_programs = new WP_Query([
            'post_type'         => 'program',
            'posts_per_page'    => -1,
            'orderby'           => 'title',
            'order'             => 'ASC',
            'meta_query'        => [
                [
                    'key'       => 'related_campus',
                    'compare'   => 'LIKE',
                    'value'     => '"' . get_the_ID() . '"'
                ]
            ]
        ]);

        if ($related_programs->have_posts()): ?>
            <hr class="section-break">
            <h2 class="headline headline--medium">Programs available at this campus</h2>
            <ul class="min-list link-list">
                <?php
                while ($related_programs->have_posts()) {
                    $related_programs->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?>
                        </a>
                    </li>
                <?php
                }
                wp_reset_postdata(); ?>
            </ul>
        <?php endif; ?>
    </div>
<?php
}
get_footer(); ?>