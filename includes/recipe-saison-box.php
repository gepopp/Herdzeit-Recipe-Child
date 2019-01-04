<div class="recipe-box white-block" style="height: 98%">
    <div class="row">
        <div class="col-sm-12">
            <?php
            if (function_exists('get_wp_term_image'))
            {
                $meta_image = get_wp_term_image($term_id);
                //It will give category/term image url
            } ?>
            <div class="blog-media" style="max-height: 200px">
                <img src="<?= $meta_image ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">


            <div class="content-inner">
        <h3 style="margin-top: 0;"><?= single_term_title() ?></h3>
        <p>
            <?= $term->description ?>
        </p>
            </div>
    </div>
        <div class="col-sm-6">
            <div class="content-inner">
                <div class="widget-title-wrap">
                    <h5 class="widget-title">
                     <?php _e( 'Herdzeit\'s Infoecke', 'recipe' ) ?>
                    </h5>
                </div>
                <?php
                    $args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 5,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'season',
                                'field'    => 'slug',
                                'terms'    => get_query_var('season')
                            ),
                        ),
                    );
                    $posts = new WP_Query($args);
                    if($posts->have_posts()){
                        echo '<ul class="list-unstyled similar-recipes infobox-list">';
                        while($posts->have_posts()){
                            $posts->the_post();
                            ?>
                            <li style="text-align: left">
                                <!--<a href="<?= the_permalink() ?>" class="no-margin">
								<div class="embed-responsive embed-responsive-16by9">
									<img width="800" height="477" src="<?= the_post_thumbnail_url() ?>" class="embed-responsive-item wp-post-image" alt="">								</div>
							</a>-->
                                <!--<div class="infobox-title">-->
                                    <a href="<?= the_permalink( ) ?>"><?= the_title() ?></a>
                                <!--</div>-->
						    </li>
                        <?php
                        }
                        echo '</ul>';
                    }

                    wp_reset_postdata()
                ?>
                <a href="/blog?season=<?= get_query_var('season')?>" class="btn btn-block">Weitere Info's</a>
            </div>
        </div>
    </div>
    </div>
