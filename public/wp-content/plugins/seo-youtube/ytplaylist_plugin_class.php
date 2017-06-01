<?php
/*
        License: Copyright © 2011 cfconsultancy
        This is not free software !
*/

class YoutubePlaylistPlugin
{
        const UUID = "youtubeplaylist";
        const YTPLIST = 'ytplaylist';

        /**
         * A list of all the settings for this plugin.
         * The array is divided in two sub arrays, php and js.
         * The user is able to override the settings in the "php" subarray through wordpress shortcode attributes.
         * The user cannot override the "js" settings, site wide defaults can be configured within the admin environment.
         *
         *
         */
        private static $plugin_settings = array(
                "js" => array(
                        "html5" => array(
                                "name" => "HTML5",
                                "desc" => "<br />Use the HTML5 video player",
                                "values" => array(true,false),
                                "default" => true
                        ),
                        "autoHide" => array(
                                "name" => "Auto Hide",
                                "desc" => "<br />Hide, Unhide the player bar (true or false)",
                                "values" => array(true,false),
                                "default" => false
                        ),
                        "playerWidth" => array(
                                "name" => "Player Width",
                                "desc" => "<br />In pixels. The width of the embedded youtube video. <br />The player height is determined based on this width. Default is 640",
                                "default" =>"640",
                        ),
                        "centerPlayer" => array(
                                "name" => "Center Player",
                                "desc" => "<br />Whether or not to center the player in the parent container",
                                "values" => array(true,false),
                                "default" => true
                        ),
                        "showRelated" => array(
                                "name" => "Show Related",
                                "desc" => "<br />Set to false to stop related videos being shown at the end of the embedded video.",
                                "values" => array(true,false),
                                "default" =>false
                        ),
                        "showInfo" => array(
                                "name" => "Show Info",
                                "desc" => "<br />Set to false to stop the title being shown in the video",
                                "values" => array(true,false),
                                "default" =>true
                        ),
                        "playerversion" => array(
                                "name" => "Player Version",
                                "desc" => "<br />Set to version = 3 for the new flash youtube player (Works only if html5 is false)",
                                "values" => array("2","3"),
                                "default" =>"2",
                        ),
                        "playerColor" => array(
                                "name" => "Player Color",
                                "desc" => "<br />The color of the player (works only with version 2 and 3 and html5 is false and without an #)",
                                "default" =>"f2f2f2",
                        ),
                        "hd" => array(
                                "name" => "HD",
                                "desc" => "<br />Set to true to play the HD version if available",
                                "values" => array(true,false),
                                "default" =>false
                        ),
                        "probably_logged_in" => array(
                                "name" => "Probably Logged In",
                                "desc" => "<br />Set to true to show the watch later button on the html5 version. <br />Works only if the viewer is logged in at youtube and html5 true. <br />(Not supported by youtube)",
                                "values" => array(true,false),
                                "default" =>true
                        ),
                        "autoPlay" => array(
                                "name" => "Auto Play",
                                "desc" => "<br />Set to true to play automatic with onclick",
                                "values" => array(true,false),
                                "default" =>true
                        ),
                        "playOnLoad" => array(
                                "name" => "Play on Load",
                                "desc" => "<br />Play first video automatic with page onload",
                                "values" => array(true,false),
                                "default" =>false
                        ),
                        "playfirst" => array(
                                "name" => "Play first",
                                "desc" => "<br />Select the video to start with. 0 is the first, 1 the second ect",
                                "default" =>"0",
                        ),
                        "allowFullScreen" => array(
                                "name" => "Allow Full Screen",
                                "desc" => "<br />Add the full screen button with player2 version <br />(doesn't work with the html5 version)",
                                "values" => array(true,false),
                                "default" =>true
                        )
                ),
                "php" => array(
                        "feed" => array(
                                "name" => "Choose feed type",
                                "desc" => "<br />Choose which feed you want. Use the shortcodes to set this on each page(See on top of the page)",
                                "values" => array("keywords","username","favorites","playlist"),
                                "default" => "keywords"
                        ),
                        "sort" => array(
                                "name" => "Sort",
                                "desc" => "<br />Can be relevance (default), published, viewCount, rating.",
                                "values" => array("relevance","published","viewCount","rating"),
                                "default" => "relevance"
                        ),
                        "lang" => array(
                                "name" => "Language Filter",
                                "desc" => "<br />Set language filter for example en (english)<br />All codes can be <a href=\"http://www.loc.gov/standards/iso639-2/php/code_list.php\" target=\"_blank\">found here</a>",
                                "default" => ""
                        ),
                        "time" => array(
                                "name" => "Time Filter",
                                "desc" => "<br />Set time parameter to restrict the search to videos uploaded within the specified time. <br />Valid values for this parameter are today (1 day), this_week (7 days), this_month (1 month) and all_time",
                                "default" => "all_time"
                        ),
                        "restriction" => array(
                                "name" => "IP restriction",
                                "desc" => "<br />The restriction parameter identifies the IP address that should be used to filter videos that can only be played in specific countries.
                                                        <br />It is recommend that you always use this parameter to specify the end users IP address. (By default, the API filters out videos that cannot be played in the country from which you send API requests. This restriction is based on your client applications IP address.). Or dont use to get all videos.
                                                        <br />NB. Don't use this if you use the cache function !",
                                "values" => array(true,false),
                                "default" => true
                        ),
                        "safesearch" => array(
                                "name" => "Safe Search",
                                "desc" => "<br />Parameter none , moderate or strict",
                                "values" => array("none", "moderate", "strict" ),
                                "default" => "strict"
                        ),
                        "caption" => array(
                                "name" => "Caption",
                                "desc" => "<br />The caption parameter enables you to restrict a search to videos that have or do not have caption tracks. true or false or dont use.",
                                "values" => array(true,false),
                                "default" => false
                        ),
                        "cache" => array(
                                "name" => "Cache",
                                "desc" => "<br />Cache the xml feed(true or false). Make sure the dir cache has rights to write to.",
                                "values" => array(true,false),
                                "default" => false
                        ),
                        "cachelife"        => array(
                                "name" => "Cache Life",
                                "desc" => "<br />Empty cached xml for example after one hour 3600, one day 86400 or one week 604800 (in seconds)",
                                "default" => "86400"
                        ),
                        "desclength" => array(
                                "name" => "Description Length",
                                "desc" => "<br />Description Length. If you make the width of the player smaller you can set here the max. characters (Default 200)",
                                "default" => "200"
                        ),
                        "titlelength" => array(
                                "name" => "Title Length",
                                "desc" => "<br />Title Length. If you make the width of the player smaller you can set here the max. characters (Default 70)",
                                "default" => "70"
                        )
                )
        );

