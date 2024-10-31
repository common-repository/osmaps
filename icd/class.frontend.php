<?php
namespace osmaps;

if ( ! defined( 'ABSPATH' ) ) {	exit; }

final class OSMaps_frontend{
    public $OS = '';
    public $db_options = [];        /*  options for all maps */
    
    private $path_button_map = '';
            
    public function __construct( $OSM, $osmaps_db_options ) {
        $this->OS = $OSM;
        $this->OS->markerURL = $this->OS->dirURL  . 'public/marker-green.png';
        $this->OS->urlDirections = 'https://www.openstreetmap.org/directions?from=';
        
        $this->path_button_map = ABSPATH . 'wp-content/plugins/osmaps/public/tmp';        
        (! is_dir($this->path_button_map)) ? mkdir($this->path_button_map) : '' ;
        
        $this->db_options = $osmaps_db_options;
        
        add_action( 'wp_enqueue_scripts', [ $this, 'load_external_resources' ] );

        add_shortcode( $this->OS->shortcode, [ $this, 'shortcodes_OSMaps_exec' ] );
    }
    
    
    public function load_external_resources(){
        $deps = [];
        /*   OpenLayer  */
        wp_register_style( 'olmincss', $this->OS->dirURL . 'public/ol.min.css', $deps, null );
        wp_enqueue_style( 'olmincss' );
        
        wp_register_style( 'popupmincss', $this->OS->dirURL . 'public/popup.min.css', $deps, null );
        wp_enqueue_style( 'popupmincss' );
       
        /*  Leaflet css */
        wp_register_style('LeafletCss', "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css", $deps, '2.3.5');
        wp_enqueue_style( 'LeafletCss' );

        /*  button link  2.3.0   */
        global $wp_styles;
        $wp_styles->add_inline_style( 'LeafletCss', $this->linkButtonStyle() );
        
        /*  Leaflet js  */
        wp_register_script( 'LeafletJs', "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js", $deps, '2.3.5',false);
        wp_enqueue_script('LeafletJs');
    }
    
    /**
     * css for button link  ver 2.3.0
     * @return string
     */
    private function linkButtonStyle() {
        return  <<<'STI'
/*  --- osmaps button  ---  */
/*  --- css by w3school --  */
.OSMbutton:link, .OSMbutton:visited {
  background-color: white;
  color: black;
  border: 2px solid blue;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
}

.OSMbutton:hover, .OSMbutton:active {
  background-color: blue;
  color: white;
}
STI;
    }
    
    /**
     * Read and execute the shortcode in a web page.
     * @param array $atts
     * @return a map or one error.
     */
    public function shortcodes_OSMaps_exec( $atts ){
       $id = '';
       
       extract( shortcode_atts([ 'id' => '' ], (array)$atts ));
       
       if(!empty($atts)){
           $id = $atts[ 'id' ];
       }

        /* $id vuoto mantiene la compatibilità con le versioni precedenti     */

        /* $db_options. Extracts the Options for one map.                     */

        if( $id === '') { $db_options = $this->db_options[ 'mapDefault' ]; } 
        else {
            /* Se mappa è stata cancellata: 'Undefined array key "Verona" ' */
            if(isset($this->db_options[ $id ])) {
                $db_options = $this->db_options[ $id ];         /*  $db_options di una singola mappa    */
            }
        }
        
        if(isset($db_options['ApiJs'])){                   /* 'undefined $db_options se mappa cancellata' */
            if( $db_options['ApiJs'] === 'oplj'){
                return $this->DisplayOpenLayer($db_options, $id); 
            }
        
            if( $db_options['ApiJs'] === 'leaf'){
                return $this->DisplayLeaflet($db_options, $id); 
            }
        }
        
        $erMsg = esc_html__( 'API javascript unknown', 'osmapsWP' );
        $erMsg .= ' - Error Map:' . $id . ' . ' . esc_html__( 'Delete shortcode in this page or post.', 'osmapsWP' );

        return $erMsg;
    }

    
    private function CleanTxtArea($param){
         if ( isset( $param['mapopup'] )){
            $param['mapopup'] = str_replace( "\r\n", "<br>", $param['mapopup'] );
            $param['mapopup'] = str_replace( ["\n", "\t", "\r"], "&nbsp", $param['mapopup'] );
            $param['mapopup'] = str_replace( "'", "&#39;", $param['mapopup'] );
            $param['mapopup'] = str_replace( "&lt;", "<", $param['mapopup'] ); 
            $param['mapopup'] = str_replace( "&gt;", ">", $param['mapopup'] );
        }
        return $param;
    }
    
