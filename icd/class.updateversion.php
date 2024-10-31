<?php
namespace osmaps;

/* Plugin version 2.3.7 */

if ( ! defined( 'ABSPATH' ) ) {	exit; }

final class GetDb_and_UpdateVersion{
    static $osmaps_db_options = [];
    
    public function __construct( $OSM ) {
        /* get database options */
        $osmaps_db_options = get_option( $OSM->name );
        
        self::$osmaps_db_options = $osmaps_db_options;

        if( version_compare( '2.3.8', $osmaps_db_options[ 'version' ]) == 0) { return;}
        
        $this->Check_and_Update( $OSM );
    }
    
    
    private function Check_and_Update( $OSM ) {
        
        $osmaps_db_options = self::$osmaps_db_options;
        
        /* if the database options are empty, set the initial ones */
        if( ! $osmaps_db_options ){
            if( !class_exists('OSMaps_admin')){
                include 'class.admin.php';
            }

            $dbIni = OSMaps_admin::Db_initial_values();
            add_option( $OSM->name, ( array )$dbIni );
            $osmaps_db_options = get_option( $OSM->name );
        }
        
        /*  update from 1.0.0 */
        if( !isset( $osmaps_db_options[ 'version' ] )){
            $db_up = [];

            $db_up[ 'version' ] = '2.1.0';          /*  add version */

            $db_up[ 'mapDefault' ] = $osmaps_db_options;    /*  add the old map */

            $db_up[ 'mapDefault' ]['noWheel'] = 'checked';      /*  add disable wheel   */

            update_option( $OSM->name, ( array )$db_up );
            $osmaps_db_options = get_option( $OSM->name );
        }
        
        /* update from 1.1.0  */
        if( $osmaps_db_options[ 'version' ] === '1.1.0' ){
            $osmaps_db_options[ 'version' ] = '2.1.0';                  /*  change version */

        }
        
        /* update from  2.0.0  */
        if( $osmaps_db_options[ 'version' ] === '2.0.0' ){
            $osmaps_db_options[ 'version' ] = '2.1.0';
        }
        
        /* update from 2.1.0    */
        if( $osmaps_db_options[ 'version' ] === '2.1.0' ){
            $update_options = [];
            foreach ($osmaps_db_options as $key => $value){
               if($key === 'version') {
                   $update_options[$key] = '2.1.1';
                   continue;
               }
               
               $service = $value;  $service['ApiJs'] = 'oplj';
               $update_options[ $key ] = $service;
            }

            global $wpdb;
            $serial_update = serialize($update_options);
            $wpdb->query(
                    $wpdb->prepare(
                            "UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s", $serial_update, $OSM->name 
                            )
                    );
            $osmaps_db_options = $update_options; 
            
        }
        
        /* update from 2.1.1    */
        if( $osmaps_db_options[ 'version' ] === '2.1.1' ){
            $osmaps_db_options[ 'version' ] = '2.1.3';
        }
        
        /* update from 2.1.3 */
        if( $osmaps_db_options[ 'version' ] === '2.1.3' ){
            $osmaps_db_options[ 'version' ] = '2.2.0';
        }
        
        /* update from 2.2.0 */
        if( $osmaps_db_options[ 'version' ] === '2.2.0' ){
            $osmaps_db_options[ 'version' ] = '2.2.1';
        }

        /* update from 2.2.1 */
        if( $osmaps_db_options[ 'version' ] === '2.2.1' ){
            $update_options = [];
            foreach ($osmaps_db_options as $key => $value){
                if($key === 'version') {
                    $update_options[$key] = '2.2.2';
                   
                   /* inserisce tiles */
                    $update_options[ 'sTiles' ] = ['openstreet' => '',                              /*  contains the key of tiles */
                                                  'mapbox'=> ''];
                    continue;
                }
                /* inserisce tiles in ogni singola mappa */
                $service = $value;  $service['tiles'] = 'OpenStreet';
                $update_options[ $key ] = $service;
            }
            
            global $wpdb;
            $serial_update = serialize($update_options);
            $wpdb->query(
                    $wpdb->prepare(
                            "UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s", $serial_update, $OSM->name 
                            )
                    );
            
            $osmaps_db_options = $update_options; 
        }
        
        /* update from 2.2.2 */
        if( $osmaps_db_options[ 'version' ] === '2.2.2' ){
             $update_options = [];
             foreach ($osmaps_db_options as $key => $value){
                 if($key === 'version') {
                     $update_options[$key] = '2.3.0';
                     continue;
                 }
                 
                 if($key === 'sTiles') {
                     $service = $value;
                     $update_options[ $key ] = $service;
                     continue;
                 }
                 
                 $service = $value; $service['ckPopup'] = '';
                 $update_options[ $key ] = $service;
             }
             
            global $wpdb;
            $serial_update = serialize($update_options);
            $wpdb->query(
                    $wpdb->prepare(
                            "UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s", $serial_update, $OSM->name 
                            )
                    );
            
            $osmaps_db_options = $update_options; 

        }

         /* update from 2.3.0   */
        if( $osmaps_db_options[ 'version' ] === '2.3.0' ){
            $osmaps_db_options[ 'version' ] = '2.3.5';
        }

        /* update from 2.3.5    */
        if( $osmaps_db_options[ 'version' ] === '2.3.5' ){
            $osmaps_db_options[ 'version' ] = '2.3.7';
        }

        /* update from 2.3.7    */
        if( $osmaps_db_options[ 'version' ] === '2.3.7' ){
            $osmaps_db_options[ 'version' ] = '2.3.8';
        }
        
        self::$osmaps_db_options = $osmaps_db_options;          //  when registering a new map 
                                                                //  the new Osmaps version will also be registered in the db


    }
}