        /**
         *
         * This hook runs only once when the plugin gets activated.
         *
         * It extracts all the default values from the static $plugin_settings array
         * and writes them to the database.
         *
         */
        function hook_plugin_activate()
        {
                $default_plugin_settings = array();

                foreach( self::$plugin_settings['js'] as $key => $value )
                        $default_plugin_settings['js'][$key] = $value['default'];

                foreach( self::$plugin_settings['php'] as $key => $value )
                        $default_plugin_settings['php'][$key] = $value['default'];

                update_option( self::YTPLIST, $default_plugin_settings );
        }

        /**
         *
         * Delete all the plugin options from the database on deactivation
         *
         */
        function hook_plugin_deactivate()
        {
                delete_option(self::YTPLIST);
        }

        /**
         *
         * - Make sure jquery is included in wordpress.
         * - Add youtubeplaylist css file to the document (depends on presence of wp_head() in theme file)
         * - Add youtubeplaylist js file to the document (depends on presence of wp_head() and jquery in theme file)
         *
         */
        function hook_plugin_init()
        {
        if (!is_admin()) {
                wp_enqueue_style( 'ytplaylist_style', plugins_url('/css/youtubeplaylist.css', __FILE__), null, null, 'all' );
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script('ytplaylist_script', plugins_url('/js/jquery.youtubeplaylist-min.js', __FILE__), null, null , true );
        }
        }

        /**
         *
         * Render a menu item in the Wordpress admin environment called 'Youtube Playlist' under 'Settings'
         *
         */
        function hook_admin_menu()
        {
                add_options_page(
                        'Youtube Playlist Options',
                        'Youtube Playlist',
                        'manage_options',
                        self::UUID,
                        self::add_handler('render_options_page')
                );

                add_action( 'admin_init', self::add_handler('admin_init_callback') );
        }

