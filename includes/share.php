<?php
$enable_share = recipe_get_option( 'enable_share' );

if( $enable_share == 'yes' ):
$facebook_share = recipe_get_option( 'facebook_share' );
$twitter_share = recipe_get_option( 'twitter_share' );
$google_share = recipe_get_option( 'google_share' );
$linkedin_share = recipe_get_option( 'linkedin_share' );
$tumblr_share = recipe_get_option( 'tumblr_share' );
?>
    <div class="dh_free_whatsapp_share_button_container text-right" style="max-width: 100%">
        <ul class="list-inline" >
            <li style="clear: none; text-align: center;width: 25%;  ">
                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= get_permalink()?>">
                    <i class="fa fa-facebook  fa-2x" style="padding-top: 10px;"></i>
                </a></li>
            <li style="clear: none; text-align: center;width: 25%;  ">
                <a target="_blank" href="https://twitter.com/intent/tweet?text=<?= get_the_title() ?>&url=<?= get_permalink() ?>">
                    <i class="fa fa-twitter fa-2x" style="padding-top: 10px;"></i>
                </a></li>

            <li style="clear: none; text-align: center;width: 25%;  ">
                <a target="_blank" data-pin-do="buttonBookmark" href="http://pinterest.com/pin/create/button/?url=<?= get_permalink() ?>&media=<?= get_the_post_thumbnail_url(null, 'large')?>&description=<?= get_the_excerpt()?>">
                    <i class="fa fa-pinterest fa-2x pinterest" style="padding-top: 10px;"></i>
                </a></li>
        </ul>
    </div>
<?php endif; ?>