    /**
     * Display Map with OpenLayer API
     * @param array $db_options
     * @param name of the map string $id
     * @return string
     */
    private function DisplayOpenLayer($db_options, $id = '' ) {
        
        $db_options = $this->CleanTxtArea($db_options);
        
        switch ( $db_options['mptype'] ){
            case 1:
                $a = $this->HTMLsimple($db_options, $id);
                break;
            case 2:
                $a = $this->HTMLmarker($db_options, $id);
                break;
            case 3:
                $a = $this->HTMLpopup($db_options, $id);
                break;
            case 5:
                $a = $this->HTMLmarker_popup2($db_options, $id);     /*  marker and popup    */
                break;

            default:
                $a = sprintf( esc_html__( 'OSMaps error #1: wrong select! %s does not exist.', 'osmapsWP' ), $db_options['mptype'] );
                break;
        }

        return $a;
    }
    
    
    /**
     * Display map with Leaflet API. 
     * Look /public/expanded/MapLeaflet.php for expanded code
     * @param (array) $db_options. Options for one map.
     * @return string
     */
    private function DisplayLeaflet($db_options, $id = '') {
        
        if( $db_options[ 'tiles' ] === 'mapb' ){
            return $this->DisplayMapbox($db_options, $id);
        }
        
        $db_options = $this->CleanTxtArea($db_options);
        
        switch ( $db_options['mptype'] ){
            case 1:
                $a = $this->LeafHtmlSimple($db_options, $id);
                break;
            case 2:
                $a = $this->LeafHtmlMarker($db_options, $id);
                break;
            case 3:
                $a = $this->LeafHtmlPopup($db_options, $id);
                break;
            case 5:
                $a = $this->LeafHtmlMarkerPopup($db_options, $id);
                break;
            default:
                $a = sprintf( esc_html__( 'OSMaps error #1: wrong select! %s does not exist.', 'osmapsWP' ), $db_options['mptype'] );
                break;
        }
        
        return $a;
    }
    
    /**
     * Look /public/expanded/Mapbox.php for expanded code
     * Display one map with Leaflet API and Mapbox images (tiles).
     * @param array  $db_options of the map
     * @param string $id         name of the map
     * @return string
     */
    private function DisplayMapbox($db_options, $id = '') {
        $db_options = $this->CleanTxtArea($db_options);
        
        switch ( $db_options['mptype'] ){
            case 1:
                $a = $this->MboxHtmlSimple($db_options, $id);
                break;
            case 2:
                $a = $this->MBoxHtmlMarker($db_options, $id);
                break;
            case 3:
                $a = $this->MBoxHtmlPopup($db_options, $id);
                break;
            case 5:
                $a = $this->MBoxMarkerPopup($db_options, $id);
                break;
            default:
                $a = sprintf( esc_html__( 'OSMaps error #1: wrong select! %s does not exist.', 'osmapsWP' ), $db_options['mptype'] );
                break;
        }
        
        return $a;
        
    }
    
    /**
     * custom style map container 
     * @param type $db_options
     * @return string
     */
    private function customStyle($db_options){
    /*  'px' is already present in the options of database.  Add only ';'    */
        ( $db_options['maxwtd'] != '' ) ?  ( $wdt = 'max-width:' . $db_options['maxwtd'] . ';' ) : $wdt = '' ;
        
        return <<<PStyle
style = "height:{$db_options['height']}; {$wdt}"
PStyle;

    }
    
    /**
     * Html that display a button "VIEW MAP", in a new window.
     * @param string $id 
     * @param string $url
     * @return html string
     */
    private function DisplayButton($id, $url) {
        if($id === ''){ $id = 'mapDefault'; }
        
        $ViewMap = esc_html__( 'VIEW MAP', 'osmapsWP' );
        
        return <<<btn
<div class="OSMdivButton"><button onclick="window.open('{$url}', '{$id}')">{$ViewMap}</button></div>               
btn;

    }
    
    /**
     * Button that display "CLOSE MAP"
     * @return string
     */
    private function DisplayButton_CloseMap() {

        return <<<btn
<button class="button" onclick="window.close();" > CLOSE MAP </button>
btn;

    }
        
    
    /**
     * Display one map with marker and popup. Mapbox tiles and Leaflet APIs.
     * See  /wp-content/plugins/osmaps/public/expanded/MapMapbox.php
     * Case 5
     * @param string $id  name of the map
     * @param array $db_options
     * @return string
     */
    private function MBoxMarkerPopup($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button "View map" , in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $MPboxWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapMpbox{height:70vh;}</style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapMapbox" class="mapMpbox"></div>
        <script id="MBLFmarkerPopup">
            document.addEventListener('DOMContentLoaded', ()=>{
                var mapMapBoxjs = L.map( 'mapMapbox',{
                    center:[{$db_options['latitude']}, {$db_options['longitude']}],
                    zoom: {$db_options['zoom']},
                    scrollWheelZoom: {$wheel}
                });
                var marker = L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapMapBoxjs);
                marker.bindPopup('{$db_options['mapopup']}').openPopup();
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox/satellite-streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '{$this->db_options['sTiles']['mapbox']}'
                }).addTo(mapMapBoxjs);

                (()=>{
                    try{ var id = 'mapMapbox', pElem = '#' + id + ' ' + '.leaflet-popup-tip', tip = document.querySelector(pElem);
                        tip.style.setProperty('visibility', 'hidden'); }
                    catch(e){ console.warn(e); }
                })();

            });
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $MPboxWindow);
            return $this->DisplayButton($id, $mapURL);
        }
        /*  --- end button ---------    */

        /* html + javascript code   */
        return <<<MPB
