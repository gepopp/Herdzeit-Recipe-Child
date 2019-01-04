<?php
/*
	Template Name: Members
*/
session_start();
get_header();
the_post();
?>

<?php
$number = get_option('posts_per_page');
$cur_page = (get_query_var('paged')) ? get_query_var('paged') : 1; //get curent page
$offset = ($cur_page - 1) * $number;
$args = array(
    'orderby' => 'display_name',
    'order' => 'ASC',
    'count_total' => true,
    'number' => $number,
    'offset' => $offset,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'phantom_user',
            'compare' => '==',
            'value' => '0'
        ),
        array(
            'key' => 'phantom_user',
            'compare' => 'NOT EXISTS'
        )
    ),
);
$users = new WP_User_Query($args);
$total_users = $users->get_total();
$total_pages = ceil($total_users / $number);

if (!empty($users->results)) {
    ?>
    <section>
        <div class="container">
            <div class="section-title">
                <h1 class="text-center h3-size">
                    <i class="fa fa-users"></i>
                    <?php _e('Chefs In Our Kitchen', 'recipe'); ?>
                </h1>
            </div>
            <div class="row">
                <?php
                $pagination = paginate_links(
                    array(
                        'base' => esc_url(add_query_arg('paged', '%#%')),
                        'prev_next' => true,
                        'end_size' => 2,
                        'mid_size' => 2,
                        'total' => $total_pages,
                        'current' => $cur_page,
                        'prev_next' => false
                    )
                );

                $counter = 0;
                foreach ($users->results as $user) {
                    if ($counter == 3) {
                        echo '</div><div class="row">';
                        $counter = 0;
                    }
                    $counter++;
                    ?>
                    <div class="col-md-4">
                        <div class="white-block member-block">
                            <div class="member-avatar">
                                <?php
                                $avatar_url = recipe_get_avatar_url(get_avatar($user->ID, 150));
                                if (!empty($avatar_url)):
                                    ?>
                                    <img src="<?php echo esc_url($avatar_url) ?>" class="img-responsive" alt="author"/>
                                <?php
                                endif;
                                ?>
                            </div>
                            <div class="memeber-holder">
                                <a href="<?php echo get_author_posts_url($user->ID); ?>" class="blog-title">
                                    <h5><?php echo !empty($user->display_name) ? $user->display_name : $user->user_login; ?></h5>
                                </a>
                                <ul class="list-unstyled post-meta">
                                    <li>
                                        <?php recipe_user_rating($user->ID); ?>
                                    </li>
                                    <li>
                                        <?php
                                        _e('Joined: ', 'recipe');
                                        $timestamp = strtotime($user->user_registered);
                                        echo date_i18n('F j, Y', $timestamp);
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        _e('Wrote: ', 'recipe');
                                        $recipes = count_user_posts($user->ID, 'recipe');
                                        echo $recipes;
                                        echo $recipes == 1 ? _e(' recipe', 'recipe') : _e(' recipes', 'recipe');
                                        ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            if (!empty($pagination)): ?>
                <div class="pagination">
                    <?php echo $pagination; ?>
                </div>
            <?php
            endif;
            ?>
        </div>
    </section>
    <?php
}
?>
<?php get_footer(); ?>