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

add_action( 'admin_menu', 'search_categories_metabox_menu' );

function search_categories_metabox_menu() {
    add_options_page( "Search Categories Metabox", "Search Categories Mb", "manage_options", "search-categories-metabox", "render_page");
}