<div id="mapMapbox" {$this->customStyle($db_options)}></div>
<script id="MBLFmarkerPopup">
    document.addEventListener('DOMContentLoaded', ()=>{
        var mapMapBoxjs = L.map( 'mapMapbox',{
            center:[{$db_options['latitude']}, {$db_options['longitude']}],
            zoom: {$db_options['zoom']},
            scrollWheelZoom: {$wheel}
        });
        var marker = L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapMapBoxjs);
        marker.bindPopup('{$db_options['mapopup']}').openPopup();
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/satellite-streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: '{$this->db_options['sTiles']['mapbox']}'
        }).addTo(mapMapBoxjs);
            
        (()=>{
            try{ var id = 'mapMapbox', pElem = '#' + id + ' ' + '.leaflet-popup-tip', tip = document.querySelector(pElem);
                tip.style.setProperty('visibility', 'hidden'); }
            catch(e){ console.warn(e); }
        })();

    });
</script>
MPB;

    }
    
    /**
     * Display one map with popup. Mapbox tiles and Leaflet APIs.
     * See  /wp-content/plugins/osmaps/public/expanded/MapMapbox.php
     * Case 3
     * @param options of the map string $db_options 
     * @param name of the map array $id
     * @return string
     */
    private function MBoxHtmlPopup($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $MPboxWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapMpbox{height:70vh;}</style>            
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapMapbox" class="mapMpbox"></div>
        <script id="MBLFpopup">
            document.addEventListener('DOMContentLoaded', ()=>{
                var mapMapBoxjs = L.map( 'mapMapbox',{
                    center:[{$db_options['latitude']}, {$db_options['longitude']}],
                    zoom: {$db_options['zoom']},
                    scrollWheelZoom: {$wheel}
                });
                L.popup().setLatLng([{$db_options['latitude']}, {$db_options['longitude']}]).setContent('{$db_options['mapopup']}').openOn(mapMapBoxjs); 

                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox/satellite-streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '{$this->db_options['sTiles']['mapbox']}'
                }).addTo(mapMapBoxjs);
            });
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $MPboxWindow);
            return $this->DisplayButton($id, $mapURL);
        }
        /*  --- end button ---------    */

        /* html + javascript code   */
        return <<<MPB
<div id="mapMapbox" {$this->customStyle($db_options)}></div>
<script id="MBLFpopup">
    document.addEventListener('DOMContentLoaded', ()=>{
        var mapMapBoxjs = L.map( 'mapMapbox',{
            center:[{$db_options['latitude']}, {$db_options['longitude']}],
            zoom: {$db_options['zoom']},
            scrollWheelZoom: {$wheel}
        });
        L.popup().setLatLng([{$db_options['latitude']}, {$db_options['longitude']}]).setContent('{$db_options['mapopup']}').openOn(mapMapBoxjs); 
            
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/satellite-streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: '{$this->db_options['sTiles']['mapbox']}'
        }).addTo(mapMapBoxjs);
    });
</script>
MPB;

    }
    
    /**
     * Display one map with marker. Mapbox tiles and Leaflet APIs
     * See  /wp-content/plugins/osmaps/public/expanded/MapMapbox.php
     * Case 2
     * @param array $db_options
     * @param string $id  name of the map
     * @return string
     */
    private function MBoxHtmlMarker($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $MPboxWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapMpbox{height:70vh;}</style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapMapbox" class="mapMpbox"></div>
        <script id="MBLFmarker">
            document.addEventListener('DOMContentLoaded', ()=>{
                var mapMapBoxjs = L.map( 'mapMapbox',{
                    center:[{$db_options['latitude']}, {$db_options['longitude']}],
                    zoom: {$db_options['zoom']},
                    scrollWheelZoom: {$wheel}
                });
                L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapMapBoxjs);
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox/satellite-streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '{$this->db_options['sTiles']['mapbox']}'
                }).addTo(mapMapBoxjs);
            });
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $MPboxWindow);
            return $this->DisplayButton($id, $mapURL);

        }
        /*  --- end button -------  */

        /* html + javascript code   */
        return <<<MPB
<div id="mapMapbox" {$this->customStyle($db_options)}></div>
<script id="MBLFmarker">
    document.addEventListener('DOMContentLoaded', ()=>{
        var mapMapBoxjs = L.map( 'mapMapbox',{
            center:[{$db_options['latitude']}, {$db_options['longitude']}],
            zoom: {$db_options['zoom']},
            scrollWheelZoom: {$wheel}
        });
        L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapMapBoxjs);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/satellite-streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: '{$this->db_options['sTiles']['mapbox']}'
        }).addTo(mapMapBoxjs);
    });
