<?php
function inforow_func( $atts, $content ){
    extract( shortcode_atts( array(
        'icon' => '',
        'title' => '',
        'small_title' => '',
        'number' => '3',
        'style' => '1',
        'source' => '',
        'category' => '',
        'cuisine' => '',
        'season'  => '',
    ), $atts ) );
    $permalink = recipe_get_permalink_by_tpl( 'page-tpl_search' );

    $args = array(
        'tax_query' => array()
    );

    $no_title = false;
    ob_start();

    ?>
    <section class="<?php echo $no_title ? 'no-title' : '' ?>">
        <div class="container">
                <div class="section-title clearfix">
                    <h3 class="pull-left">
                            <i class="fa fa-info"></i>Herdzeit Info
                    </h3>
                    <?php if( !empty( $small_title ) ): ?>
                        <span class="btn pull-right">Werbung</span>
                    <?php endif; ?>
                </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="widget white-block clearfix infrow-white-block" id="nl-form">
                        <div class="widget-title-wrap">
                            <h5 class="widget-title">
                                Newsletter            </h5>
                        </div>
                        <form id="subForm" class="js-cm-form" action="https://www.createsend.com/t/subscribeerror?description=" method="post" data-id="30FEA77E7D0A9B8D7616376B90063231C5792E80AE964688F9498716ADBAE2C6B17D14328577DFB59E3910E1DB7682A18AAE8395E724FC9BAF10C292766C89E1">

                            <p>
                                <label for="fieldName">Name</label><br>
                                <input id="fieldName" name="cm-name" type="text" class="form-control">
                            </p>
                            <p>
                                <label for="fieldEmail">E-Mail</label><br>
                                <input id="fieldEmail" class="js-cm-email-input form-control" name="cm-cjdilt-cjdilt" type="email" required="">
                            </p>
                            <p>
                            </p><div class="checkbox">
                                <label>
                                    <input type="checkbox" value="" id="agb">
                                    Ich akzeptiere die Bedingungen in der <a href="/datenschutzerklaerung">Datenschutzerlärung</a>.
                                </label>
                            </div>
                            <p></p>
                            <p>
                                <button class="js-cm-submit-button btn btn-block" type="submit">senden</button>
                            </p>
                        </form>
                        <script type="text/javascript" src="https://js.createsend1.com/javascript/copypastesubscribeformlogic.js"></script>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="widget white-block clearfix infrow-white-block">
                        <?= do_shortcode('[bsa_pro_ad_space id=5]') ?>
                        <div class="clearfix"></div>
                    </div>

                </div>



            </div>
        </div>
    </section>
    <?php
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode( 'inforow', 'inforow_func' );


?>