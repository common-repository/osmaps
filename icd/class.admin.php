<?php
namespace osmaps;

if ( ! defined( 'ABSPATH' ) ) {	exit; }

trait OSMaps_admin{

    static function osmaps_register_setting(){
        global $OSM;
        register_setting( $OSM->name, $OSM->name, [ 'osmaps\OSMaps_admin', 'Validate_form' ] );
    }
    
    
    static function add_submenu_page() {
        add_options_page( 'OSMaps', 'OSMaps', 'manage_options', plugin_basename(__FILE__), [ 'osmaps\OSMaps_admin', 'display_admin_page' ] );
    }
    
    
    static function load_admin_external() {
        global $OSM;        $deps = [];
        
        wp_register_style( 'OSMadminStyle', $OSM->dirURL . 'public/osmAdmin.css', $deps, null );
        wp_enqueue_style( 'OSMadminStyle' );
    }
    
     
    static function display_admin_page(){
        global $OSM;    global $osmaps_db_options;
        
        $all_options = json_encode($osmaps_db_options, JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_APOS);
        
    /*  form action url    */
        $url = admin_url( 'options.php' );

    /*  translations */
        $DET = new \stdClass();
        $DET->lon = \wp_kses_post(__( '<span>- Longitude</span>. A positive (East) or negative (West) floating point number. Ex: 10.36581', 'osmapsWP' ));
        $DET->lat = \wp_kses_post(__( '<span>- Latitude</span>. A positive (Nord) or negative (South) floating point number. Ex: 45.3980', 'osmapsWP' ));
        $DET->hCo = \wp_kses_post(__( '<span>- Help</span>. <a href="https://osmaps.edilweb.eu/maps_findCoordinates/" target="_blank"> Help for coordinates </a>', 'osmapsWP' ));
        $DET->zoo = \wp_kses_post(__( '<span>- Zoom</span>. A positive integer number. Ex: 16 ', 'osmapsWP' ));
        $DET->pop = \wp_kses_post(__( '<span>- PopUp</span>. Your message. In plain text or text/html format.<br>  &nbsp;Caution. Your html tags could take an existing style from the theme.', 'osmapsWP' ));
        $DET->hei = \wp_kses_post(__( '<span>- Height</span>. Ex: 400px', 'osmapsWP' ));
        $DET->max = \wp_kses_post(__( '<span>- Max-width</span>. Optional. An empty setting will make the image full width.  Ex: 360px to display an image with a width of 360px', 'osmapsWP' ));
        $DET->whe = \wp_kses_post(__( '<span>- Wheel</span>. Disable the mouse wheel. Default disable. Good to see the map on mobiles.', 'osmapsWP' ));
        $DET->api = \wp_kses_post(__( '<span>- API</span>. Choose your API javascript for a single map. Leaflet are lighter.', 'osmapsWP' ));
        $DET->til = \wp_kses_post(__( '<span>- Source Tiles</span>. Choose your Source Map Tiles. Mapbox require a key. <a href="https://account.mapbox.com/access-tokens/" target="_blank"> Link to get a key </a>', 'osmapsWP' ));
        $DET->btn = \wp_kses_post(__( '<span>- New Window. </span>Displays a button labeled: <strong>View Map</strong>. Clicking a full screen new window popup displays the required map. Width and height already set are ignored.<br>&nbsp;The button is wrapped in a container div with class <strong>"OSMdivButton"</strong>. You can use WP\'s css editor to have a custom button style.<br>&nbsp;There is no limit to the number of buttons on a web page.', 'osmapsWP' ));
        
        $lon  = esc_html__( 'Longitude', 'osmapsWP' );               $lonT = esc_html__( 'Longitude float value', 'osmapsWP' );
        $lat  = esc_html__( 'Latitude', 'osmapsWP' );                $latT = esc_html__( 'Latitude float value', 'osmapsWP' );
        $zomT = esc_html__('integer value', 'osmapsWP');
        
        $mty = esc_html__( 'Map type', 'osmapsWP' );
        $OP = new \stdClass();
        $OP->s  = esc_html__( 'Simple', 'osmapsWP' );
        $OP->m  = esc_html__( 'with Marker', 'osmapsWP' );
        $OP->p  = esc_html__( 'with PopUp', 'osmapsWP' );
        $OP->mp = esc_html__( 'with Marker and PopUp', 'osmapsWP' );
        
        $txt = \wp_kses_post(__( 'Your<br>popup text', 'osmapsWP' ));       $txh = esc_html__( 'HTML allowed', 'osmapsWP' );
        
        $hgt = esc_html__( 'Height', 'osmapsWP' );                   $hgtT = esc_html__( 'Height. Ex: 330px', 'osmapsWP' );
        $max = esc_html__( 'Max-width', 'osmapsWP' );                $maxT = esc_html__( 'Max-width. Ex: 400px', 'osmapsWP' );
        
        $wdi = esc_html__( 'Disable wheel', 'osmapsWP' );            $wdiT = esc_html__( 'Disable wheel by default', 'osmapsWP' );
        
        $un4 = esc_html__( 'Type or paste your key', 'osmapsWP' );
        $un41= esc_html__( 'Warning. Missing key for Mapbox tiles.', 'osmapsWP' );
        $un42= esc_html__( 'Warning. Tiles Mapbox run with API Leaflet!', 'osmapsWP' );
        $un5 = esc_html__( 'error #5: unknown error in SELECT', 'osmapsWP' );
        $un6 = esc_html__( 'error #6: unknown error in SELECT', 'osmapsWP' );
        $un7 = esc_html__( 'No empty Map!', 'osmapsWP' );
        $un8 = esc_html__( 'Double map name Error!', 'osmapsWP' );
        $un9 = esc_html__( 'Delete map -', 'osmapsWP' );            $un91 = esc_html__( '- Are you sure?', 'osmapsWP' );
        $un92= esc_html__( 'Reserved name.', 'osmapsWP' );
        
        
        $MP = new \stdClass();
        $MP->a   = esc_html__( 'All Maps', 'osmapsWP' );
        $MP->s   = esc_html__( 'Selected map', 'osmapsWP' );
        $MP->t   = esc_html__( 'Source Tiles Key', 'osmapsWP' );
        $MP->v   = esc_html__( 'Maps available', 'osmapsWP' );
        $MP->n   = esc_html__( 'Add New Map', 'osmapsWP' );
        $MP->nm  = esc_html__( 'Name Map', 'osmapsWP' );
        $MP->nt  = esc_html__( 'Your new map. Es: MyNewMap', 'osmapsWP' );
        $MP->ba  = esc_html__( 'Add new Map', 'osmapsWP' );
        $MP->ym  = esc_html__( 'Map name', 'osmapsWP' );
        $MP->adw = esc_html__( 'Click Save Changes for a definitive registration.', 'osmapsWP' );
        $MP->api = esc_html__( 'API javascript', 'osmapsWP' );
        $MP->til = esc_html__( 'Source Map Tiles', 'osmapsWP' );
        $MP->sTl = esc_html__( 'Source', 'osmapsWP' );
        $MP->tk  = esc_html__( 'Source Tiles and Key', 'osmapsWP' );
        $MP->ts  = esc_html__( 'Add / Modify keys for your source tiles', 'osmapsWP' );
        $MP->nk  = esc_html__( 'do not require a key', 'osmapsWP' );
        $MP->rk  = esc_html__( 'require a key.', 'osmapsWP' );
        $MP->mkl = esc_html__( 'Link to get a key', 'osmapsWP' );
        $MP->mst = esc_html__( 'Mapbox creates satellite maps.', 'osmapsWP' );
        $MP->new = esc_html__( 'Map on New Window', 'osmapsWP' );
        $MP->yes = esc_html__( 'New Window on / off', 'osmapsWP');
        $MP->tit = esc_html__( 'Turn on / off the map displayed in a new window', 'osmapsWP' );
        

    /*
     * look /plugins/OSMaps/public/expanded/OSMaps_setting.php
     * for this expanded code
     */
        $a = <<<STG
<div class="wrap">
<h1>OSMaps Settings <span class="spanVers">&nbsp; ver.&nbsp;{$osmaps_db_options['version']}</span><span class="spanVers">&nbsp;{$DET->hCo}</span></h1>
                
<div class="option-page-wrap">
<h2 class="nav-tab-wrapper">
<a class="nav-tab" id="wpt_allMaps_tab" href="#wpt-allMaps">{$MP->a}</a>
<a class="nav-tab" id="wpt_selMap_tab" href="#wpt-selMap">{$MP->s}</a>
<a class="nav-tab" id="wpt_sourceKey_tab" href="#wpt-sourceKey">{$MP->t}</a>
</h2> 
            
<form id="useless_form" action=""></form>

<form action="{$url}" method="post" id="osmaps_form">
STG;
        echo $a;
    
    /* Output nonce, action, and option_page fields for my settings page.    */
        settings_fields( $OSM->name );
    
        $aa = <<<STG
<div class="tab-content-wrapper">
<input id="Ser2Bro" name="{$OSM->name}" type="hidden" value='{$all_options}'>
<input id="ShortDefault" type="hidden" value="{$OSM->shortcode}">

<section id="wpt_allMaps" class="tab-content">
<h3 class="wp-heading-inline">{$MP->v}</h3>
<details id="OSMaddMap" class="OSMadminDetail">
<summary>{$MP->n}</summary>
<p>
<label class="newMapLabel" for="new_map">{$MP->nm}</label>
<input type="text" id="new_map" title="{$MP->nt}" form="useless_form" pattern="^[a-zA-Z0-9_]*$">
<input type="submit" form="useless_form" value="{$MP->ba}">
</p>
</details>
<div id="add_del_warning" class="osmMsgGreen">
<p><strong>{$MP->adw}</strong></p>
</div>
<table class="wp-list-table widefat">
<thead>
<tr>
<td id="cb" class="manage-column">
<label class="screen-reader-text">Seleziona</label>
</td>
<th scope="col" id="name" class="manage-column column-name">{$MP->ym}</th>
<th scope="col" id="delete" class="manage-column column-delete">Delete</th>
<th scope="col" id="shCode" class="manage-column column-shortcode">ShortCode</th>
</tr>
</thead>
<tbody id="the-list">
<tr>
<th scope="row" class="check-column">
<label class="screen-reader-text">Seleziona una mappa</label>
<input type="checkbox" id="mapDefault" data-id="mapDefault" checked onclick="OSMx.clickCheckbox(this);">
</th>
<td class="map column-map">
<strong>mapDefault</strong>
</td>
<td class="mapDelete column-mapDelete">
<strong> --- </strong>
</td>
<td class="mapShCode column-mapShCode">
<strong>[{$OSM->shortcode}]</strong>
</td>
</tr>
</tbody>
</table>
</section>

<section id="wpt_selMap" class="tab-content">
<h3 class="wp-heading-inline">Selected map:<span id="osm_selected"></span></h3>
<details class="OSMadminDetail">
<p> {$DET->lon}</p>
<p> {$DET->lat}</p>
<p> {$DET->hCo}</p>
<p> {$DET->zoo}</p>
<p> {$DET->pop}</p>
<p> {$DET->hei}</p>
<p> {$DET->max}</p>
<p> {$DET->whe}</p>
<p> {$DET->api}</p>
<p> {$DET->til}</p>
<p> {$DET->btn}</p>
<hr>
</details>
<table class="form-table">
<tbody>
<tr>
<th scope="row">
<label class="OSrequi" for="longi_tude">{$lon} </label>
</th>
<td>
<input type="text" id="longi_tude" class="groupElementschange" pattern="^(?:[1-9]\d*|0)?(?:\.\d+)?$" title="{$lonT}" value="" required>
</td>
</tr>
<tr>
<th scope="row">
<label class="OSrequi" for="lati_tude">{$lat} </label>
</th>
<td>
<input type="text" id="lati_tude" class="groupElementschange" pattern="^(?:[1-9]\d*|0)?(?:\.\d+)?$" title="{$latT}" value="" required>
</td>
</tr>
<tr>
<th scope="row">
<label class="OSrequi" for="zo_om">Zoom </label>
</th>
<td>
<input type="number" id="zo_om" class="groupElementschange" title="{$zomT}" min="1" value="" required>
</td>
</tr>
<tr>
<th scope="row">
<label for="mp_type">{$mty} </label>
</th>
<td>
<select id="mp_type" class="groupElementschange">
<option value="1" >{$OP->s}</option> 
<option value="2">{$OP->m}</option>
<option value="3" >{$OP->p}</option>
<option value="5" >{$OP->mp} </option>
</select>
</td>
</tr>
<tr id="hid_tr">
<th scope="row">
<label for="map_popup">{$txt} </label>
</th>
<td>
<textarea  id="map_popup" class="mappaPopup groupElementschange"></textarea><br><i>{$txh}</i>
</td>
</tr>
<tr><th scope="row"><i>CSS</i></th><td>&nbsp;</td></tr>
<tr>
<th scope="row">
<label class="OSrequi" for="al_tezza">{$hgt} </label>
</th>
<td>
<input type="text" id="al_tezza" class="groupElementschange" pattern="^\d{1,4}(px)$" title="{$hgtT}"  value="" required>
</td>
</tr>
<tr>
<th scope="row">
<label for="max_wtd">{$max} </label>
</th>
<td>
<input type="text" id="max_wtd" class="groupElementschange" pattern="^\d{1,4}(px)$" title="{$maxT}" value="">
</td>
</tr>
<tr>
<th scope="row">
<p><label for="no_wheel">{$wdi}</label></p>
</th>
<td>
<input type="checkbox" id="no_wheel" class="groupElementschange" title="{$wdiT}" >
</td>
</tr>
<tr><th scope="row"><i>API JS</i></th><td>&nbsp;</td></tr>
<tr>
<th scope="row">
<p><label for="idApiJs">{$MP->api}</label></p>
</th>
<td>
<select id="idApiJs" class="groupElementschange">
<option value="oplj">OpenLayer</option>
<option value="leaf">Leaflet</option>
</select>
</td>
</tr>
<tr><th scope="row"><i>{$MP->til}</i></th><td>&nbsp;</td></tr>
<tr>
<th scope="row">
<p><label for="sourceTiles">{$MP->sTl}</label></p>
</th>
<td>
<select id="sourceTiles" class="groupElementschange">
<option value="OpenStreet">OpenStreetMap</option>
<option value="mapb">Mapbox</option>
</select>
</td>
</tr>
<tr><th scope="row"><i>{$MP->new} </i></th><td>&nbsp;</td></tr>
<th scope="row">
<p><label for="ck_Popup">{$MP->yes}</label></p>
</th>
<td>
<input type="checkbox"  id="ck_Popup" class="groupElementschange" title="{$MP->tit}"> 
</td>
</tbody>
</table>
</section>
<section id="wpt_sourceKey" class="tab-content">
<h3 class="wp-heading-inline">{$MP->tk}</h3>
<details class="OSMadminDetail">
    <summary>{$MP->ts}</summary>
    <p><span>OpenStreetMap</span> {$MP->nk}</p>
    <p><span>Mapbox</span> {$MP->rk} <a href="https://account.mapbox.com/access-tokens/" target="_blank"> {$MP->mkl} </a><br>{$MP->mst}</p>
</details>
<br>
<table class="wp-list-table widefat">
    <thead>
        <tr>
        <td id="cb" class="manage-column">
            <label class="screen-reader-text">Seleziona</label>
        </td>
        <th scope="col" id="tiles_name" class="manage-column column-name">Source</th>
        <th scope="col" id="tiles_key" class="manage-column column-key">Key / Token</th>
        <th scope="col" id="tiles_input" class="manage-column column-modify">Modify</th>
        </tr>
    </thead>
    <tbody id="SourceTiles-list">
        <tr>
            <th scope="row" class="check-column">
                <label class="screen-reader-text">Select source</label>
                <input type="checkbox" id="TilesOSM" data-id="TilesDefault">
            </th>
            <td class="TilesName column-TilesName">
                <strong>OpenStreetMap</strong>
            </td>
            <td class="TilesKey column-TilesKey">
                <strong><span id="TilesOSM_key"> </span></strong>
            </td>
            <td class="TilesModify column-TilesModify">
                <strong> --- </strong>
            </td>
        </tr>
        <tr>
            <th scope="row" class="check-column">
                <label class="screen-reader-text">Select source</label>
                <input type="checkbox" id="TilesMapbox" data-id="TilesMapbox">
            </th> 
            <td class="TilesName column-TilesName">
                <strong>Mapbox</strong>
            </td>
            <td class="TilesKey column-TilesKey">
                <small><span id="TilesMapbox_key"> </span></small>
            </td>
            <td id="mpBoxCell" class="TilesModify column-TilesModify">
                <button type="button" id="btMapbox_tok">add / modify</button>&nbsp
            </td>
        </tr>
    </tbody>
</table>
</section>
</div>
<p>
STG;
        echo $aa;

    /*  Echoes a submit button  */
        submit_button();
                
        $aaa = <<<STG
</p>
</form>
</div>
                
<script>
(function(){var a = mp_type.options[mp_type.selectedIndex].value;switch(a){case '1':case '2':map_popup.disabled = true;hid_tr.style.visibility = 'hidden';break;
case '3':case '4':case '5':map_popup.disabled = false;hid_tr.style.visibility = 'visible';break;default:alert('{$un5}');break;}})();
var passiveSupported = false;
try {var options={get passive(){passiveSupported = true;}};window.addEventListener("test",options,options);window.removeEventListener("test",options,options);}catch(err){passiveSupported=false;}
mp_type.addEventListener('change',function(){var a = this.options[ this.selectedIndex ].value;switch(a){case '1':case '2':map_popup.disabled = true;hid_tr.style.visibility = 'hidden';break;case '3':case '4':case '5':map_popup.disabled = false;hid_tr.style.visibility = 'visible';break;default:alert('{$un6}');break;}},passiveSupported?{passive:true}:false);
</script>

<script>
function OSMsettingsJS(){
    var AllSettings = {}; var ActiveMap; var EditedMap;

    function RemoveMapboxInput(){ try { MapboxToken.parentNode.removeChild(MapboxToken); } catch(err){ return;}}

    this.edited = function(){return EditedMap = true;};

    this.displayAll = function(){if( EditedMap ){SaveOptionsChanged();} RemoveMapboxInput(); wpt_sourceKey.style.display = 'none'; wpt_selMap.style.display = 'none'; wpt_allMaps.style.display = 'block';};

    this.displayTil = function(){ wpt_sourceKey.style.display = 'block'; wpt_selMap.style.display = 'none'; wpt_allMaps.style.display = 'none'; ValueSelected(); PopulateKeyTiles(); var genit = mpBoxCell; var iput = document.createElement('input'); iput.type = 'text'; iput.id = 'MapboxToken'; iput.placeholder = '{$un4}'; genit.appendChild(iput);};

    function PopulateKeyTiles(){ TilesOSM_key.innerHTML = AllSettings['sTiles'].openstreet; TilesMapbox_key.innerHTML = AllSettings['sTiles'].mapbox;}

    function SaveOptionsChanged(){ AllSettings[ActiveMap].longitude = longi_tude.value; AllSettings[ActiveMap].latitude = lati_tude.value; AllSettings[ActiveMap].zoom = zo_om.value; AllSettings[ActiveMap].mptype = mp_type.options[mp_type.selectedIndex].value; AllSettings[ActiveMap].mapopup = map_popup.value; AllSettings[ActiveMap].height = al_tezza.value; AllSettings[ActiveMap].maxwtd = max_wtd.value; AllSettings[ActiveMap].noWheel = no_wheel.checked; AllSettings[ActiveMap].ApiJs = idApiJs.options[idApiJs.selectedIndex].value; AllSettings[ActiveMap].tiles = sourceTiles.options[sourceTiles.selectedIndex].value; AllSettings[ActiveMap].ckPopup = ck_Popup.checked; EditedMap = false; }

    this.displaySel = function(){ wpt_allMaps.style.display = 'none'; wpt_selMap.style.display = 'block'; RemoveMapboxInput(); wpt_sourceKey.style.display = 'none'; ValueSelected();};

    this.ParseSettings = function(){ AllSettings = JSON.parse(Ser2Bro.value); ActiveMap = 'mapDefault'; var MapsKey = Object.keys(AllSettings); 
        MapsKey.forEach(function(item){ if(item === 'version') {return;} if(item === 'sTiles') {return;} if(item === 'mapDefault') {return;} 
        var genit = document.getElementById('the-list'), riga = document.createElement('tr'); riga.id = 'r_' + item; 
        riga.innerHTML='<th scope="row" class="check-column">'+'<label class="screen-reader-text">Seleziona una mappa</label>'+'<input type="checkbox" id="' + item + '" data-id="'+ item +'" onclick="OSMx.clickCheckbox(this);">'+'</th>'+'<td class="mapName column-mapName">'+'<strong>' + item + '</strong>'+'</td>'+'<td class="mapDelete column-mapDelete">'+'<strong><a id="a_' + item + '" href="" onclick="OSMx.deleteMap(this);">Delete</a></strong>'+'</td>'+'<td class="mapShCode column-mapShCode">'+'<strong>['+ ShortDefault.value + ' ' + 'id=' + '"'+ item + '"' + ']</strong>'+'</td>';
        genit.appendChild(riga);}); wpt_sourceKey.style.display = 'none'; wpt_selMap.style.display = 'none'; wpt_allMaps.style.display = 'block'; };

    this.MySubmitForm = function(){ if( EditedMap === true){ AllSettings[ActiveMap].longitude = longi_tude.value; AllSettings[ActiveMap].latitude = lati_tude.value; AllSettings[ActiveMap].zoom = zo_om.value; AllSettings[ActiveMap].mptype = mp_type.options[mp_type.selectedIndex].value; AllSettings[ActiveMap].mapopup = map_popup.value; AllSettings[ActiveMap].height = al_tezza.value; AllSettings[ActiveMap].maxwtd = max_wtd.value; AllSettings[ActiveMap].noWheel = no_wheel.checked;} AllSettings[ActiveMap].ApiJs = idApiJs.options[idApiJs.selectedIndex].value; AllSettings[ActiveMap].tiles = sourceTiles.options[sourceTiles.selectedIndex].value; AllSettings[ActiveMap].ckPopup = ck_Popup.checked; Ser2Bro.value = JSON.stringify(AllSettings); };

    this.AddNewMap = function(event){ event.preventDefault(); if(new_map.value === ''){ alert('{$un7}'); return;} 
        if(new_map.value === 'sTiles'){ alert('{$un92}'); return;}
        var MapsKey = Object.keys(AllSettings); 
        var nameError; MapsKey.forEach(function(item){ if(item === new_map.value){nameError = 1;alert('{$un8}');}}); if(nameError === 1) {return;}
        newMap={longitude: 0, latitude: 0, zoom: 18, mptype: 1, mapopup: '', height: '350px', maxwtd: '', noWheel: 'checked', ApiJs: 'oplj', tiles: 'OpenStreet', ckPopup: '' }; AllSettings[new_map.value] = newMap;
        var genit = document.getElementById('the-list'), riga = document.createElement('tr'); riga.id = 'r_' + new_map.value;
        riga.innerHTML = '<th scope="row" class="check-column">'+'<label class="screen-reader-text">Seleziona una mappa</label>'+'<input type="checkbox" id="' + new_map.value + '" data-id="'+ new_map.value +'" onclick="OSMx.clickCheckbox(this);">'+'</th>'+'<td class="mapName column-mapName">'+'<strong>' + new_map.value + '</strong>'+'</td>'+'<td class="mapDelete column-mapDelete">'+'<strong><a id="a_' + new_map.value + '" href="" onclick="OSMx.deleteMap(this);">Delete</a></strong>'+'</td>'+'<td class="mapShCode column-mapShCode">'+'<strong>['+ ShortDefault.value + ' ' + 'id=' + '"'+ new_map.value + '"' + ']</strong>'+'</td>';genit.appendChild(riga);
        var cBox = document.querySelectorAll('[data-id]');cBox.forEach(function(item){if(item.id === new_map.value){item.checked = true; ActiveMap = item.id; ValueSelected()}else{item.checked = false;}});
        new_map.value = ''; OSMaddMap.removeAttribute("open"); add_del_warning.style = "visibility:visible;";};

    this.clickCheckbox = function(box){ if(box.id === ActiveMap){ event.preventDefault(); }else{document.getElementById(ActiveMap).checked = false; ActiveMap = box.id; box.checked = true; ValueSelected();}};

    this.deleteMap = function(box){event.preventDefault();let mapToDelete = box.id.substring(2);var dlt = confirm('{$un9} '+ mapToDelete + ' {$un91}');if(dlt === false){return;}
        document.getElementById(ActiveMap).checked = false; delete AllSettings[mapToDelete]; document.getElementById('r_' + mapToDelete).remove(); ActiveMap = 'mapDefault'; document.getElementById(ActiveMap).checked = true; ValueSelected(); add_del_warning.style = "visibility:visible;";};

    function ValueSelected(){osm_selected.innerHTML = ActiveMap; longi_tude.value = AllSettings[ActiveMap].longitude; lati_tude.value = AllSettings[ActiveMap].latitude; zo_om.value = AllSettings[ActiveMap].zoom; for(var i=0; i < mp_type.options.length; i++){ if(mp_type.options[i].value === AllSettings[ActiveMap].mptype.toString()) { mp_type.options[i].selected = true; mapTypeTextarea(mp_type);}} map_popup.value = AllSettings[ActiveMap].mapopup; al_tezza.value = AllSettings[ActiveMap].height; max_wtd.value = AllSettings[ActiveMap].maxwtd; no_wheel.checked = AllSettings[ActiveMap].noWheel; for(var m=0; m < idApiJs.options.length; m++){if(idApiJs.options[m].value === AllSettings[ActiveMap].ApiJs){idApiJs.options[m].selected = true; }}
        for(var n=0; n < sourceTiles.options.length; n++){ if(sourceTiles.options[n].value === AllSettings[ActiveMap].tiles) { sourceTiles.options[n].selected = true; }} ck_Popup.checked = AllSettings[ActiveMap].ckPopup;}

    function mapTypeTextarea(cheType){var a = cheType.options[cheType.selectedIndex].value;switch (a){case '1':case '2':map_popup.disabled = true;hid_tr.style.visibility = 'hidden';break;case '3':case '4':case '5':map_popup.disabled = false;hid_tr.style.visibility = 'visible';break;default:alert( 'error #6: unknown error in popup' );break;}}
    
    this.ModifyMapboxToken = function(){ AllSettings['sTiles'].mapbox = MapboxToken.value; TilesMapbox_key.innerHTML = AllSettings['sTiles'].mapbox; MapboxToken.value = ''; };

    this.MissingKeyMapbox = function(){ if(sourceTiles.value === 'mapb'){ if( '' === AllSettings['sTiles'].mapbox.trim()){ alert('{$un41}'); sourceTiles.value = 'OpenStreet'; } } };
            
    this.ModifyMapboxToken = function(){ AllSettings['sTiles'].mapbox = MapboxToken.value; TilesMapbox_key.innerHTML = AllSettings['sTiles'].mapbox; MapboxToken.value = ''; };

    this.InvalidTiles = function(){ if(sourceTiles.value === 'mapb'){ if(idApiJs.value !== 'leaf'){alert('{$un42}'); idApiJs.value = 'leaf'; } } };
}
    
var OSMx = new OSMsettingsJS;
document.addEventListener('DOMContentLoaded', ()=>{
    OSMx.ParseSettings();

    wpt_allMaps_tab.addEventListener('click', function(){ OSMx.displayAll(); });

    wpt_selMap_tab.addEventListener('click', function(){ OSMx.displaySel(); });
        
    wpt_sourceKey_tab.addEventListener('click', function(){ OSMx.displayTil();});

    osmaps_form.addEventListener('submit', function(){ OSMx.MySubmitForm();});

    useless_form.addEventListener('submit', function(event){ OSMx.AddNewMap(event);});
        
    btMapbox_tok.addEventListener('click', function(){ OSMx.ModifyMapboxToken();});
        
    document.querySelectorAll('.groupElementschange').forEach(item => {
        if(item.id === 'sourceTiles'){ item.addEventListener('change', ()=> { OSMx.MissingKeyMapbox();});}
            
        if(item.id === 'idApiJs'){ item.addEventListener('change', ()=> { OSMx.InvalidTiles();});}
    
        if(item.id === 'sourceTiles'){item.addEventListener('change', ()=> {OSMx.InvalidTiles();});}
            
        item.addEventListener('change', ()=> { OSMx.edited();}); 
    });
        
});
</script>
</div>
STG;
        echo $aaa;
    }
    