</script>
MPB;

    }
    
    /**
     * Map simple with Mapbox tiles and Leaflet library
     * See  /wp-content/plugins/osmaps/public/expanded/MapMapbox.php
     * Case 1
     * @param array  $db_options
     * @param string $id  name of the map
     * @return string
     */
    private function MboxHtmlSimple($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $MPboxWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapMpbox{height:70vh;}</style>            
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapMapbox" class="mapMpbox"></div>
        <script id="MBLFsimple">
            document.addEventListener('DOMContentLoaded', ()=>{
                var mapMapBoxjs = L.map( 'mapMapbox',{
                    center:[{$db_options['latitude']}, {$db_options['longitude']}],
                    zoom: {$db_options['zoom']},
                    scrollWheelZoom: {$wheel}
                });

                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox/satellite-streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '{$this->db_options['sTiles']['mapbox']}'
                }).addTo(mapMapBoxjs);
            });
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $MPboxWindow);
            return $this->DisplayButton($id, $mapURL);

        }
        /*  --- end button -----    */

        /* html + javascript code   */
        return <<<MPB
<div id="mapMapbox" {$this->customStyle($db_options)}></div>
<script id="MBLFsimple">
    document.addEventListener('DOMContentLoaded', ()=>{
        var mapMapBoxjs = L.map( 'mapMapbox',{
            center:[{$db_options['latitude']}, {$db_options['longitude']}],
            zoom: {$db_options['zoom']},
            scrollWheelZoom: {$wheel}
        });
        
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/satellite-streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: '{$this->db_options['sTiles']['mapbox']}'
        }).addTo(mapMapBoxjs);
    });
</script>
MPB;

    }
    
    
    /**
     * API Leaflet. See /wp-content/plugins/osmaps/public/expanded/MapLeaflet.php
     * Case 1
     * @param type $db_options
     * @param string $id  name of the map
     * @return string
     */
    private function LeafHtmlSimple($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
         
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $lfMapWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapLeaflet{height:70vh;}</style>            
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        {$this->DisplayButton_CloseMap()}
        <br>

        <div id="mapLeaflet" class="mapLeaflet"></div>
        <script id="LeafSimple">
            var mapLeaf_js = L.map( 'mapLeaflet',{
                zoom: {$db_options['zoom']},
                scrollWheelZoom: {$wheel}
            }).setView([{$db_options['latitude']}, {$db_options['longitude']}]);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapLeaf_js);
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $lfMapWindow);
            return $this->DisplayButton($id, $mapURL);

        }
        
        /*  --- end button -------  */

        /* html + javascript code   */
        return <<<Leaf
<br>
<div id="mapLeaflet" {$this->customStyle($db_options)}></div>

<script id="LeafSimple">
var mapLeaf_js = L.map( 'mapLeaflet',{
    zoom: {$db_options['zoom']},
    scrollWheelZoom: {$wheel}
}).setView([{$db_options['latitude']}, {$db_options['longitude']}]);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mapLeaf_js);
</script>
Leaf;

    }
    
    
    /**
     * API Leaflet. See /wp-content/plugins/osmaps/public/expanded/MapLeaflet.php
     * Case 2
     * @param type $db_options
     * @param string $id  name of the map
     * return string
     */
    private function LeafHtmlMarker($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $lfMapWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapLeaflet{height:70vh;}</style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapLeaflet" class="mapLeaflet"></div>
        <script id="LeafMarker">
            let mapLeaf_js = L.map( 'mapLeaflet',{
                zoom: {$db_options['zoom']},
                scrollWheelZoom: {$wheel}
            }).setView([{$db_options['latitude']}, {$db_options['longitude']}]);
            
            let LeafMarker = L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapLeaf_js);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapLeaf_js);
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $lfMapWindow);
            return $this->DisplayButton($id, $mapURL);

        }

        /*  ----------  */
        /* html + javascript code   */
        return <<<Leaf
<br>
<div id="mapLeaflet" {$this->customStyle($db_options)}></div>

<script id="LeafMarker">
var mapLeaf_js = L.map( 'mapLeaflet',{
    zoom: {$db_options['zoom']},
    scrollWheelZoom: {$wheel}
}).setView([{$db_options['latitude']}, {$db_options['longitude']}]);

var LeafMarker = L.marker( [ {$db_options['latitude']}, {$db_options['longitude']} ] ).addTo(mapLeaf_js);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mapLeaf_js);
</script>
Leaf;

    }
    
    
    /**
     * API leaflet. Case 3.
     * @param type $db_options
     * @param string $id  name of the map
     * @return string
     */
    private function LeafHtmlPopup($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

            $lfMapWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapLeaflet{height:70vh;}</style>            
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapLeaflet" class="mapLeaflet"></div>
        <script id="LeafPopup">
            var mapLeaf_js = L.map( 'mapLeaflet',{
                zoom: {$db_options['zoom']},
                scrollWheelZoom: {$wheel}
            }).setView([{$db_options['latitude']}, {$db_options['longitude']}]);

            var popup = L.popup()
            .setLatLng([{$db_options['latitude']}, {$db_options['longitude']}])
            .setContent('{$db_options['mapopup']}')
            .openOn(mapLeaf_js);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapLeaf_js);
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $lfMapWindow);
            return $this->DisplayButton($id, $mapURL);

        }
        /*  --- end button -------  */

        /* html + javascript code   */
        return <<<Leaf
