<?php
    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: https://docs.reduxframework.com
     * */   

    global $recipe_opts;

    if ( ! class_exists( 'Recipe_Options' ) ) {

        class Recipe_Options {

            public $args = array();
            public $sections = array();
            public $theme;
            public $ReduxFramework;

            public function __construct() {

                if ( ! class_exists( 'ReduxFramework' ) ) {
                    return;
                }

                // This is needed. Bah WordPress bugs.  ;)
                if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                    $this->initSettings();
                } else {
                    add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );
                }

            }

            public function initSettings() {

                // Just for demo purposes. Not needed per say.
                $this->theme = wp_get_theme();

                // Set the default arguments
                $this->setArguments();

                // Create the sections and fields
                $this->setSections();

                if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
                    return;
                }

                // If Redux is running as a plugin, this will remove the demo notice and links
                //add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

                $this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
            }

            // Remove the demo link and the notice of integrated demo from the redux-framework plugin
            function remove_demo() {

                // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
                if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
                    remove_filter( 'plugin_row_meta', array(
                        ReduxFrameworkPlugin::instance(),
                        'plugin_metalinks'
                    ), null, 2 );

                    // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                    remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
                }
            }

            public function setSections() {

                /**
                 * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
                 * */
                // Background Patterns Reader
                $sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
                $sample_patterns_url  = ReduxFramework::$_url . '../sample/patterns/';
                $sample_patterns      = array();

                if ( is_dir( $sample_patterns_path ) ) :

                    if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
                        $sample_patterns = array();

                        while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

                            if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
                                $name              = explode( '.', $sample_patterns_file );
                                $name              = str_replace( '.' . end( $name ), '', $sample_patterns_file );
                                $sample_patterns[] = array(
                                    'alt' => $name,
                                    'img' => $sample_patterns_url . $sample_patterns_file
                                );
                            }
                        }
                    endif;
                endif;

                /**********************************************************************
                ***********************************************************************
                OVERALL
                **********************************************************************/
                $this->sections[] = array(
                    'title' => __('Overall', 'recipe') ,
                    'icon' => '',
                    'desc' => __('This is basic section where you can set up main settings for your website.', 'recipe'),
                    'fields' => array(
                        //Show Top Bar
                        array(
                            'id' => 'show_top_bar',
                            'type' => 'select',
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'title' => __('Show Top Bar', 'recipe') ,
                            'desc' => __('Enable or hide top bar', 'recipe')
                        ),  
                        array(
                            'id' => 'enable_sticky',
                            'type' => 'select',
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'title' => __('Enable Sticky', 'recipe') ,
                            'desc' => __('Show or hide sticky menu', 'recipe'),
                            'default' => 'yes'
                        ),
                        //Site Logo
                        array(
                            'id' => 'site_logo',
                            'type' => 'media',
                            'title' => __('Site Logo', 'recipe') ,
                            'desc' => __('Upload site logo', 'recipe')
                        ),
                        //Favicon
                        array(
                            'id' => 'site_favicon',
                            'type' => 'media',
                            'title' => __('Site Favicon', 'recipe') ,
                            'desc' => __('Please upload favicon here in PNG or JPG format. <small>(18px 18px maximum size recommended)</small>)', 'recipe')
                        ), 
                        array(
                            'id'          => 'featured_recipes',
                            'type'        => 'text',
                            'title'       => __( 'Featured Recipes', 'recipe' ),
                            'desc'        => __('Input comma separated list of recipe IDs you wish to add as featured', 'recipe'),
                        ),
                        array(
                            'id'          => 'featured_slider_rotate',
                            'type'        => 'text',
                            'title'       => __( 'Featured Slider Auto Rotate', 'recipe' ),
                            'desc'        => __('Input auto rotate speed in ms. Leave empty to disable', 'recipe'),
                        ),                        
                        //Tumblr Top Bar Link
                        array(
                            'id' => 'cooking-levels',
                            'type' => 'textarea',
                            'title' => __('Cooking Levels', 'recipe') ,
                            'desc' => __('Input cooking levels separated by the new line. To add range for the certan level use this form( Rookie:0-10 )', 'recipe')
                        ),
                        array(
                            'id' => 'similar_recipes_num',
                            'type' => 'text',
                            'title' => __('Similar Recipes', 'recipe') ,
                            'desc' => __('Input number of similar recipes to show on recipe single page', 'recipe'),
                            'default' => 3
                        ),
                        array(
                            'id' => 'recipe_single_layout',
                            'type' => 'select',
                            'title' => __('Recipe Single Layout', 'recipe') ,
                            'desc' => __('Select recipe single layout', 'recipe'),
                            'options' => array(
                                'right-sidebar' => __( 'Right Sidebar', 'recipe' ),
                                'left-sidebar' => __( 'Left Sidebar', 'recipe' ),
                            ),
                            'default' => 'right-sidebar'
                        ),                        
                        array(
                            'id' => 'share_images',
                            'type' => 'select',
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' )
                            ),
                            'title' => __('All Users See All Images?', 'recipe') ,
                            'desc' => __('Enable or disable recipe images sharing.', 'recipe')
                        ) ,
                    )
                );
                
                /**********************************************************************
                ***********************************************************************
                SEO
                **********************************************************************/
                
                $this->sections[] = array(
                    'title' => __('Slugs', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Rename slugs which are being used in the theme.', 'recipe'),
                    'fields' => array(  
                        // Recipe
                        array(
                            'id' => 'trans_recipe',
                            'type' => 'text',
                            'title' => __('Recipe Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe post types.', 'recipe'),
                            'default' => 'recipe'
                        ) ,
                        
                        // Category
                        array(
                            'id' => 'trans_recipe-category',
                            'type' => 'text',
                            'title' => __('Recipe Category Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe category', 'recipe'),
                            'default' => 'recipe-category'
                        ) ,

                        // Tag
                        array(
                            'id' => 'trans_recipe-tag',
                            'type' => 'text',
                            'title' => __('Recipe Tag Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe tag', 'recipe'),
                            'default' => 'recipe-tag'
                        ) ,

                        // Cuisine
                        array(
                            'id' => 'trans_recipe-cuisine',
                            'type' => 'text',
                            'title' => __('Recipe Cuisine Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe cuisine', 'recipe'),
                            'default' => 'recipe-cuisine'
                        ) ,

                        // Ingredients
                        array(
                            'id' => 'trans_ingredients',
                            'type' => 'text',
                            'title' => __('Recipe Ingredients Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe ingredients', 'recipe'),
                            'default' => 'ingredients'
                        ) ,

                        // Keyword
                        array(
                            'id' => 'trans_keyword',
                            'type' => 'text',
                            'title' => __('Recipe Keyword Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe keyword', 'recipe'),
                            'default' => 'keyword'
                        ) ,

                        // Keyword
                        array(
                            'id' => 'trans_sort',
                            'type' => 'text',
                            'title' => __('Recipe Sort Slug', 'recipe') ,
                            'desc' => __('Input slug for the recipe sort', 'recipe'),
                            'default' => 'sort'
                        ) ,
                    )
                );
                

                /**********************************************************************
                ***********************************************************************
                SHARE
                **********************************************************************/
                
                $this->sections[] = array(
                    'title' => __('Share', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Post share options.', 'recipe'),
                    'fields' => array(
                        // Enable Share
                        array(
                            'id' => 'enable_share',
                            'type' => 'select',
                            'title' => __('Enable Share', 'recipe') ,
                            'desc' => __('<br />Enable or disable post share.', 'recipe'),
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'std' => 'yes'
                        ),
                        // Share Facebook
                        array(
                            'id' => 'facebook_share',
                            'type' => 'select',
                            'title' => __('Facebook Share', 'recipe') ,
                            'desc' => __('<br />Enable or disable post share on Facebook.', 'recipe'),
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'std' => 'yes'
                        ),
                        // Share Twitter
                        array(
                            'id' => 'twitter_share',
                            'type' => 'select',
                            'title' => __('Twitter Share', 'recipe') ,
                            'desc' => __('<br />Enable or disable post share on Twitter.', 'recipe'),
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'std' => 'yes'
                        ),
                        // Share Google+
                        array(
                            'id' => 'google_share',
                            'type' => 'select',
                            'title' => __('Google+ Share', 'recipe') ,
                            'desc' => __('<br />Enable or disable post share on Google+.', 'recipe'),
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'std' => 'yes'
                        ),
                        // Share Linkedin
                        array(
                            'id' => 'linkedin_share',
                            'type' => 'select',
                            'title' => __('Linkedin Share', 'recipe') ,
                            'desc' => __('<br />Enable or disable post share on Linkedin.', 'recipe'),
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'std' => 'yes'
                        ),
                        // Share Tumblr
                        array(
                            'id' => 'tumblr_share',
                            'type' => 'select',
                            'title' => __('Tumblr Share', 'recipe') ,
                            'desc' => __('<br />Enable or disable post share on Tumblr.', 'recipe'),
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'std' => 'yes'
                        ),
                    )
                );  

                /**********************************************************************
                ***********************************************************************
                SUBSCRIPTION
                **********************************************************************/
                
                $this->sections[] = array(
                    'title' => __('Subscription', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Set up subscription API key and list ID.', 'recipe'),
                    'fields' => array(
                        // Mail Chimp API
                        array(
                            'id' => 'mail_chimp_api',
                            'type' => 'text',
                            'title' => __('API Key', 'recipe') ,
                            'desc' => __('Type your mail chimp api key.', 'recipe')
                        ) , 
                        // Mail Chimp List ID
                        array(
                            'id' => 'mail_chimp_list_id',
                            'type' => 'text',
                            'title' => __('List ID', 'recipe') ,
                            'desc' => __('Type here ID of the list on which users will subscribe.', 'recipe')
                        ) ,
                    )
                );

                /**********************************************************************
                ***********************************************************************
                MESSAGEG
                **********************************************************************/
                
                $this->sections[] = array(
                    'title' => __('Messages', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Messaging settings.', 'recipe'),
                    'fields' => array(
                        array(
                            'id' => 'inform_user',
                            'type' => 'select',
                            'title' => __('Enable User Information', 'recipe') ,
                            'options' => array(
                                'yes' => __( 'Yes', 'recipe' ),
                                'no' => __( 'No', 'recipe' ),
                            ),
                            'desc' => __('Enable or disable sending information to user about accepting, declining, updating recipe.', 'recipe'),
                            'default' => 'yes'
                        ) ,                        
                        // Registrtion message subject
                        array(
                            'id' => 'registration_subject',
                            'type' => 'text',
                            'title' => __('Registration Message Subject', 'recipe') ,
                            'desc' => __('Type registration message subject.', 'recipe')
                        ) ,
                        // Registrtion message
                        array(
                            'id' => 'registration_message',
                            'type' => 'textarea',
                            'title' => __('Registration Message', 'recipe') ,
                            'desc' => __('Type registration message for confirming the link. Use %LINK% to place the link .', 'recipe')
                        ) , 
                        // Registration message sender name
                        array(
                            'id' => 'registration_sender_name',
                            'type' => 'text',
                            'title' => __('Registration Message Name', 'recipe') ,
                            'desc' => __('Type name from who the registration message is sent.', 'recipe')
                        ) ,
                        // Registration message sender email address
                        array(
                            'id' => 'registration_sender_email',
                            'type' => 'text',
                            'title' => __('Registration Message Email', 'recipe') ,
                            'desc' => __('Type email address from who the registration message is sent.', 'recipe')
                        ),

                        // Recover message subject
                        array(
                            'id' => 'recover_subject',
                            'type' => 'text',
                            'title' => __('Recover Password Message Subject', 'recipe') ,
                            'desc' => __('Type recover password subject', 'recipe')
                        ) , 
                        // Recover message
                        array(
                            'id' => 'recover_message',
                            'type' => 'textarea',
                            'title' => __('Recover Password Message', 'recipe') ,
                            'desc' => __('Type registration message for confirming the link. Use %USERNAME% to place the username. use %PASSWORD% to put new password', 'recipe')
                        ),
                        // Recover message sender name
                        array(
                            'id' => 'recover_sender_name',
                            'type' => 'text',
                            'title' => __('Recover Password Sender Name', 'recipe') ,
                            'desc' => __('Type name from who the recover message is sent.', 'recipe')
                        ) ,
                        // Recover message sender email address
                        array(
                            'id' => 'recover_sender_email',
                            'type' => 'text',
                            'title' => __('Recover Password Sender Email', 'recipe') ,
                            'desc' => __('Type email address from who the recover message is sent.', 'recipe')
                        ),

                        //Approved review sender email
                        array(
                            'id' => 'review_subject',
                            'type' => 'text',
                            'title' => __('Review Message Subject', 'recipe') ,
                            'desc' => __('Type review subject', 'recipe')
                        ) , 
                        // Approved review message
                        array(
                            'id' => 'review_message_approved',
                            'type' => 'textarea',
                            'title' => __('Approved Review message', 'recipe') ,
                            'desc' => __('Type review message for the submitter. Use %LINK% to put link, %NAME% to put name', 'recipe')
                        ),
                        // Declined review message
                        array(
                            'id' => 'review_message_declined',
                            'type' => 'textarea',
                            'title' => __('Decline message', 'recipe') ,
                            'desc' => __('Type review message for the submitter. Use %NAME% to put name', 'recipe')
                        ),                        
                        // Review sender name
                        array(
                            'id' => 'review_sender_name',
                            'type' => 'text',
                            'title' => __('Review Sender Name', 'recipe') ,
                            'desc' => __('Type name from who the review message is sent.', 'recipe')
                        ) ,
                        // Review sender email
                        array(
                            'id' => 'review_sender_email',
                            'type' => 'text',
                            'title' => __('Review Sender Email', 'recipe') ,
                            'desc' => __('Type email address from who the review message is sent.', 'recipe')
                        ),

                        /* NEW SUBMITS*/
                        array(
                            'id' => 'review_recive_email',
                            'type' => 'text',
                            'title' => __('Review Receive Email', 'recipe') ,
                            'desc' => __('Type email address where the informations about new submit will arrive.', 'recipe')
                        ),                        
                    )
                );

                /***********************************************************************
                Appearance
                **********************************************************************/
                $this->sections[] = array(
                    'title' => __('Appearance', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Set up the looks.', 'recipe'),
                    'fields' => array(
                        array(
                            'id' => 'top_bar_bg_color',
                            'type' => 'color',
                            'title' => __('Top Bar Background Color', 'recipe'),
                            'desc' => __('Select color of the top bar background.', 'recipe'),
                            'transparent' => false,
                            'std' => '#333'
                        ),
                        array(
                            'id' => 'top_bar_font',
                            'type' => 'color',
                            'title' => __('Top Bar Font Color', 'recipe'),
                            'desc' => __('Select font color for the top bar.', 'recipe'),
                            'transparent' => false,
                            'std' => '#ffffff'
                        ),                        
                        /*--------------------------NAVIGATION-------------------------*/
                        array(
                            'id' => 'navigation_bg_color',
                            'type' => 'color',
                            'title' => __('Navigation Background Color', 'recipe'),
                            'desc' => __('Select background color of the navigation bar.', 'recipe'),
                            'std' => '#ffffff',
                            'transparent' => false,
                        ),
                        array(
                            'id' => 'navigation_font_color',
                            'type' => 'color',
                            'title' => __('Navigation Font Color', 'recipe'),
                            'desc' => __('Select font color of the navigation bar.', 'recipe'),
                            'transparent' => false,
                            'std' => '#676767'
                        ),
                        array(
                            'id' => 'navigation_active_color',
                            'type' => 'color',
                            'title' => __('Navigation Font Color Hover/Active', 'recipe'),
                            'desc' => __('Select font color of the navigation bar on hover / active.', 'recipe'),
                            'transparent' => false,
                            'std' => '#6BA72B'
                        ),          
                        array(
                            'id' => 'subnavigation_bg_color',
                            'type' => 'color',
                            'title' => __('Subnavigation Background Color', 'recipe'),
                            'desc' => __('Select background color of the subnavigation bar.', 'recipe'),
                            'transparent' => false,
                            'std' => '#ffffff'
                        ),
                        array(
                            'id' => 'subnavigation_font_color',
                            'type' => 'color',
                            'title' => __('Subnavigation Font Color', 'recipe'),
                            'desc' => __('Select font color of the subnavigation bar.', 'recipe'),
                            'transparent' => false,
                            'std' => '#676767'
                        ),
                        array(
                            'id' => 'subnavigation_active_color',
                            'type' => 'color',
                            'title' => __('Subnavigation Font Color Hover/Active', 'recipe'),
                            'desc' => __('Select font color of the subnavigation bar on hover / active.', 'recipe'),
                            'transparent' => false,
                            'std' => '#6BA72B'
                        ),      
                        array(
                            'id' => 'subnavigation_border_color',
                            'type' => 'color',
                            'title' => __('Subnavigation Border Color', 'recipe'),
                            'desc' => __('Select border color of the subnavigation bar.', 'recipe'),
                            'transparent' => false,
                            'std' => '#eeeeee'
                        ),
                        array(
                            'id' => 'navigation_font',
                            'type' => 'select',
                            'title' => __('Navigation Font', 'recipe'),
                            'desc' => __('Select navigation font.', 'recipe'),
                            'transparent' => false,
                            'options' => recipe_all_google_fonts(),
                            'std' => 'Lato'
                        ),
                        array(
                            'id' => 'navigation_font_size',
                            'type' => 'text',
                            'title' => __('Navigation Font Size', 'recipe'),
                            'desc' => __('Input navigation font size.', 'recipe'),
                            'std' => '14px'
                        ),
                        /*-------------------------TEXT FONT----------------------------*/
                        //Text font
                        array(
                            'id' => 'text_font',
                            'type' => 'select',
                            'title' => __('Text Font', 'recipe'),
                            'desc' => __('Select font for the regular text.', 'recipe'),
                            'options' => recipe_all_google_fonts(),
                            'std' => 'Lato'
                        ),
                        array(
                            'id' => 'text_font_size',
                            'type' => 'text',
                            'title' => __('Text Font Size', 'recipe'),
                            'desc' => __('Input text font size.', 'recipe'),
                            'std' => '14px'
                        ),
                        array(
                            'id' => 'text_line_height',
                            'type' => 'text',
                            'title' => __('Text Line Height', 'recipe'),
                            'desc' => __('Input text line height.', 'recipe'),
                            'std' => '24px'
                        ),
                        /*-----------------TITLES-----------------------------*/
                        //Text font
                        array(
                            'id' => 'title_font',
                            'type' => 'select',
                            'title' => __('Title Font', 'recipe'),
                            'desc' => __('Select font for the title text.', 'recipe'),
                            'options' => recipe_all_google_fonts(),
                            'std' => 'Ubuntu'
                        ),
                        array(
                            'id' => 'h1_font_size',
                            'type' => 'text',
                            'title' => __('Heading 1 Font Size', 'recipe'),
                            'desc' => __('Input heading 1 font size.', 'recipe'),
                            'std' => '38px'
                        ),
                        array(
                            'id' => 'h1_line_height',
                            'type' => 'text',
                            'title' => __('Heading 1 Line Height', 'recipe'),
                            'desc' => __('Input heading 1 line height.', 'recipe'),
                            'std' => '1.25'
                        ),
                        array(
                            'id' => 'h2_font_size',
                            'type' => 'text',
                            'title' => __('Heading 2 Font Size', 'recipe'),
                            'desc' => __('Input heading 2 font size.', 'recipe'),
                            'std' => '32px'
                        ),
                        array(
                            'id' => 'h2_line_height',
                            'type' => 'text',
                            'title' => __('Heading 2 Line Height', 'recipe'),
                            'desc' => __('Input heading 2 line height.', 'recipe'),
                            'std' => '1.25'
                        ),
                        array(
                            'id' => 'h3_font_size',
                            'type' => 'text',
                            'title' => __('Heading 3 Font Size', 'recipe'),
                            'desc' => __('Input heading 3 font size.', 'recipe'),
                            'std' => '28px'
                        ),
                        array(
                            'id' => 'h3_line_height',
                            'type' => 'text',
                            'title' => __('Heading 3 Line Height', 'recipe'),
                            'desc' => __('Input heading 3 line height.', 'recipe'),
                            'std' => '1.25'
                        ),
                        array(
                            'id' => 'h4_font_size',
                            'type' => 'text',
                            'title' => __('Heading 4 Font Size', 'recipe'),
                            'desc' => __('Input heading 4 font size.', 'recipe'),
                            'std' => '22px'
                        ),
                        array(
                            'id' => 'h4_line_height',
                            'type' => 'text',
                            'title' => __('Heading 4 Line Height', 'recipe'),
                            'desc' => __('Input heading 4 line height.', 'recipe'),
                            'std' => '1.25'
                        ),
                        array(
                            'id' => 'h5_font_size',
                            'type' => 'text',
                            'title' => __('Heading 5 Font Size', 'recipe'),
                            'desc' => __('Input heading 5 font size.', 'recipe'),
                            'std' => '18px'
                        ),
                        array(
                            'id' => 'h5_line_height',
                            'type' => 'text',
                            'title' => __('Heading 5 Line Height', 'recipe'),
                            'desc' => __('Input heading 5 line height.', 'recipe'),
                            'std' => '1.25'
                        ),
                        array(
                            'id' => 'h6_font_size',
                            'type' => 'text',
                            'title' => __('Heading 6 Font Size', 'recipe'),
                            'desc' => __('Input heading 6 font size.', 'recipe'),
                            'std' => '13px'
                        ),
                        array(
                            'id' => 'h6_line_height',
                            'type' => 'text',
                            'title' => __('Heading 6 Line Height', 'recipe'),
                            'desc' => __('Input heading 6 line height.', 'recipe'),
                            'std' => '1.25'
                        ),
                        /* -------------------MAIN BODY------------------------- */
                        //Body Background Image
                        array(
                            'id' => 'body_bg_image',
                            'type' => 'media',
                            'title' => __('Body Background Image', 'recipe'),
                            'desc' => __('Select image for the body.', 'recipe'),
                        ),
                        //Body Background Color
                        array(
                            'id' => 'body_bg_color',
                            'type' => 'color',
                            'title' => __('Body Background Color', 'recipe'),
                            'desc' => __('Select color for the body.', 'recipe'),
                            'transparent' => false,
                            'std' => '#f5f5f5'
                        ),
                        /* -------------------MAIN COLOR------------------------- */
                        //Main Color
                        array(
                            'id' => 'main_color',
                            'type' => 'color',
                            'title' => __('Main Color', 'recipe'),
                            'desc' => __('Select main color for the site.', 'recipe'),
                            'transparent' => false,
                            'std' => '#6BA72B'
                        ),
                        //Main Color Button Font
                        array(
                            'id' => 'maincolor_btn_font_clr',
                            'type' => 'color',
                            'title' => __('Main Color Button Font', 'recipe'),
                            'desc' => __('Select button font color for the buttons with the main color.', 'recipe'),
                            'transparent' => false,
                            'std' => '#FFFFFF'
                        ),
                        //Main Color
                        array(
                            'id' => 'secondary_color',
                            'type' => 'color',
                            'title' => __('Secondary Color', 'recipe'),
                            'desc' => __('Select secondary color for the site.', 'recipe'),
                            'transparent' => false,
                            'std' => '#333'
                        ),
                        //Main Color Button Font
                        array(
                            'id' => 'secondarycolor_btn_font_clr',
                            'type' => 'color',
                            'title' => __('Secondary Color Button Font', 'recipe'),
                            'desc' => __('Select button font color for the buttons with the secondary color.', 'recipe'),
                            'transparent' => false,
                            'std' => '#FFFFFF'
                        ),
                        /* -------------------COPYRIGHTS------------------------- */
                        array(
                            'id' => 'copyrigths_bg_color',
                            'type' => 'color',
                            'title' => __('Copyrights Background Color', 'recipe'),
                            'desc' => __('Select background color for the copyrights section.', 'recipe'),
                            'transparent' => false,
                            'std' => '#333'
                        ),
                        array(
                            'id' => 'copyrigths_font_color',
                            'type' => 'color',
                            'title' => __('Copyrights Font Color', 'recipe'),
                            'desc' => __('Select font color for the copyrights section.', 'recipe'),
                            'transparent' => false,
                            'std' => '#ffffff'
                        ),                        
                    )
                );  

                /**********************************************************************
                ***********************************************************************
                CONTACT PAGE SETTINGS
                **********************************************************************/
                
                $this->sections[] = array(
                    'title' => __('Contact Page', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Contact page settings.', 'recipe'),
                    'fields' => array(
                        array(
                            'id' => 'contact_form_email',
                            'type' => 'text',
                            'title' => __('Contact Email', 'recipe') ,
                            'desc' => __('Input email where the messages should arrive.', 'recipe'),
                        ),
                        array(
                            'id' => 'contact_sender_mail',
                            'type' => 'text',
                            'title' => __('Contact Sender Email', 'recipe') ,
                            'desc' => __('Input email of the sender and make sure that email domain has the same domain as site.', 'recipe'),
                        ),
                        array(
                            'id' => 'contact_sender_name',
                            'type' => 'text',
                            'title' => __('Contact Sender Name', 'recipe') ,
                            'desc' => __('Input name of the sender.', 'recipe'),
                        ),
                    )
                );

                /**********************************************************************
                ***********************************************************************
                FOOTER COPYRIGHTS
                **********************************************************************/
                
                $this->sections[] = array(
                    'title' => __('Copyrights', 'recipe') ,
                    'icon' => '',
                    'desc' => __('Copyrights settings.', 'recipe'),
                    'fields' => array(
                        array(
                            'id' => 'copyrights',
                            'type' => 'text',
                            'title' => __('Copyrights', 'recipe') ,
                            'desc' => __('Input copyrights text.', 'recipe'),
                        ),
                        //Facebook Top Bar Link
                        array(
                            'id' => 'copyrights-facebook',
                            'type' => 'text',
                            'title' => __('Facebook Link', 'recipe') ,
                            'desc' => __('Input link to your facebook page', 'recipe')
                        ),
                        //Twitter Top Bar Link
                        array(
                            'id' => 'copyrights-twitter',
                            'type' => 'text',
                            'title' => __('Twitter Link', 'recipe') ,
                            'desc' => __('Input link to your twitter page', 'recipe')
                        ),
                        //Google Top Bar Link
                        array(
                            'id' => 'copyrights-google',
                            'type' => 'text',
                            'title' => __('Google Link', 'recipe') ,
                            'desc' => __('Input link to your google page', 'recipe')
                        ),
                        //Linkedin Top Bar Link
                        array(
                            'id' => 'copyrights-linkedin',
                            'type' => 'text',
                            'title' => __('Linkedin Link', 'recipe') ,
                            'desc' => __('Input link to your linkedin page', 'recipe')
                        ),
                        //Tumblr Top Bar Link
                        array(
                            'id' => 'copyrights-tumblr',
                            'type' => 'text',
                            'title' => __('Tumblr Link', 'recipe') ,
                            'desc' => __('Input link to your tumblr page', 'recipe')
                        ),
                        //Pinterest icon
                        array(
                            'id' => 'copyrights-pinterest',
                            'type' => 'text',
                            'title' => __('Pinterest Link', 'recipe') ,
                            'desc' => __('Input link to your pinterest page', 'recipe')
                        ),
                        //Interest icon
                        array(
                            'id' => 'copyrights-instagram',
                            'type' => 'text',
                            'title' => __('Instagram Link', 'recipe') ,
                            'desc' => __('Input link to your instagram page', 'recipe')
                        ),
                    )
                );                

            }

            /**
             * All the possible arguments for Redux.
             * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
             * */
            public function setArguments() {

                $theme = wp_get_theme(); // For use with some settings. Not necessary.

                $this->args = array(
                    // TYPICAL -> Change these values as you need/desire
                    'opt_name'             => 'recipe_options',
                    // This is where your data is stored in the database and also becomes your global variable name.
                    'display_name'         => $theme->get( 'Name' ),
                    // Name that appears at the top of your panel
                    'display_version'      => $theme->get( 'Version' ),
                    // Version that appears at the top of your panel
                    'menu_type'            => 'menu',
                    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                    'allow_sub_menu'       => true,
                    // Show the sections below the admin menu item or not
                    'menu_title'           => __( 'Recipe WP', 'redux-framework-demo' ),
                    'page_title'           => __( 'Recipe WP', 'redux-framework-demo' ),
                    // You will need to generate a Google API key to use this feature.
                    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                    'google_api_key'       => '',
                    // Set it you want google fonts to update weekly. A google_api_key value is required.
                    'google_update_weekly' => false,
                    // Must be defined to add google fonts to the typography module
                    'async_typography'     => true,
                    // Use a asynchronous font on the front end or font string
                    //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                    'admin_bar'            => true,
                    // Show the panel pages on the admin bar
                    'admin_bar_icon'     => 'dashicons-portfolio',
                    // Choose an icon for the admin bar menu
                    'admin_bar_priority' => 50,
                    // Choose an priority for the admin bar menu
                    'global_variable'      => '',
                    // Set a different name for your global variable other than the opt_name
                    'dev_mode'             => false,
                    // Show the time the page took to load, etc
                    'update_notice'        => true,
                    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                    'customizer'           => true,
                    // Enable basic customizer support
                    //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                    //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                    // OPTIONAL -> Give you extra features
                    'page_priority'        => null,
                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                    'page_parent'          => 'themes.php',
                    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                    'page_permissions'     => 'manage_options',
                    // Permissions needed to access the options panel.
                    // Specify a custom URL to an icon
                    'last_tab'             => '',
                    // Force your panel to always open to a specific tab (by id)
                    'page_icon'            => 'icon-themes',
                    // Icon displayed in the admin panel next to your menu_title
                    'page_slug'            => '_options',
                    // Page slug used to denote the panel
                    'save_defaults'        => true,
                    // On load save the defaults to DB before user clicks save or not
                    'default_show'         => false,
                    // If true, shows the default value next to each field that is not the default value.
                    'default_mark'         => '',
                    // What to print by the field's title if the value shown is default. Suggested: *
                    'show_import_export'   => true,
                    // Shows the Import/Export panel when not used as a field.

                    // CAREFUL -> These options are for advanced use only
                    'transient_time'       => 60 * MINUTE_IN_SECONDS,
                    'output'               => true,
                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                    'output_tag'           => true,
                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                    // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                    'database'             => '',
                    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                    'system_info'          => false,
                    // REMOVE

                    // HINTS
                    'hints'                => array(
                        'icon'          => 'icon-question-sign',
                        'icon_position' => 'right',
                        'icon_color'    => 'lightgray',
                        'icon_size'     => 'normal',
                        'tip_style'     => array(
                            'color'   => 'light',
                            'shadow'  => true,
                            'rounded' => false,
                            'style'   => '',
                        ),
                        'tip_position'  => array(
                            'my' => 'top left',
                            'at' => 'bottom right',
                        ),
                        'tip_effect'    => array(
                            'show' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'mouseover',
                            ),
                            'hide' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'click mouseleave',
                            ),
                        ),
                    )
                );


            }

        }

        global $recipe_opts;
        $recipe_opts = new Recipe_Options();
        } else {
        echo "The class named Recipe_Options has already been called. <strong>Developers, you need to prefix this class with your company name or you'll run into problems!</strong>";
    }