        /**
         *
         * This function is called whenever a shortcode of the type [youtube-list] is found in Wordpress content.
         *
         * It converts the shortcode to a combination of html and javascript to output the Youtube Video player
         *
         */
        function hook_shortcode( $atts )
        {
                $defaults = get_option( self::YTPLIST );
                $options = shortcode_atts( $defaults["php"], $atts );

                require_once('class/class.youtubelist.php');

                $video = new youtubelist( $options["feed"] );

                switch($options["feed"])
                {
                        case "playlist":
                                $video->set_playlist($atts["playlist"]);
                        break;
                        case "favorites":
                                $video->set_favorites($atts["username"]);
                        break;
                        case "username":
                                $video->set_username($atts["username"]);
                        break;
                        case "keywords":
                                if( !empty($atts["keywords"]) )
                                        $video->set_keywords($atts["keywords"]);
                        break;
                }

                $video->set_max(50);

                if( !empty($atts["sort"]) )
                         $video->set_order($options["sort"]);

                $video->set_cachexml((bool)$options["cache"]);
                $video->set_cachelife($options["cachelife"]);
                $video->set_xmlpath(__DIR__."\/cache\/");
                $video->set_safeSearch($options["safesearch"]);

                if( !empty($options["time"]) )
                        $video->set_time( $options["time"] );

                if( !empty($options["lang"]) )
                        $video->set_lang( $options["lang"] );

        if( $options["restriction"] == 1 )
                $video->set_restriction($_SERVER['REMOTE_ADDR']);

                $video->set_start(1);
                $video->set_mobile(0);
                $video->set_descriptionlength( $options["desclength"] );
                $video->set_titlelength( $options["titlelength"] );

                ob_start();
                $defaults["js"]["holderId"] = "ytvideo";

            //Playbar height settings
                if ($defaults["js"]["playerversion"] == 2 && $defaults["js"]["html5"] == '' && $defaults["js"]["autoHide"] == '') {
                   $barheight = 25;
                }elseif ($defaults["js"]["autoHide"] == 1 && $defaults["js"]["html5"] == '') {
                   $barheight = 0;
                }elseif ($defaults["js"]["playerversion"] == 2 && $defaults["js"]["autoHide"] == 1 && $defaults["js"]["html5"] == '') {
                   $barheight = 0;
                }elseif ($defaults["js"]["autoHide"] == 1 && $defaults["js"]["html5"] == 1) {
                   $barheight = 4;
                }elseif ($defaults["js"]["html5"] == 1 && $defaults["js"]["autoHide"] == 1) {
                   $barheight = 0;
                }elseif ($defaults["js"]["playerversion"] == 2 && $defaults["js"]["autoHide"] == '' && $defaults["js"]["html5"] == '') {
                   $barheight = 34;
                }else{
                   $barheight = 34;
                }

                $defaults["js"]["playerHeight"] = (int) ceil( intval($defaults["js"]["playerWidth"])/16*9 ) + $barheight;
                $defaults["js"]["playerversion"] = "&amp;version=".$defaults["js"]["playerversion"];
                ?>
                        <script type="text/javascript">
                          var _y_timer = null;
                          
                          function _y_load_it() {
                            jQuery(document).ready(function($) {
                              $(".videoThumb").ytplaylist(<?php echo json_encode($defaults["js"]); ?>);
                            });
                          }
                          
                          function _y_test_it() {
                            if(typeof jQuery == 'undefined' || !jQuery.fn.ytplaylist) {
                              setTimeout(function(){
                                _y_test_it();
                              },200);
                            } else {
                              _y_load_it()
                            }
                          }
                          
                          _y_test_it();
                        </script>
                        <div id="yt_holder" style="<?php echo($defaults["js"]["centerPlayer"])?"margin-left:auto;margin-right:auto;":""; echo (!empty($defaults["js"]["playerWidth"]))?"width:{$defaults["js"]["playerWidth"]}px;":"";?>">
                                <div id="<?php echo $defaults["js"]["holderId"]; ?>" <?php if(!empty($defaults["js"]["playerHeight"])){echo "style=\"height:{$defaults["js"]["playerHeight"]}px;\"";}?>></div>
                                <div class="youplayer">
                                        <ul class="videoyou" <?php if(!empty($defaults["js"]["playerWidth"])){echo "style=\"width:{$defaults["js"]["playerWidth"]}px;\"";}?>>
                                                <?php
                                                if( $video->get_videos() !=null )
                                                {
                                                        foreach( $video->get_videos() as $key => $val )
                                                        {
                                                                echo sprintf('<li><p class="youtitle">%s</p><span class="time">%s</span><a class="videoThumb" href="http://www.youtube.com/watch?v=%s">%s</a></li>', $val['title'], $val['time'], $val['videoid'], $val['description']);
                                                        }
                                                }
                                                else
                                                {
                                                        echo '<li>Sorry, no video\'s found</li>';
                                                }
                                                ?>
                                        </ul>
                                </div>
                        </div>
                        <div style="clear:both;">&nbsp;</div>
                <?php
                $result = ob_get_clean();
                return $result;
        }