<br>
<div id="mapLeaflet" {$this->customStyle($db_options)}></div>

<script id="LeafPopup">
var mapLeaf_js = L.map( 'mapLeaflet',{
    zoom: {$db_options['zoom']},
    scrollWheelZoom: {$wheel}
}).setView([{$db_options['latitude']}, {$db_options['longitude']}]);

var popup = L.popup()
    .setLatLng([{$db_options['latitude']}, {$db_options['longitude']}])
    .setContent('{$db_options['mapopup']}')
    .openOn(mapLeaf_js);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mapLeaf_js);
</script>
Leaf;

    }
    
    
    /**
     * API Leaflet. See /wp-content/plugins/osmaps/public/expanded/MapLeaflet.php
     * Case 5
     * @param type $db_options
     * @param string $id  name of the map
     * return string
     */
    private function LeafHtmlMarkerPopup($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        /* Disable wheel, optional  */
        ($db_options['noWheel'] === 'checked') ? $wheel = 'false' : $wheel = 'true';
        
        /* create button. View map in a new window  */
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';

           $lfMapWindow = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .mapLeaflet{height:70vh;}</style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <div id="mapLeaflet" class="mapLeaflet"></div>
        <script id="LeafMarkerPopup">
            document.addEventListener('DOMContentLoaded', ()=>{
                var mapLeaf_js = L.map( 'mapLeaflet',{
                    zoom: {$db_options['zoom']},
                    scrollWheelZoom: {$wheel}
                }).setView([{$db_options['latitude']}, {$db_options['longitude']}]);
                var LeafMarker = L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapLeaf_js);
                LeafMarker.bindPopup('{$db_options['mapopup']}').openPopup();
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(mapLeaf_js);
    
            (function (){
                try{ var id = 'mapLeaflet', pElem = '#' + id + ' ' + '.leaflet-popup-tip', tip = document.querySelector(pElem);
                    tip.style.setProperty('visibility', 'hidden'); }
                    catch(e){ console.warn(e); }
            })();
            });
        </script>
    </body>
</html>
BTN;
            
            file_put_contents($this->path_button_map . '/' . $id . '.html', $lfMapWindow);
            return $this->DisplayButton($id, $mapURL);

        }

        /*  -------------   */
        /* html + javascript code   */
        return <<<Leaf
<br>
<div id="mapLeaflet" {$this->customStyle($db_options)}></div>

<script id="LeafMarkerPopup">
document.addEventListener('DOMContentLoaded', ()=>{
    var mapLeaf_js = L.map( 'mapLeaflet',{
        zoom: {$db_options['zoom']},
        scrollWheelZoom: {$wheel}
    }).setView([{$db_options['latitude']}, {$db_options['longitude']}]);
    var LeafMarker = L.marker([{$db_options['latitude']}, {$db_options['longitude']}]).addTo(mapLeaf_js);
    LeafMarker.bindPopup('{$db_options['mapopup']}').openPopup();
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mapLeaf_js);
    
    (function (){
        try{ var id = 'mapLeaflet', pElem = '#' + id + ' ' + '.leaflet-popup-tip', tip = document.querySelector(pElem);
            tip.style.setProperty('visibility', 'hidden'); }
        catch(e){ console.warn(e); }
    })();
});
</script>
Leaf;

    }
    
    
    /**
     * API OpenLayer. Case: 1 
     * look /plugins/OSMaps/public/expanded/HTMLsimple.html     for expanded code
     * @param string $db_options 
     * @param string $id  name of the map 
     * @return string
     */
    private function HTMLsimple($db_options, $id = '' ) {
        if($id === ''){ $id = 'mapDefault'; }
        
        $JsURL = $this->OS->dirURL . 'public/ol.min.js';
        
        ($db_options[ 'noWheel' ] === 'checked') ? $wheel = "interactions: ol.interaction.defaults( {mouseWheelZoom:false} )," : $wheel = '';
                
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';
            
            $fileStyle = $this->OS->dirURL . 'public/ol.min.css';
           
            $Map_window = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" id="olmincss-css" href="{$fileStyle}" media="all">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .osmapsMap{height:70vh;}</style>
    </head>
    <body>
{$this->DisplayButton_CloseMap()}
<br>
<script src="{$JsURL}"></script>
   
<div id="osmaps_map" class="osmapsMap"></div>
<script>
var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({source: new ol.source.OSM()})],target: 'osmaps_map',view: new ol.View({ center: ol.proj.fromLonLat([{$db_options['longitude']}, {$db_options['latitude']}]), zoom: {$db_options['zoom']}})});
</script>
</body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $Map_window);
            
            return $this->DisplayButton($id, $mapURL);

        }
        
/*  ----------      */
        $ApiLoad = <<<API
<script src="{$JsURL}"></script>
API;
        
        $html = <<<HTM
