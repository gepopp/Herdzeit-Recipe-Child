<?php
/*=============================
	DEFAULT SINGLE
=============================*/
get_header();
the_post();
global $recipe_slugs;
$post_pages = wp_link_pages(
    array(
        'before' => '',
        'after' => '',
        'link_before' => '<span>',
        'link_after' => '</span>',
        'next_or_number' => 'number',
        'nextpagelink' => __('&raquo;', 'recipe'),
        'previouspagelink' => __('&laquo;', 'recipe'),
        'separator' => ' ',
        'echo' => 0
    )
);

add_filter('the_step_content', 'wptexturize');
add_filter('the_step_content', 'convert_smilies', 20);
add_filter('the_step_content', 'wpautop');
add_filter('the_step_content', 'shortcode_unautop');
add_filter('the_step_content', 'prepend_attachment');
add_filter('the_step_content', 'wp_make_content_images_responsive');
add_filter('the_step_content', 'do_shortcode', 11);

$recipe_single_layout = recipe_get_option('recipe_single_layout');

$recipe_video = get_post_meta(get_the_ID(), 'recipe_video', true);
$recipe_video = recipe_parse_video_url($recipe_video);
$recipe_images = recipe_smeta_images('recipe_images', get_the_ID(), array());
$permalink = recipe_get_permalink_by_tpl('page-tpl_search');
$active_tab_set = false;
$active_tab_link_set = false;

$review_count = get_post_meta(get_the_ID(), 'count_review', true);
if (empty($review_count)) {
    $review_count = 0;
}

$average_review = get_post_meta(get_the_ID(), 'average_review', true);
if (empty($average_review)) {
    $average_review = 0.0;
}

$featured_image_data = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');

$recipe_ingredient = get_post_meta(get_the_ID(), 'recipe_ingredient', true);
$recipe_ingredients_ld = $recipe_ingredient;
$recipe_ingredients_ld = str_replace(array("#", "\r"), "", $recipe_ingredients_ld);
$recipe_ingredients_ld = explode("\n", $recipe_ingredients_ld);

$recipe_ingredients = explode("\n", $recipe_ingredient);

$recipe_prep_time = get_post_meta(get_the_ID(), 'recipe_prep_time', true);
$recipe_prep_time_str = recipe_prep_unix_time($recipe_prep_time);

$recipe_cook_time = get_post_meta(get_the_ID(), 'recipe_cook_time', true);
$recipe_cook_time_str = recipe_prep_unix_time($recipe_cook_time);

$recipe_ready_in = get_post_meta(get_the_ID(), 'recipe_ready_in', true);

$total_interval = 0;
if (!empty($recipe_prep_time_str)) {
    $total_interval += $recipe_prep_time_str;
}

if (!empty($recipe_cook_time_str)) {
    $total_interval += $recipe_cook_time_str;
}
$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names');
$json_tags = wp_get_post_terms(get_the_ID(), 'recipe-tag', $args);

$recipe_categories = get_the_terms( get_the_ID(), 'recipe-category' );
$recipe_cuisines = get_the_terms( get_the_ID(), 'recipe-cuisine' );

$recipe_steps = get_post_meta( get_the_ID(), 'recipe_steps', true );
//$recipe_steps = recipe_prepare_steps( $recipe_steps );
$recipe_steps = explode( "--", $recipe_steps );
foreach($recipe_steps as $recipe_step){
    $steps[] = [ '@type' => "HowToStep", "text" => $recipe_step ];
}
$steps = json_encode($steps, ENT_QUOTES);
$steps = preg_replace("/#(.*?)#/", "", $steps);
$steps = preg_replace("/\r\n\r\n/", ": ", $steps);

?>
    <!-- ********************** -->
    <!-- GOOGLE STRUCTURED DATA -->
    <!-- ********************** -->

