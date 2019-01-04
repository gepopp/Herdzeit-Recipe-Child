<?php get_header();

$term = get_term_by('slug', get_query_var('season'), 'season');

$cur_page = (get_query_var('paged')) ? get_query_var('paged') : 1; //get curent page

$term_id = $term->term_id;

$args = array(
    'post_type' => 'recipe',
    'post_status' => 'publish',
    'paged' => $cur_page,
    'posts_per_page' => 6,
    'tax_query' => array(
        array(
            'taxonomy' => 'season',
            'field' => 'slug',
            'terms' => get_query_var('season')
        ),
    ),
);

$recipes = new WP_Query($args);

$page_links_total = (int) $recipes->max_num_pages;

$pagination = paginate_links(
    array(
        'prev_next' => false,
        'end_size' => 2,
        'mid_size' => 2,
        'total' => $page_links_total,
        'current' => $cur_page,

    )
);

?>


<?php if ($recipes->have_posts()): ?>
    <section>
        <div class="container">
            <div class="row">
                <?php
                $counter = 0;
                $counterRow = 1;
                while ($recipes->have_posts()) {

                    if ($counter == 3) {
                        echo '</div><div class="row row-eq-height">';
                        $counter = 0;
                        $counterRow++;
                    }


                    if ($counterRow == 2 && $counter == 0) {
                        ?>
                        <div class="flex-holder">
                            <div class="col-sm-8 tax-middle-row">
                                <?php include(locate_template('includes/recipe-saison-box.php')); ?>
                            </div>
                            <div class="col-sm-4 recipe-ads-box tax-middle-row">
                                <?php include(locate_template('includes/recipe-ads-box.php')); ?>
                            </div>
                        </div>
                        <?php
                        $counter = 3;
                    } else {
                        $recipes->the_post();
                        $counter++;
                        echo '<div class="col-sm-4">';
                        include(locate_template('includes/recipe-box.php'));
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <?php if (!empty($pagination)): ?>
                <div class="pagination">
                    <?php echo $pagination; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php else: ?>
    <section>
        <div class="container">
            <div class="white-block">
                <div class="content-inner">
                    <?php esc_html_e('No recipes found matching your criteria', 'recipe') ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
    <style>

    </style>


<?php get_footer() ?>