<div id="osmaps_map"  class="osmapsMap" {$this->customStyle($db_options)}></div>
<script>
var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({source: new ol.source.OSM()})],target: 'osmaps_map',view: new ol.View({ center: ol.proj.fromLonLat([{$db_options['longitude']}, {$db_options['latitude']}]), zoom: {$db_options['zoom']}})});
</script>
HTM;
        return $ApiLoad . $html;
        
    }
    
    
    /**
     * API OpenLayer. Case: 2
     * look /plugins/OSMaps/public/expanded/HTMLmarker.html     for expanded code
     * @return string
     */
    private function HTMLmarker($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }

        $JsURL = $this->OS->dirURL . 'public/ol.min.js'; 
        
        ( $db_options['noWheel'] === 'checked' ) ? $wheel = "interactions: ol.interaction.defaults( {mouseWheelZoom:false} )," : $wheel = '';

        if( $db_options[ 'ckPopup' ] === 'checked' ){
            
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';
            $fileStyle = $this->OS->dirURL . 'public/ol.min.css';
            
            $Map_window = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" id="olmincss-css" href="{$fileStyle}" media="all">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .osmapsMap{height:70vh;}</style>
    </head>
    <body>
<!-- <button class="button" onclick="window.open('','_self').close();" > CLOSE MAP </button> -->
    {$this->DisplayButton_CloseMap()}
<br>
<script src="{$JsURL}"></script>

<div id="osmaps_map" class="osmapsMap"></div>
<script>
var city = new ol.Feature({geometry: new ol.geom.Point(ol.proj.fromLonLat([{$db_options['longitude']}, {$db_options['latitude']}]))}); city.setStyle(new ol.style.Style({image: new ol.style.Icon(({src: '{$this->OS->markerURL}', anchor: [0.5, 1]}))}));
var vectorSource = new ol.source.Vector({features: [city]});
var vectorLayer = new ol.layer.Vector({source: vectorSource});
var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({source: new ol.source.OSM()}),vectorLayer],target: 'osmaps_map', view: new ol.View({center: ol.proj.fromLonLat([{$db_options['longitude']}, {$db_options['latitude']}]), zoom: {$db_options['zoom']}})});
</script>
</body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $Map_window);
            
            return $this->DisplayButton($id, $mapURL);
        }
        
        /*  ----------  */
        $apiLoad = <<<API
<script src="{$JsURL}"></script>
API;
        
        $html = <<<HTM
<div id="osmaps_map" class="osmapsMap" {$this->customStyle($db_options)}></div>
<script>
var city = new ol.Feature({geometry: new ol.geom.Point(ol.proj.fromLonLat([{$db_options['longitude']}, {$db_options['latitude']}]))}); city.setStyle(new ol.style.Style({image: new ol.style.Icon(({src: '{$this->OS->markerURL}', anchor: [0.5, 1]}))}));
var vectorSource = new ol.source.Vector({features: [city]});
var vectorLayer = new ol.layer.Vector({source: vectorSource});
var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({source: new ol.source.OSM()}),vectorLayer],target: 'osmaps_map', view: new ol.View({center: ol.proj.fromLonLat([{$db_options['longitude']}, {$db_options['latitude']}]), zoom: {$db_options['zoom']}})});
</script>
HTM;
        return $apiLoad . $html;
    }
    
    
    /**
     * Popup for Openlayer Api
     * @param string $db_options
     * @return string html
     */
    private function HTMLpopup_sub($db_options) {
        $directions_icon_url = $this->OS->dirURL . 'public/Directions_image1.png';
        
        $JsURL = $this->OS->dirURL . 'public/ol.min.js';
        $ApiLoad = <<<API
<script src="{$JsURL}"></script>
API;
        
        $html = <<<HEO
<div id="os_map" class="osmapsMap" {$this->customStyle($db_options)}></div>
<div id="popup" class="ol-popup">
    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
    <div class="ol-popup-direc">
    <a id="popup_href_directions" target="_blank"><img alt="Directions Icon" src="{$directions_icon_url}" title="Directions"></a>
    </div>
    <div id="popup_content" class="ol-popup-content">{$db_options['mapopup']}</div>
</div>
HEO;
        return $ApiLoad . $html;
    } 
    
    
    /**
     * API Openlayer. Case: 3
     * look /plugins/OSMaps/public/expanded/HTMLpopup.html     for expanded code
     * @return string
     */
    private function HTMLpopup( $db_options, $id = '' ) {
        if($id === ''){ $id = 'mapDefault'; }
        
        ($db_options['noWheel'] === 'checked') ? $wheel = "interactions: ol.interaction.defaults( {mouseWheelZoom:false} )," : $wheel = '';
        
        if( $db_options[ 'ckPopup' ] === 'checked' ){
            
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';
            
            $fileStyle = $this->OS->dirURL . 'public/ol.min.css';
            $fileStylePopup = $this->OS->dirURL . 'public/popup.min.css';
            
            $JsURL = $this->OS->dirURL . 'public/ol.min.js';
            $directions_icon_url = $this->OS->dirURL . 'public/Directions_image1.png';
            
            $Map_window = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" id="olmincss-css" href="{$fileStyle}" media="all">
        <link rel="stylesheet" id="popupmincss-css" href="{$fileStylePopup}" type="text/css" media="all">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .osmapsMap{height:70vh;}</style>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}<br>
        <script src="{$JsURL}"></script>
        <div id="os_map" class="osmapsMap"></div>
        <div id="popup" class="ol-popup">
            <a href="#" id="popup-closer" class="ol-popup-closer"></a>
            <div class="ol-popup-direc">
                <a id="popup_href_directions" target="_blank"><img alt="Directions Icon" src="{$directions_icon_url}" title="Directions"></a>
            </div>
            <div id="popup_content" class="ol-popup-content">{$db_options['mapopup']}</div>
        </div>
        <script>
        var markerLon = {$db_options['longitude']}, markerLat = {$db_options['latitude']}, myZoom = {$db_options['zoom']};
        var centerLon = markerLon, centerLat = markerLat;
        var container = document.getElementById('popup');
        var closer = document.getElementById('popup-closer');
        var Lat2Direc, Lon2Direc;
        (Number.isInteger(markerLat)) ? Lat2Direc = markerLat.toString()+'.00' : Lat2Direc = markerLat.toString();
        (Number.isInteger(markerLon)) ? Lon2Direc = markerLon.toString()+'.00' : Lon2Direc = markerLon.toString();
        popup_href_directions.href = '{$this->OS->urlDirections}' + Lat2Direc + ',' + Lon2Direc;
        var overlay = new ol.Overlay({ element: container, autoPan: true, position: ol.proj.fromLonLat([markerLon, markerLat]),offset: [-80,-30],autoPanAnimation: { duration: 250}});
        closer.onclick = function(){overlay.setPosition(undefined); closer.blur(); return false;};
        var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({ source: new ol.source.OSM()})],overlays: [overlay], target: 'os_map', view: new ol.View({center: ol.proj.fromLonLat([centerLon, centerLat]), zoom: myZoom})});
        map.on('singleclick', function () {overlay.setPosition(ol.proj.fromLonLat([markerLon, markerLat]));});
        </script>
    </body>
    </html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $Map_window);
            
            return $this->DisplayButton($id, $mapURL);
        }
        
        /*  -----   */
        $html = $this->HTMLpopup_sub($db_options);
        
        $js = <<<JS
