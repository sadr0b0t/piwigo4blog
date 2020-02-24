<?php
/*
Version: 0.1
Plugin Name: piwigo4blog
Plugin URI: // Here comes a link to the Piwigo extension gallery, after
           // publication of your plugin. For auto-updates of the plugin.
Author: // sadr0b0t.
Description: Mass export images from gallery wrapped in html tags for blog post.
*/

// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
 
// Define the path to our plugin.
define('PIWIGO4BLOG_PATH', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');

// Hook on to an event to show the administration page.
add_event_handler('get_admin_plugin_menu_links', 'piwigo4blog_admin_menu');

// Add an entry to the 'Plugins' menu.
function piwigo4blog_admin_menu($menu) {
    array_push(
        $menu,
        array(
            'NAME'  => 'piwigo4blog',
            // https://fotki.sadrobot.su/admin.php?page=plugin-piwigo4blog
            'URL' => get_root_url().'admin.php?page=plugin-piwigo4blog'
            // example does not work:
            //'URL'   => get_admin_plugin_menu_link(dirname(__FILE__)).'/admin.php'
            // https://fotki.sadrobot.su/admin.php?page=plugin&section=piwigo4blog/admin.php
            // [Hacking attempt] the input parameter "section" is not valid
            // #1	check_input_parameter /var/www/piwigo/admin.php(30)
        )
    );
    return $menu;
}
?>

