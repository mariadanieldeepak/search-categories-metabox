<?php
/*
Plugin Name: Search Categories Metabox
Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
Description: This plugin includes a search field to the Categories Metabox in the WordPress Editor.
Version:     0.1.0
Author:      Maria Daniel Deepak
Author URI:  http://mariadanieldeepak.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: search-categories-metabox
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-search-categories-metabox.php';

function run_search_categories_metabox() {

    if( ! isset( $search_categories_metabox ) ) {
        $search_categories_metabox = new Search_Categories_Metabox( __FILE__ );
    }
    $search_categories_metabox->run();

}

run_search_categories_metabox();