<script>
var markerLon = {$db_options['longitude']}, markerLat = {$db_options['latitude']}, myZoom = {$db_options['zoom']};
var centerLon = markerLon, centerLat = markerLat;
var container = document.getElementById('popup');
var closer = document.getElementById('popup-closer');
var Lat2Direc, Lon2Direc;
(Number.isInteger(markerLat)) ? Lat2Direc = markerLat.toString()+'.00' : Lat2Direc = markerLat.toString();
(Number.isInteger(markerLon)) ? Lon2Direc = markerLon.toString()+'.00' : Lon2Direc = markerLon.toString();
popup_href_directions.href = '{$this->OS->urlDirections}' + Lat2Direc + ',' + Lon2Direc;
var overlay = new ol.Overlay({ element: container, autoPan: true, position: ol.proj.fromLonLat([markerLon, markerLat]),offset: [-80,-30],autoPanAnimation: { duration: 250}});
closer.onclick = function(){overlay.setPosition(undefined); closer.blur(); return false;};
var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({ source: new ol.source.OSM()})],overlays: [overlay], target: 'os_map', view: new ol.View({center: ol.proj.fromLonLat([centerLon, centerLat]), zoom: myZoom})});
map.on('singleclick', function () {overlay.setPosition(ol.proj.fromLonLat([markerLon, markerLat]));});
</script>
JS;
        $html .= $js;
        return $html;
    }
    
    
    /**
     * API OpenLayer. Case 5:
     * look /plugins/OSMaps/public/expanded/HTMLmarker_popup2.html     for expanded code
     * @return string
     */
    private function HTMLmarker_popup2($db_options, $id = '') {
        if($id === ''){ $id = 'mapDefault'; }
        
        ($db_options['noWheel'] === 'checked') ? $wheel = "interactions: ol.interaction.defaults( {mouseWheelZoom:false} )," : $wheel = '';
        
        if( $db_options[ 'ckPopup' ] === 'checked' ){   /*  display a button    */
            $mapURL = $this->OS->dirURL . 'public/tmp/' . $id . '.html';
            
            $fileStyle = $this->OS->dirURL . 'public/ol.min.css';
            $fileStylePopup = $this->OS->dirURL . 'public/popup.min.css';
            
            $JsURL = $this->OS->dirURL . 'public/ol.min.js';
            $directions_icon_url = $this->OS->dirURL . 'public/Directions_image1.png';
            
            $Map_window = <<<BTN
<!DOCTYPE html>
<html>
    <head>
        <title>{$id} MAP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" id="olmincss-css" href="{$fileStyle}" media="all">
        <link rel="stylesheet" id="popupmincss-css" href="{$fileStylePopup}" type="text/css" media="all">
        <style>.button {background-color:blue;  border:none;  color:white;  padding:15px 32px; text-align:center; text-decoration:none; display:inline-block; font-size:16px; margin:4px 2px; cursor:pointer;} .osmapsMap{height:70vh;}</style>
    </head>
    <body>
        {$this->DisplayButton_CloseMap()}
        <br>
        <script src="{$JsURL}"></script>
        <div id="os_map" class="osmapsMap"></div>
        <div id="popup" class="ol-popup">
            <a href="#" id="popup-closer" class="ol-popup-closer"></a>
            <div class="ol-popup-direc">
                <a id="popup_href_directions" target="_blank"><img alt="Directions Icon" src="{$directions_icon_url}" title="Directions"></a>
            </div>
            <div id="popup_content" class="ol-popup-content">{$db_options['mapopup']}</div>
        </div>
        <script>
            var markerLon = {$db_options['longitude']}, markerLat = {$db_options['latitude']}, myZoom = {$db_options['zoom']};
            var centerLon = markerLon, centerLat = markerLat;
            var container = document.getElementById('popup');
            var closer = document.getElementById('popup-closer');
            var Lat2Direc, Lon2Direc;
            (Number.isInteger(markerLat)) ? Lat2Direc = markerLat.toString()+'.00' : Lat2Direc = markerLat.toString();
            (Number.isInteger(markerLon)) ? Lon2Direc = markerLon.toString()+'.00' : Lon2Direc = markerLon.toString();
            popup_href_directions.href = '{$this->OS->urlDirections}' + Lat2Direc + ',' + Lon2Direc;
            var overlay = new ol.Overlay({ element: container, autoPan: true, position: ol.proj.fromLonLat([markerLon, markerLat]), offset: [-80,-30], autoPanAnimation: { duration: 250}});
            closer.onclick = function(){overlay.setPosition(undefined);closer.blur();return false;};
            var city = new ol.Feature({geometry: new ol.geom.Point(ol.proj.fromLonLat([markerLon, markerLat]))});
            city.setStyle(new ol.style.Style({image: new ol.style.Icon(({src: '{$this->OS->markerURL}',anchor: [0.5, 1]}))}));
            var vectorSource = new ol.source.Vector({features: [city]});
            var vectorLayer = new ol.layer.Vector({source: vectorSource});
            var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({source: new ol.source.OSM()}),vectorLayer],overlays: [overlay],target: 'os_map',view: new ol.View({center: ol.proj.fromLonLat([centerLon,centerLat]),zoom: myZoom})});
            map.on('singleclick', function () {overlay.setPosition(ol.proj.fromLonLat([markerLon, markerLat]));});
        </script>
    </body>