<script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "Recipe",
  "name": "<?php the_title() ?>",
  "image": "<?php echo !empty($featured_image_data[0]) ? esc_url($featured_image_data[0]) : ''; ?>",
  "author": {
    "@type":"Person",
    "name":"<?php echo get_the_author_meta('display_name'); ?>"
  },
  "datePublished": "<?php the_time('Y-m-d') ?>",
  "description": "<?php echo get_the_excerpt(); ?>",
  <?php if ($review_count > 0): ?>
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?php echo $average_review; ?>",
    "reviewCount": "<?php echo $review_count; ?>"
  },
  <?php endif; ?>
  "recipeIngredient": <?php echo json_encode($recipe_ingredients_ld, JSON_UNESCAPED_UNICODE); ?>,
  "recipeYield": "<?php echo get_post_meta(get_the_ID(), 'recipe_yield', true); ?>",
  "prepTime": "<?php echo !empty($recipe_prep_time_str) ? recipe_time_to_iso8601_duration($recipe_prep_time_str) : '' ?>",
  "cookTime": "<?php echo !empty($recipe_cook_time_str) ? recipe_time_to_iso8601_duration($recipe_cook_time_str) : '' ?>",
  "totalTime": "<?php echo !empty($total_interval) ? recipe_time_to_iso8601_duration($total_interval) : '' ?>",
  "keywords": <?= json_encode($json_tags) ?>,
  "recipeCategory":"<?= ($recipe_categories[0]->name) ?>",
  "recipeCuisine": "<?= ($recipe_cuisines[0]->name) ?>",
  "recipeInstructions": <?= $steps ?>
   }
}


