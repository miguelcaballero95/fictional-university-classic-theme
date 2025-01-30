<?php

get_header();

while (have_posts()) {
    the_post();
    page_banner([
        'title'         => get_the_title(),
        'subtitle'      => get_field('page_banner_subtitle')
    ]); ?>
    <div class="container container--narrow page-section">
        <?php
        $parent_page_id = wp_get_post_parent_id(get_the_ID());
        if ($parent_page_id): ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_the_permalink($parent_page_id); ?>">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        Back to <?php echo get_the_title($parent_page_id); ?>
                    </a>
                    <span class="metabox__main"><?php the_title(); ?></span>
                </p>
            </div>
        <?php endif; ?>

        <?php
        $child_pages = get_pages(['child_of' => get_the_ID()]);
        if ($parent_page_id || !empty($child_pages)): ?>
            <div class="page-links">
                <h2 class="page-links__title"><a href="<?php echo get_the_permalink($parent_page_id); ?>"><?php echo get_the_title($parent_page_id); ?></a></h2>
                <ul class="min-list">
                    <?php
                    $child_of = $parent_page_id !== 0 ? $parent_page_id : get_the_ID();
                    wp_list_pages([
                        'title_li'  => null,
                        'child_of'  => $child_of,
                        'short_column'  => 'menu_order'
                    ]); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="generic-content">
            <?php the_content(); ?>
        </div>
    </div>

<?php
}
get_footer(); ?>