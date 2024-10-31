<?php
/*
Plugin Name: OSMaps
Plugin URI: https://osmaps.edilweb.eu
Description: It is a plugin which displays maps on your site. Source Tiles: OpenStreetMap. API javascript: OpenLayers and Leaflet.
Version: 2.3.8
Author: Alessandro Lin
Author URI:  https://osmaps.edilweb.eu
License: GPL-2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: osmapsWP
Domain Path: /languages
*/

/**
 * Copyright 2023 Alessandro Lin
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
namespace osmaps;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {	exit; }


/**
 * Nothing at the moment
 *
 * @return void
 */
function activate_osmaps(){}

/**
 * Nothing at the moment
 *
 * @return void
 */
function deactivate_osmaps() {}


function add_action_link_osmaps( $links ) {
    $mylinks = [ '<a href="' . admin_url( 'options-general.php?page=osmaps_options' ) . '">' . __( 'Settings', 'osmapsWP' ) .'</a>' ];
    return array_merge( $links, $mylinks );
}
    
function osmaps_textdomain(){
    global $OSM;
    load_plugin_textdomain( $OSM->name, FALSE, ( dirname( plugin_basename( __FILE__ ) ) . '/languages' )); 
}


/* ******** */ 
/*   main   */
/* ******** */ 

$OSM = new \stdClass;                        /*  global  */
$OSM->name = 'osmapsWP';
$OSM->dirURL = plugin_dir_url(__FILE__);
$OSM->shortcode = 'osmaps_display';         /*  [osmaps_display]    */
$OSM->adminErr = 'OSMadminError';

register_activation_hook( __FILE__, 'osmaps\activate_osmaps' );
register_deactivation_hook( __FILE__, 'osmaps\deactivate_osmaps' );

add_action( 'plugins_loaded', 'osmaps\osmaps_textdomain' );

if( !class_exists( 'osmaps\GetDb_and_UpdateVersion' )){
    include __DIR__ . '/icd/class.updateversion.php';    
}
new GetDb_and_UpdateVersion( $OSM );

/* @var $osmaps_db_options array. get database options */
$osmaps_db_options = GetDb_and_UpdateVersion::$osmaps_db_options;           /*  global  */


if( is_admin() ){
    if( !trait_exists('osmaps\OSMaps_admin')){
        include __DIR__ . '/icd/class.admin.php';
    }
    add_action( 'admin_init', 'osmaps\OSMaps_admin::osmaps_register_setting' );  
    
    add_action( 'admin_menu', 'osmaps\OSMaps_admin::add_submenu_page' );
    
    add_action('admin_enqueue_scripts', 'osmaps\OSMaps_admin::load_admin_external');
    
    add_filter( 'plugin_action_links_'. plugin_basename(__FILE__), 'osmaps\OSMaps_admin::add_link_settings' );
}
else{
    include __DIR__ . '/icd/class.frontend.php';
    
    new OSMaps_frontend( $OSM, $osmaps_db_options );    
}