    /**
     * initial table values
     * table: wp_options
     * option_name: osmapsWP
     * fields: osmaps version; tiles; one default map (Venezia, Italy)
     */
    static function Db_initial_values() {
         return [
             'version'=>'2.3.8',
             'sTiles' => [ 'openstreet' => '',                              /*  contains the key  for tiles */
                           'mapbox'=> ''
                         ],
             'mapDefault' => ['longitude' => 12.338843,                        /*   float   */
                            'latitude' => 45.434302,                           /*  float   */
                            'zoom'    => 18,                                      /*  integer */
                            'mptype'  => 5,                                         /*  integer */
                            'mapopup' => 'San Marco Square <br>Venezia - Italy',   /*  text/html string  */
                            'height'  => '350px',                                   /*  string  */
                            'maxwtd'  => '',                                    /*  string  */
                            'noWheel' => 'checked',                      /*  default no wheel */
                            'ApiJs'   => 'oplj',                         /*  default OpenLayers => oplj, leaflet => 'leaf' */
                            'tiles'   => 'OpenStreet',                   /*  OpenStreet, mapbox, ecc. ecc. */
                            'ckPopup' => ''                             /*   button with new window = ''; Default no button with new window map  */
                         ]
             ];
    }
    

    static function Validate_form( $inputs ) {

        global $OSM;
        
        $decJSON = json_decode($inputs, true );
        
        $upValues = [];             /* do not modify $decJSON  into foreach  */

        foreach ( $decJSON as $key => $value ) {
            $valid = [];
            
            if( $key === 'version' ) {$upValues[$key] = $value; continue; }
            
            if( $key === 'sTiles' ) {
                $valid['openstreet'] = '';
                $valid['mapbox'] = sanitize_text_field( $value['mapbox']);
                $upValues[$key] = $valid;
                continue;
            }

            $valid['longitude'] = floatval( sanitize_text_field( $value['longitude'] ));
            $valid['latitude']  = floatval( sanitize_text_field( $value['latitude'] ));
            $valid['zoom']      = intval( sanitize_text_field( $value['zoom'] ));
            $valid['mptype']    = intval( sanitize_text_field( $value['mptype'] ));
            
            /*  popup visible or hidden */
            if(( $valid['mptype'] < 1 ) || ( $valid['mptype'] > 5 )) {
                add_settings_error( $OSM->adminErr, $OSM->adminErr, esc_html__( 'OSMaps error #2: OSMaps_admin::Validate_form', 'osmapsWP' ) );
                $valid['mptype'] = 3;
            }
            else {
                if(( 1 === $valid['mptype'] ) || ( 2 === $valid['mptype'] )){
                    $valid['mapopup'] = '';
                }
                else {
                    /*  sanitize and balance tags    */
                    $valid['mapopup'] = balanceTags( wp_kses_post( $value['mapopup'] ), true );
                }
            }


            $valid['height'] = sanitize_text_field( $value['height'] );    /* height   */
            if( !preg_match( '/^\d{1,4}(px)$/', $valid['height'] )){
                add_settings_error( $OSM->adminErr, $OSM->adminErr, esc_html__( 'OSMaps error #3: Height format example: 350px', 'osmapsWP' ));
                $valid['height']   = '350px';
            }


            $valid['maxwtd'] = sanitize_text_field( $value['maxwtd'] );    /* width    */
            if( !empty($valid['maxwtd'] )){
                if( !preg_match( '/^\d{1,4}(px)$/', $valid['maxwtd'] )){
                    add_settings_error( $OSM->adminErr, $OSM->adminErr, esc_html__( 'OSMaps error #4: Max - Width format example: 500px', 'osmapsWP' ));
                    $valid['maxwtd'] = '';
                }
            }

            /* disable wheel    */
           ( isset( $value['noWheel'] ) && ($value['noWheel'] == true))  ? $valid['noWheel'] = 'checked' : $valid['noWheel'] = '';
           
           /*
            * API JS
            * since 2.1.1  december 2020
            */
            $valid['ApiJs'] = sanitize_text_field($value['ApiJs']);
            
            $MapsApi = [ 'oplj', 'leaf' ];
            if( !in_array($valid['ApiJs'], $MapsApi)){
                add_settings_error(
                    $OSM->adminErr, 
                    $OSM->adminErr, 
                    esc_html__( "OSMaps error #7: unknown API Javascript {$valid['ApiJs']}. The default value is assumed.", 'osmapsWP' )
                );
                
                $valid['ApiJs'] = 'oplj';
            }
            
            /* tiles */
            /* Only OpenStreet, mapbox,  at the moment */
            $valid['tiles'] = sanitize_text_field($value['tiles']);
            
            /*
             *  Map display on new window
             *  since 2.3.0 June 2021
             */
            ( isset( $value['ckPopup'] ) && ( $value['ckPopup'] == true) )  ? $valid['ckPopup'] = 'checked' : $valid['ckPopup'] = '';

            $upValues[$key] = $valid;
        }
        
        unset($decJSON);
        return $upValues;
    }
    
    /**
     * Used by filter "plugin_action_links_". Add a administrative setting link for Osmaps
     * @param type $links
     * @return array
     */
    static function add_link_settings( $links ){
        $adminURL = admin_url( 'options-general.php?page=' . plugin_basename(__FILE__));
        
        $mylinks = [ '<a href="' . $adminURL . '">' . esc_html__( 'Settings', 'osmapsWP' ) . '</a>' ];
        
        return array_merge( $links, $mylinks );
    }
}
