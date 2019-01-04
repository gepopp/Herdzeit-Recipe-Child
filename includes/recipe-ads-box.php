<div class="recipe-box white-block recipe-ads-box" style="height: 98%">
    <div class="blog-media">
        <img src="<?= get_stylesheet_directory_uri() ?>/images/Herdzeit_FullLogo_ads_box.jpg" class="embed-responsive-item wp-post-image" />
        <div class="ratings">
            Werbung
        </div>
    </div>
    <div class="content-inner banner-content">
            <?= do_shortcode('[bsa_pro_ad_space id=3]'); ?>
    </div>
    <div class="clearfix"></div>
    <div class="content-footer">
        <div class="content-inner">
            <div class="widget-title-wrap">
                <h5 class="widget-title">
                    <?php _e( 'Newsletter', 'recipe' ) ?>
                </h5>
            </div>
            <div id="nl-form">
            <form id="subForm" class="js-cm-form" action="https://www.createsend.com/t/subscribeerror?description=" method="post" data-id="30FEA77E7D0A9B8D7616376B90063231C5792E80AE964688F9498716ADBAE2C6B17D14328577DFB59E3910E1DB7682A18AAE8395E724FC9BAF10C292766C89E1">
                <label for="fieldEmail">E-Mail</label><br />
                <input id="fieldEmail" class="js-cm-email-input form-control" name="cm-cjdilt-cjdilt" type="email" required />
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="" id="agb">
                        Ich akzeptiere die AGB.
                    </label>
                </div>
                <button class="js-cm-submit-button btn btn-block" type="submit">senden</button>
            </form>
            </div>
        </div>
    </div>
</div>