        /**
         *
         * Register a unique key for storing plugin settings in a row of the wp_options table.
         *
         * Adds two sections to the Youtube Playlist options page in the admin environment.
         * - one for js settings
         * - one for php settings
         *
         */
        function admin_init_callback()
        {
                register_setting( self::YTPLIST, self::YTPLIST, self::add_handler('validate_settings_fields') );
                add_settings_section('js_section', 'Overall Javascript Settings', self::add_handler('render_options_section_js'), self::UUID);
                add_settings_section('php_section', 'Overall PHP Settings', self::add_handler('render_options_section_php'), self::UUID);
        }

        /**
         *
         * Render the plugin options page including important form fields for security
         *
         */
        function render_options_page()
        {
                if( !current_user_can('manage_options') )
                        wp_die( __('You do not have sufficient permissions to access this page.') );
                ?>
          <div class="wrap">
            <h2>Youtube SEO Playlist</h2>
            <div id="tabs" style="width:85%; padding:10px">
            <ul>
                <li><a href="#tabs-1">Settings</a></li>
                <li><a href="#tabs-2">Shortcodes &amp; readme</a></li>
            </ul>
                <div id="tabs-2" class="wrap yadmin">
                    <div class="icon32" id="icon-options-general"></div>
                    <a href="http://codecanyon.net/item/youtube-seo-playlist-for-wordpress/237365?ref=ceasar" target="_blank"><img align="right" src="<?php echo WP_PLUGIN_URL . '/youtubeplaylist/thumb.jpg'; ?>" width="80" height="80" alt="youtubeplaylist" /></a>
                    <h2>Youtube Playlist SEO Options</h2><br /><br />
                    <div class="postbox">
                    <b>Default feed type selected in admin:</b> <br />If you have selected for example <b>feed type keywords</b> in the admin as an default you only have to add the following example shortcode.
                            <br />To overrule this default see the other shortcode options below.
                        <p>[youtube-list keywords="HD nature"]</p>
                    </div>
                    <div class="postbox">
                    <b>feed type keywords:</b> <br />Load feed type keywords and keywords search for example 'HD Nature'
                        <p>[youtube-list feed="keywords" keywords="HD nature"]</p>
                    </div>
                    <div class="postbox">
                    <b>feed type username:</b> <br />Load feed type username (channel name)
                        <p>[youtube-list feed="username" username="cfconsultancy"]</p>
                    </div>
                    <div class="postbox">
                    <b>feed type favorites:</b> <br />Load feed type favorites from a username
                        <p>[youtube-list feed="favorites" username="giantmonster"]</p>
                    </div>
                    <div class="postbox">
                    <b>feed type playlist:</b> <br />Load feed type playlist. <a href="http://www.google.nl/search?hl=nl&q=find+playlist+code+youtube&btnG=Google+zoeken&meta=&rlz=" target="_blank">Look here</a> if you want some info how to find your playlist key.
                        <p>[youtube-list feed="playlist" playlist="9E912E4AA41E8618"]</p>
                    </div>
                    <div class="postbox">
                    <b>Standard code:</b> <br />Load feed type keywords and default search 'HD Nature'
                        <p>[youtube-list]</p>
                    </div>
                    <div class="postbox">
                    <b>Other shortcodes:</b>
                        <br />For example sort="viewCount"
                        <p>Example [youtube-list feed="keywords" keywords="HD nature" sort="viewCount"]</p>
                    </div>
                    <b>NB.</b>
                    <ul>
                    <li>You can only <b>use one player</b> on each page or post !</li>
                    <li>Retrieve max 50 video's. (restriction youtube)</li>
                    <li>If you want to use the <b>cache option</b> make sure the dir youtubeplaylist - cache has rights to write to (CHMOD)</li>
                    </ul>
                </div><!--#tabs-2-->

                <div id="tabs-1" class="wrap yadmin"><!--#tabs-1-->
                    <div class="icon32" id="icon-options-general"></div>
                        <a href="http://codecanyon.net/item/youtube-seo-playlist-for-wordpress/237365?ref=ceasar" target="_blank"><img align="right" src="<?php echo WP_PLUGIN_URL . '/youtubeplaylist/thumb.jpg'; ?>" width="80" height="80" alt="youtubeplaylist" /></a>
                        <h2>Youtube Playlist SEO Options</h2>
                        <form method="post" action="options.php">
                        <?php settings_fields(self::YTPLIST); //outputs all hidden fields for security ?>
                            <?php do_settings_sections(self::UUID); // renders all the html elements for settings ?>
                        <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
                        </form>
                </div><!--#tabs-1-->
            </div>
          </div>
                <?php
        }