</html>
BTN;
            file_put_contents($this->path_button_map . '/' . $id . '.html', $Map_window);
            return $this->DisplayButton($id, $mapURL);
        }
        
        /* ----------   */
        $html = $this->HTMLpopup_sub($db_options);
        
        $js = <<<JS
<script>
var markerLon = {$db_options['longitude']}, markerLat = {$db_options['latitude']}, myZoom = {$db_options['zoom']};
var centerLon = markerLon, centerLat = markerLat;
var container = document.getElementById('popup');
var closer = document.getElementById('popup-closer');
var Lat2Direc, Lon2Direc;
(Number.isInteger(markerLat)) ? Lat2Direc = markerLat.toString()+'.00' : Lat2Direc = markerLat.toString();
(Number.isInteger(markerLon)) ? Lon2Direc = markerLon.toString()+'.00' : Lon2Direc = markerLon.toString();
popup_href_directions.href = '{$this->OS->urlDirections}' + Lat2Direc + ',' + Lon2Direc;
var overlay = new ol.Overlay({ element: container, autoPan: true, position: ol.proj.fromLonLat([markerLon, markerLat]), offset: [-80,-30], autoPanAnimation: { duration: 250}});
closer.onclick = function(){overlay.setPosition(undefined);closer.blur();return false;};
var city = new ol.Feature({geometry: new ol.geom.Point(ol.proj.fromLonLat([markerLon, markerLat]))});
city.setStyle(new ol.style.Style({image: new ol.style.Icon(({src: '{$this->OS->markerURL}',anchor: [0.5, 1]}))}));
var vectorSource = new ol.source.Vector({features: [city]});
var vectorLayer = new ol.layer.Vector({source: vectorSource});
var map = new ol.Map({{$wheel}layers: [new ol.layer.Tile({source: new ol.source.OSM()}),vectorLayer],overlays: [overlay],target: 'os_map',view: new ol.View({center: ol.proj.fromLonLat([centerLon,centerLat]),zoom: myZoom})});
map.on('singleclick', function () {overlay.setPosition(ol.proj.fromLonLat([markerLon, markerLat]));});
</script>
JS;
        $html .= $js;
        return $html;
    }
    
}