</script>
    <section class="single-blog">
        <input type="hidden" name="post-id" value="<?php the_ID() ?>">
        <div class="container">
            <div class="row">
                <?php
                if ($recipe_single_layout == 'left-sidebar') {
                    include(locate_template('includes/recipe-single-sidebar.php'));
                }
                ?>
                <div class="col-md-9">
                    <div class="white-block single-item">
                        <div class="blog-media">
                            <div class="tab-content">

                                <?php if (has_post_thumbnail()): ?>
                                    <div role="tabpanel" class="tab-pane fade <?php if ($active_tab_set == false) {
                                        echo 'in active';
                                        $active_tab_set = true;
                                    } ?>" id="tab_featured">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <?php the_post_thumbnail('post-thumbnail', array('class' => 'embed-responsive-item featured-image')) ?>
                                        </div>
                                    </div>
                                    <?php
                                    $class = '';
                                    if ($active_tab_link_set == false) {
                                        $class = 'active';
                                        $active_tab_link_set = true;
                                    }
                                    $available_media[] = '<li role="presentation" class="' . esc_attr($class) . ' WIDTH-CLASS"><a href="#tab_featured" role="tab" data-toggle="tab">' . __('BILDER', 'recipe') . '</a></li>';
                                    ?>
                                <?php endif; ?>

                                <?php if (!empty($recipe_images)): ?>
                                    <div role="tabpanel" class="tab-pane fade <?php if ($active_tab_set == false) {
                                        echo 'in active';
                                        $active_tab_set = true;
                                    } ?>" id="tab_gallery">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <ul class="list-unstyled post-slider embed-responsive-item">
                                                <?php
                                                foreach ($recipe_images as $recipe_image) {
                                                    $image_data = wp_get_attachment_image($recipe_image, 'post-thumbnail', false, array('class' => 'embed-responsive-item'));
                                                    if (!empty($image_data)) {
                                                        echo '<li>' . $image_data . '</li>';
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                    $class = '';
                                    if ($active_tab_link_set == false) {
                                        $class = 'active';
                                        $active_tab_link_set = true;
                                    }
                                    $available_media[] = '<li role="presentation" class="' . esc_attr($class) . ' WIDTH-CLASS"><a href="#tab_gallery" role="tab" data-toggle="tab">' . __('GALLERY', 'recipe') . '</a></li>';
                                    ?>
                                <?php endif; ?>

                                <?php if (!empty($recipe_video)): ?>
                                    <div role="tabpanel" class="tab-pane fade <?php if ($active_tab_set == false) {
                                        echo 'in active';
                                        $active_tab_set = true;
                                    } ?>" id="tab_video">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe src="<?php echo esc_url($recipe_video); ?>"
                                                    class="embed-responsive-item" allowfullscreen="true"></iframe>
                                        </div>
                                    </div>
                                    <?php
                                    $class = '';
                                    if ($active_tab_link_set == false) {
                                        $class = 'active';
                                        $active_tab_link_set = true;
                                    }
                                    $available_media[] = '<li role="presentation" class="' . esc_attr($class) . ' WIDTH-CLASS"><a href="#tab_video" role="tab" data-toggle="tab">' . __('VIDEO', 'recipe') . '</a></li>';
                                    ?>
                                <?php endif; ?>

                            </div>

                            <?php if (sizeof($available_media) > 1): ?>
                                <ul class="nav nav-tabs" role="tablist">
                                    <?php
                                    foreach ($available_media as $tab_link) {
                                        echo str_replace('WIDTH-CLASS', 'recipe-tab-' . sizeof($available_media), $tab_link);
                                    }
                                    ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="content-inner">

                            <h1 class="post-title h3-size"><?php the_title() ?></h1>

                            <div class="post-content clearfix">
                                <?php the_content(); ?>
                            </div>
                            <div class="visible-xs">
                                <?php
                                $related_posts = get_post_meta(get_the_ID(), 'related_posts', true);
                                if (!empty($related_posts)):
                                    $posts = explode(',', $related_posts);
                                    if (!empty($posts)):
                                        ?>

                                        <h5 class="widget-title" style="margin: 15px 0">
                                            <?php _e('Herdzeit\'s Infoecke', 'recipe') ?>
                                        </h5>
                                        <ul class="list-unstyled similar-recipes">
                                            <?php
                                            foreach ($posts as $post) {
                                                $post_id = trim($post);

                                                ?>
                                                <li style="text-align: left;margin-bottom: 15px;">
                                                    <a href="<?= get_the_permalink($post_id) ?>">
                                                        <?= get_the_title($post_id); ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    <?php endif;
                                endif;
                                wp_reset_postdata();
                                ?>


                            </div>

                            <?php
                            $recipe_steps = get_post_meta(get_the_ID(), 'recipe_steps', true);
                            if (!empty($recipe_ingredient) && !empty($recipe_steps)):
                                $recipe_steps = recipe_prepare_steps($recipe_steps);

                                ?>
                                <div class="row recipe-details">
                                    <div id="app">
                                        <ingredients ingredient-list="<?php echo $recipe_ingredient ?>"
                                                     portions="<?= get_post_meta(get_the_ID(), 'recipe_servings', true) ?>"
                                                     id="<?= get_the_ID() ?>"
                                                     user="<?= get_current_user_id() ?>"
                                        ></ingredients>
                                        <cookingstep icon="<?= get_stylesheet_directory_uri() ?>/images/spons_white.png"
                                                     id="<?= get_the_ID() ?>"></cookingstep>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <hr/>
                            <?php echo do_shortcode('[bsa_pro_ad_space id=2]') ?>
                        </div>
                    </div>


                    <?php
                    $tags = recipe_custom_tax('recipe-tag');
                    if (!empty($tags)):
                        ?>
                        <div class="post-tags white-block">
                            <div class="content-inner">
                                <?php _e('<i class="fa fa-tags"></i>' . __('Recipe tags: ', 'recipe'), 'recipe');
                                echo $tags; ?>
                            </div>
                        </div>
                    <?php
                    endif;
                    ?>

                    <?php comments_template('', true) ?>

                </div>
                <?php
                if ($recipe_single_layout == 'right-sidebar') {
                    include(locate_template('includes/recipe-single-sidebar.php'));
                }
                ?>
            </div>
        </div>
        <div id="qrcode" class="visible-print pull-right">
            <?php
            if (!file_exists(get_stylesheet_directory() . '/qrfiles/qr-' . get_the_ID() . '.png')) {
                require_once get_stylesheet_directory() . '/phpqrcode/phpqrcode.php';
                QRcode::png(get_the_permalink(), get_stylesheet_directory() . '/qrfiles/qr-' . get_the_ID() . '.png');
            }
            echo '<img src="' . get_stylesheet_directory_uri() . '/qrfiles/qr-' . get_the_ID() . '.png">';
            ?>
        </div>
    </section>
    <script>

    </script>
<?php get_footer(); ?>