<?php
/*
  Plugin Name: Character Countdown
  Plugin URI: http://wordpress.org/plugins/character-countdown
  Description: Show character countdown in the Editor for Pages, Posts and Excerpts
  Version: 1.0
  Author: EnigmaWeb
  Author URI: https://profiles.wordpress.org/enigmaweb
 */
// Default Values
$ccp_default = apply_filters('ccp_default_settings', array('cc_posts' => '', 'cc_post_limit' => '', 'cc_pages' => '', 'cc_page_limit' => '', 'cc_exerpt' => '', 'cc_exerpt_limit' => ''));


// Pulling the default settings from DB
$ccp_setting = wp_parse_args(get_option('ccp_setting'), $ccp_default);


// This function registering the settings in DB
add_action('admin_init', 'ccp_register_setting');

function ccp_register_setting() {
    register_setting('ccp_setting', 'ccp_setting');
}

// Adding settings page in wp menu
add_action('admin_menu', 'ccp_setting_menu');

function ccp_setting_menu() {
    add_menu_page('Character Countdown', 'Character Countdown', 'manage_options', 'character-countdown', 'cc_setting_page', plugins_url('/character-countdown/cc-icon.png'));
}

// If setting saved
function ccp_setting_update() {
    global $ccp_setting;
    if (isset($ccp_setting['ccp_save'])) {
        echo '<div id="message" class="updated"><p>Settings has been saved!</p></div>';
    }
    unset($ccp_setting['ccp_save']);
    update_option('ccp_setting', $ccp_setting);
}

// Display admin page
function cc_setting_page() {
    ?>
    <div class="wrap">
        <form method="post" action="options.php">
            <?php settings_fields('ccp_setting'); ?>
            <?php global $ccp_setting; ?>
            <h1>Character Countdown</h1>
            <h4>Add a character countdown reference to Pages, Posts or Excerpt fields.</h4>
            <p> This plugin does not actually enforce a character limit on the front end, it just displays the number of characters in the editor. This can be a helpful reference.</p>
            <?php ccp_setting_update(); ?>
            <div class="cc_setting_row">
                <input type="checkbox"  name="ccp_setting[cc_posts]" id="ccp_posts"  value="1" <?php checked($ccp_setting['cc_posts'], 1); ?> />
                <label for="ccp_posts">Posts</label>
                 Character Countdown:
                <input type="text"  name="ccp_setting[cc_post_limit]"  value="<?php echo $ccp_setting['cc_post_limit']; ?>"/>
            </div>
            <div class="cc_setting_row">
                <input type="checkbox"  name="ccp_setting[cc_pages]" id="cc_pages" value="1" <?php checked($ccp_setting['cc_pages'], 1); ?>/>
                <label for="cc_pages">Pages</label>
                Character Countdown:
                <input type="text"  name="ccp_setting[cc_page_limit]" value="<?php echo $ccp_setting['cc_page_limit']; ?>"/>
            </div>
            <div class="cc_setting_row">
                <input type="checkbox"  name="ccp_setting[cc_excerpt]" id="cc_excerpt" value="1" <?php checked($ccp_setting['cc_excerpt'], 1); ?>/>
                <label for="cc_excerpt">Excerpt</label>
                Character Countdown:
                <input type="text"  name="ccp_setting[cc_excerpt_limit]" value="<?php echo $ccp_setting['cc_excerpt_limit']; ?>"/>
            </div>
            <input type="submit"  name="ccp_setting[ccp_save]" value="Save Settings" class="button-primary" />
        </form>
    </div>

    <?php
}

// Adding some makeup
add_action('admin_head', 'ccp_admin_head');