        /**
         *
         * Render all the php options by getting all the plugin options from the wp_options table
         * and looping through the "php" subarray
         *
         */
        function render_options_section_php()
        {
                $options = get_option( self::YTPLIST );
                ?>
                <table class="form-table">
                <?php
                        foreach( $options['php'] as $key => $value )
                        {
                                self::render_option_field($key,$value,'php',self::$plugin_settings['php'][$key]);
                        }
                ?>
                </table>
                <?php
        }

        /**
         *
         * Render all the js options by getting all the plugin options from the wp_options table
         * and looping through the "js" subarray
         *
         */
        function render_options_section_js()
        {
                $options = get_option( self::YTPLIST );
                ?>
                <table class="form-table">
                <?php
                        foreach( $options['js'] as $key => $value )
                        {
                                self::render_option_field($key,$value,'js',self::$plugin_settings['js'][$key]);
                        }
                ?>
                </table>
                <?php
        }



        /**
         *
         * Renders a valid html form field for changing plugin options.
         *
         * It checks for the presence of a ["values"] value in the static self::$plugin_settings array
         * - if it is present and it is an array then render a html <select> form field
         * - otherwise render a <input type="text" /> form field.
         *
         */
        function render_option_field($key,$value,$realm,$settings)
        {
                ?>
                <tr valign="top">
                        <th scope="row"><?php echo $settings["name"]; ?></th>
                        <td><?php
                                if( !empty($settings["values"]) && is_array($settings["values"]) ): ?>
                                <select id="<?php echo $key; ?>" name="<?php echo sprintf('%s[%s][%s]',self::YTPLIST,$realm,$key); ?>">
                                        <?php foreach( $settings["values"] as $val ): ?>
                                                <option value="<?php echo $val; ?>"<?php echo ($val==$value) ? " SELECTED" : "";?>><?php echo self::boolToString($val); ?></option>
                                        <?php endforeach; ?>
                                </select>
                                <?php
                                else:
                                        echo sprintf('<input id="%s" name="%s[%s][%s]" type="text" value="%s" />',$key,self::YTPLIST,$realm,$key,$value);
                                endif; ?>
                                <span class="description"><?php echo $settings["desc"]; ?></span>
                        </td>
                </tr>
                <?php
        }

        /**
         *
         * After the plugin options form is submitted validate all form data and convert
         * strings to booleans if needed.
         *
         */
        function validate_settings_fields($input)
        {
                foreach( $input['js'] as $key => $value )
                {
                        if( is_bool(self::$plugin_settings["js"][$key]["default"]) === true )
                        {
                                $input["js"][$key] = (bool)$input["js"][$key];
                        }
                }

                foreach( $input['php'] as $key => $value )
                {
                        if( is_bool(self::$plugin_settings["php"][$key]["default"]) === true )
                        {
                                $input["php"][$key] = (bool)$input["php"][$key];
                        }
                }

                return $input;
        }



        function boolToString($bool)
        {
                if( $bool === true )
                        return "true";
                elseif( $bool === false )
                        return "false";
                else
                        return $bool;
        }



        function add_handler($name)
        {
                return array(__CLASS__,$name);
        }
}