function ccp_admin_head() {
    global $ccp_setting;
    ?>
    <style type="text/css">
		.wrap h4 { font-size: 14px; font-weight:bold;}
        .cc_setting_row label {width:100px; display:block; float:left;padding: 6px 0 0;}
        .cc_setting_row input[type=checkbox] {float: left;margin: 9px 4px 0 0 !important;}
        .cc_setting_row input[type=text]{ margin-bottom: 10px; width:85px;}
        .ccp_excerpt_counter { float:right;}
        .ccp_post_counter { padding-left: 15px;}
        .ccp_page_counter { padding-left: 15px;}
    </style>
    <script type="text/javascript">

        jQuery(document).ready(function () {
    <?php
// Character limit for post
    if ($ccp_setting['cc_posts'] == 1 && get_post_type() == 'post') {
        ?>

                // Variables
                var post_limit = <?php echo $ccp_setting['cc_post_limit'] ?>;
                var post_count = jQuery('.post-type-post #content').val().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig, '').length;
                // Default
                jQuery('.post-type-post #wp-word-count').append('<span class="ccp_post_counter">Character Count: <span  class="ccp_count_post"></span> / <span class="ccp_limit_post"></span> </span>');
                jQuery('.ccp_count_post').text(post_count);
                jQuery('.ccp_limit_post').text(post_limit);
                // Let's Rock :p
                jQuery('.post-type-post #content').keyup(function () {

                    var post_count = jQuery(this).val().length;
                    if (post_count > post_limit) {
                        //jQuery(this).val(jQuery(this).val().substr(0, post_limit));
                        //post_count = post_limit;
                    }

                    jQuery('#tinymce').hide();
                    jQuery('.ccp_count_post').text(post_count);
                });
        <?php
    }
// Character limit for page
    if ($ccp_setting['cc_pages'] == 1 && get_post_type() == 'page') {
        ?>

                // Variables
                var limit = <?php echo $ccp_setting['cc_page_limit'] ?>;
                var count = jQuery('.post-type-page #content').val().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig, '').length;
                // Default
                jQuery('.post-type-page #wp-word-count').append('<span class="ccp_page_counter">Character Count: <span  class="ccp_count_page"></span> / <span class="ccp_limit_page"></span></span>');
                jQuery('.ccp_count_page').text(count);
                jQuery('.ccp_limit_page').text(limit);
                // Let's Rock :p
                jQuery('.post-type-page #content').keyup(function () {
                    var count = jQuery(this).val().length;
                    if (count > limit) {
                        //jQuery(this).val(jQuery(this).val().substr(0, limit));
                        //count = limit;
                    }
                    jQuery('.ccp_count_page').text(count);
                });
        <?php
    }
// Character limit for excerpt
    if ($ccp_setting['cc_excerpt'] == 1 && get_post_type() == 'post') {
        ?>

                // Variables
                var ex_limit = <?php echo $ccp_setting['cc_excerpt_limit'] ?>;
                var ex_count = jQuery('#excerpt').val().length;

                // Default
                jQuery('#postexcerpt .hndle').append('<span class="ccp_excerpt_counter">Character Count: <span  class="ccp_count"></span> / <span class="ccp_limit"></span> </span>');
                jQuery('.ccp_count').text(ex_count);
                jQuery('.ccp_limit').text(ex_limit);

                // Let's Rock :p
                jQuery('#excerpt').keyup(function () {
                    var ex_count = jQuery(this).val().length;
                    if (ex_count > ex_limit) {
                        //jQuery(this).val(jQuery(this).val().substr(0, ex_limit));
                        //ex_count = ex_limit;
                    }
                    jQuery('.ccp_count').text(ex_count);
                });

    <?php } ?>
        });
    </script>
    <?php
}

// For Visual Editor
add_action('admin_print_footer_scripts', 'check_veditor_length');

function check_veditor_length() {
    global $ccp_setting;

    // Character limit for post / Visual Editor
    if ($ccp_setting['cc_posts'] == 1 && get_post_type() == 'post') {
        ?>
        <script type="text/javascript">
            // jQuery ready fires too early, use window.onload instead
            window.onload = function () {
                tinyMCE.activeEditor.onKeyUp.add(function () {
                    
                    // Strip HTML tags, WordPress shortcodes and white space
                    var editor_content = this.getContent().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig, '');
                    jQuery('.ccp_count_post').text(editor_content.length);
                    
                    
                });
            }
        </script>
        <?php
    }

    // Character limit for page / Visual Editor
    if ($ccp_setting['cc_pages'] == 1 && get_post_type() == 'page') {
        ?>
        <script type="text/javascript">
            // jQuery ready fires too early, use window.onload instead
            window.onload = function () {
                tinyMCE.activeEditor.onKeyUp.add(function () {
                    // Strip HTML tags, WordPress shortcodes and white space
                    var editor_content = this.getContent().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig, '');
                    jQuery('.ccp_count_page').text(editor_content.length);
                    
                });
            }
        </script>
        <?php
    }
}
