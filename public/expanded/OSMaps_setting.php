<?php
/*
 * 
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title>admin html</title>
        <style>
            .OSrequi:after{content: '*'; color: red;} .OSMadminDetail{font-style: italic;} .OSMadminDetail p span{font-weight: bold;}/* #wpt_selMap{display: none;}*/
 .ewMsgGreen{
    border-left: 4px solid #1e2b02;
    padding: 12px;
    margin: auto;
    background-color: #fff;
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
        </style>
    </head>
    <body>
    <div class="wrap">    
        <h1>OSMaps Settings &nbsp; <span class="spanVers"><span>- Help</span>. <a href="https://wpd.edilweb.eu/maps_findCoordinates/" target="_blank"> Help for coordinates </a></span></h1>

        <div class="option-page-wrap">
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab" id="wpt_allMaps_tab" href="#wpt-allMaps">All Maps</a>
                <a class="nav-tab" id="wpt_selMap_tab" href="#wpt-selMap">Selected map</a>
                <a class="nav-tab" id="wpt_sourceKey_tab" href="#wpt-sourceKey">Source Tiles Key</a>
            </h2>

            <form id="useless_form" action=""></form>
            
            <form action="#" method="post" id="osmaps_form">

                     <!--settings_fields( $OSM->name );-->

                <div class="tab-content-wrapper">
                    <input id="Ser2Bro" name="{$OSM->name}" type="hidden" value='27'>
                    <input id="ShortDefault" type="hidden" value="var">

                    <section id="wpt_allMaps" class="tab-content">
                        <h3 class="wp-heading-inline">Mappe disponibili</h3>
                        <details id="OSMaddMap" class="OSMadminDetail">
                          <summary>Add One Map</summary>
                          <p>
                              <label class="newMapLabel" for="new_map">Name Map</label>
                              <input type="text" id="new_map" title="Your new map. Es: MyNewMap" form="useless_form" pattern="^[a-zA-Z0-9_]*$">
                              <input type="submit" form="useless_form" value="Add new Map">
                          </p>
                        </details>                     
                        <div id="add_del_warning" class="osmMsgGreen">
                            <p><strong>Important. Click Save Changes for a definitive registration.</strong></p>
                        </div>
                        <table class="wp-list-table widefat">
                            <thead>
                                <tr>
                                <td id="cb" class="manage-column">
                                    <label class="screen-reader-text">Seleziona</label>
                                </td>
                                <th scope="col" id="name" class="manage-column column-name">Mappa</th>
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
                                    <td class="mapName column-mapName">
                                        <strong>mapDefault</strong>
                                    </td>
                                    <td class="mapDelete column-mapDelete">
                                        <strong> --- </strong>
                                    </td>
                                    <td class="mapShCode column-mapShCode">
                                        <strong>ShortCode</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </section>

                    <section id="wpt_selMap" class="tab-content">
                        <h3 class="wp-heading-inline">Mappa selezionata &nbsp;<span id="osm_selected"></span></h3>
                        <details class="OSMadminDetail">
                            <p> <span>- Longitude</span>. A positive or negative floating point number. Ex. 10.36581 </p>
                            <p> <span>- Latitude</span>. A positive or negative floating point number. Ex. 45.3980 </p>
                            <p> <span>- Help</span>. <a href="https://wpd.edilweb.eu/maps_findCoordinates/" target="_blank"> Help for coordinates </a></p>
                            <p> <span>- Zoom</span>. A positive integer number. Ex. 16</p>
                            <p> <span>- PopUp</span>. Your message. In plain text or text/html format.<br>  &nbsp;Caution. Your html tags could take an existing style from the theme.</p>
                            <p> <span>- Height</span>. Format #px. Ex. 400px </p>
                            <p> <span>- Max-width</span>. Optional. An empty setting will make the image full width. Format #px. Ex. 360px</p>
                            <p> <span>- Wheel</span>. Disable the mouse wheel. Good to see the map on mobiles.
                            <p> <span>- API</span>. Choose your API javascript. Leaflet are lighter.</p>
                            <p> <span>- Source Tiles</span>. Choose your Source Map Tiles. Mapbox require a key. <a href="https://account.mapbox.com/access-tokens/" target="_blank"> Link to get a key </a></p>
                            <p> <span>- Map on Window Popup. </span>Displays a button labeled: <strong>View Map</strong>. Clicking a full screen new window displays the required map. Width and height already set are ignored.<br>The button has a class <strong>OSMbutton</strong> with no css rules set.<br>The button is wrapped in a container div with class <strong>OSMdivButton</strong> without css rules set. You can use WP's css editor to have a custom button style.<br>There is no limit to the number of buttons on a web page. </p>
                        </details> 
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <p><label class="OSrequi" for="longi_tude">Longitude </label></p>
                                    </th>
                                    <td>
                                        <input type="text"  id="longi_tude" class="groupElementschange" pattern="^(?:[1-9]\d*|0)?(?:\.\d+)?$" title="longitude" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <p><label class="OSrequi" for="lati_tude">Latitude </label></p>
                                    </th>
                                    <td>
                                        <input type="text"  id="lati_tude" class="groupElementschange" pattern="^(?:[1-9]\d*|0)?(?:\.\d+)?$" title="latitude" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <p><label class="OSrequi" for="zo_om">Zoom</label></p> 
                                    </th>
                                    <td>
                                        <input type="number"  id="zo_om" class="groupElementschange" title="number" min="1" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <p><label for="mp_type">Map type</label></p>
                                    </th>
                                    <td>
                                        <select id="mp_type" class="groupElementschange">
                                            <option value="1">Simple</option>
                                            <option value="2">with Marker</option>
                                            <option value="3">with PopUp</option>
                                            <option value="5">with Marker and Popup </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="hid_tr">
                                    <th scope="row">
                                        <p><label for="map_popup">Your<br> PopUp</label></p>
                                    </th>
                                    <td>
                                        <textarea  id="map_popup" class="mappaPopup groupElementschange"></textarea>
                                        <br><i>HTML allowed</i>
                                    </td>
                                </tr>
                                <tr><th scope="row">CSS</th><td>&nbsp;</td></tr>
                                <tr>
                                    <th scope="row">
                                        <p><label class="OSrequi" for="al_tezza">Height</label></p>
                                    </th>
                                    <td>
                                        <input type="text" id="al_tezza" class="groupElementschange" pattern="^\d{1,4}(px)$" title="title_alt"  value="350px" required>
                                    </td>               
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <p><label for="max_wtd">Max-width </label>
                                    </th>
                                    <td>
                                        <input type="text"  id="max_wtd" class="groupElementschange" pattern="^\d{1,4}(px)$" title="title_max" value="">                                
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <p><label for="no_wheel">Disable wheel</label></p>
                                    </th>
                                    <td>
                                        <input type="checkbox"  id="no_wheel" class="groupElementschange" title="Disable wheel by default" checked> 
                                    </td>
                                </tr>
                                
                                <tr><th scope="row">API JS</th><td>&nbsp;</td></tr>
                                <tr>
                                    <th scope="row">
                                        <p><label for="idApiJs">Api Javascript</label></p>
                                    </th>
                                    <td>
                                        <select id="idApiJs" class="groupElementschange">
                                            <option value="oplj">OpenLayer</option>
                                            <option value="leaf">Leaflet</option>
                                        </select>
                                    </td>
                                </tr>
                                
                                <tr><th scope="row"><i>Source Map Tiles</i></th><td>&nbsp;</td></tr>
                                
                                <tr>
                                    <th scope="row">
                                        <p><label for="sourceTiles">Source</label></p>
                                    </th>
                                    <td>
                                        <select id="sourceTiles" class="groupElementschange">
                                            <option value="OpenStreet">OpenStreetMap</option>
                                            <option value="mapb">Mapbox</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr><th scope="row"><i>Map on Modal Popup </i></th><td>&nbsp;</td></tr>
                                <th scope="row">
                                    <p><label for="ck_Popup">Popup on / off</label></p>
                                </th>
                                <td>
                                    <input type="checkbox"  id="ck_Popup" class="groupElementschange" title="turn on / off the map displayed in a popup"> 
                                </td>
                            </tbody>
                        </table>
                    </section>
                    
                    <section id="wpt_sourceKey" class="tab-content">
                        <h3 class="wp-heading-inline">Source Tiles and Keys</h3>
                        <details class="OSMadminDetail">
                            <summary>Add / Modify keys for your source tiles</summary>
                            <p>
                                <span>OpenStreetMap</span> do not require a key.
                            </p>
                            <p>
                                <span>Mapbox</span> require a key. <a href="https://account.mapbox.com/access-tokens/" target="_blank"> Link to get a key </a><br>Mapbox creates satellite maps. 
                            </p>
                        </details>
                        <table class="wp-list-table widefat">
                            <thead>
                                <tr>
                                    <td id="cb_tiles" class="manage-column">
                                        <label class="screen-reader-text">Select</label>
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
                                        <input type="checkbox" id="TilesOSM" data-id="TilesDefault" onclick="/*OSMx.clickCheckbox(this);*/">
                                    </th> 
                                    <td class="TilesName column-TilesName">
                                        <strong>OpenStreetMap</strong>
                                    </td>
                                    <td class="TilesKey column-TilesKey">
                                        <strong><span id="TilesOSM_key"> </span>  </strong>
                                    </td>
                                    <td class="TilesModify column-TilesModify">
                                        <strong> --- </strong>
                                    </td>                                    
                                </tr>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <label class="screen-reader-text">Select source</label>
                                        <input type="checkbox" id="TilesMapbox" data-id="TilesMapbox" onclick="/*OSMx.clickCheckbox(this);*/">
                                    </th> 
                                    <td class="TilesName column-TilesName">
                                        <strong>Mapbox</strong>
                                    </td>
                                    <td class="TilesKey column-TilesKey">
                                        <small><span id="TilesMapbox_key"> </span></small>
                                    </td>
                                    <td id="mpBoxCell" class="TilesModify column-TilesModify">
                                        <button type="button" id="btMapbox_tok">add / modify</button>&nbsp;
                                    </td>                                                                        
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>
                <!--submit_button();-->
                <p><input type="submit"></p>
            </form>
        </div>
        <p>&nbsp;</p>
        
        <script>
        (function(){
            var a = mp_type.options[mp_type.selectedIndex].value;
            switch ( a ){
                case '1':
                case '2':
                    map_popup.disabled = true;
                    hid_tr.style.visibility = 'hidden';
                    break;
                case '3':
                case '4':
                case '5':
                    map_popup.disabled = false;
                    hid_tr.style.visibility = 'visible';
                    break;
                default:
                    alert( 'error #5: unknown error in popup' );
                    break;
            }
        })();

        var passiveSupported = false;
        try {
            var options = {
                get passive() { 
                    passiveSupported = true;
                }
            };
            window.addEventListener( "test", options, options );
            window.removeEventListener( "test", options, options );
        } 
        catch( err ) {
            passiveSupported = false;
        }


        mp_type.addEventListener('change',
        function(){
            var a = this.options[this.selectedIndex].value;
            switch ( a ){
                case '1':
                case '2':
                    map_popup.disabled = true;
                    hid_tr.style.visibility = 'hidden';
                    break;
                case '3':
                case '4':
                case '5':
                    map_popup.disabled = false;
                    hid_tr.style.visibility = 'visible';
                    break;
                default:
                    alert( 'error #6: unknown error in popup' );
                    break;
            }
        }, 
        passiveSupported  ? { passive: true } : false );
        </script>

        <script>
        function OSMsettingsJS(){

            var AllSettings = {};       //  version, tiles, all maps
            var ActiveMap;              //  mappa selezionata            
            var EditedMap;              //  becomes true if the user has changed the map options
            
            this.edited = ()=>{ return EditedMap = true; };
            
            /* display first section, all maps */
            this.displayAll = function(){
                if( EditedMap ){SaveOptionsChanged();}
                RemoveMapboxInput();                
                wpt_sourceKey.style.display = 'none';
                wpt_selMap.style.display = 'none';
                wpt_allMaps.style.display = 'block'; 
            };
            
            /* display second section, options for selected map */
            this.displaySel = function(){
                wpt_allMaps.style.display = 'none';
                wpt_selMap.style.display = 'block';
                RemoveMapboxInput();
                wpt_sourceKey.style.display = 'none';
                ValueSelected();
            };                            
            
            /* display third section, source Tiles an keys */
            this.displayTil = function(){
                wpt_sourceKey.style.display = 'block';
                wpt_selMap.style.display = 'none';
                wpt_allMaps.style.display = 'none';
                ValueSelected();                /*  fix error form. Important. */
                PopulateKeyTiles();
                var genit = mpBoxCell;
                var iput = document.createElement('input');
                iput.type = 'text';
                iput.id = 'MapboxToken';
                iput.placeholder = 'Digit or paste your key';
                genit.appendChild(iput);
            };
            
            function RemoveMapboxInput(){
                try{
                    MapboxToken.parentNode.removeChild(MapboxToken);
                }
                catch(err){
                    return;
                }
            }
            
            /* click on displayAll prompts you to save the changed options */ 
            function SaveOptionsChanged(){
                AllSettings[ActiveMap].longitude = longi_tude.value;
                AllSettings[ActiveMap].latitude = lati_tude.value;
                AllSettings[ActiveMap].zoom = zo_om.value;
                
                AllSettings[ActiveMap].mptype = mp_type.options[mp_type.selectedIndex].value;

                AllSettings[ActiveMap].mapopup = map_popup.value;
                AllSettings[ActiveMap].height = al_tezza.value;
                AllSettings[ActiveMap].maxwtd = max_wtd.value;
                AllSettings[ActiveMap].noWheel = no_wheel.checked;
                
                AllSettings[ActiveMap].ApiJs = idApiJs.options[idApiJs.selectedIndex].value;
                
                AllSettings[ActiveMap].tiles = sourceTiles.options[sourceTiles.selectedIndex].value;
                
                AllSettings[ActiveMap].ckPopup = ck_Popup.checked;
                
                EditedMap = false; 
            }

            
            /* Section third. Populate the key fields */
            function PopulateKeyTiles(){
                TilesOSM_key.innerHTML = AllSettings['sTiles'].openstreet;
                TilesMapbox_key.innerHTML = AllSettings['sTiles'].mapbox;
            }
            


            /* sets the active map, the initial default one  */
            /* reads the data of all maps sent by the server */
            /* populates the first section. id="wpt_allMaps" */
            this.ParseSettings = function(){
                /* parse settings   */
                 AllSettings = JSON.parse(Ser2Bro.value);
                 
                /* clicked map  */
                ActiveMap = 'mapDefault';
                
                /* version 2.2.2.  MapsKey = (7) ["version", "sTiles", "mapDefault", "Gastone", "Paris", "Paperoga", "test56beta"] */
                var MapsKey = Object.keys(AllSettings);
                MapsKey.forEach( function(item){
                    if(item === 'version') {return;}
                    if(item === 'sTiles') {return;}
                    if(item === 'mapDefault') {return;}

                    /* new html row */
                    var genit = document.getElementById('the-list'), riga = document.createElement('tr');
                    riga.id = 'r_' + item;
                    riga.innerHTML = 
                        '<th scope="row" class="check-column">'  +
                            '<label class="screen-reader-text">Seleziona una mappa</label>' +
                            '<input type="checkbox" id="' + item + '" data-id="'+ item +'" onclick="OSMx.clickCheckbox(this);">' +
                        '</th>' +
                        '<td class="mapName column-mapName">' +
                            '<strong>' + item + '</strong>'+
                        '</td>'+
                        '<td class="mapDelete column-mapDelete">'+
                            '<strong><a id="a_' + item + '" href="" onclick="OSMx.deleteMap(this);">Delete</a></strong>'+
                        '</td>'+
                        '<td class="mapShCode column-mapShCode">'+
                            '<strong>['+ ShortDefault.value + ' ' + 'id=' + '"'+ item + '"' + ']</strong>'+
                        '</td>';
                    genit.appendChild(riga);
                });
                /* fixes possible cache error after plugin update */
                wpt_sourceKey.style.display = 'none';
                wpt_selMap.style.display = 'none';
                wpt_allMaps.style.display = 'block';  
            };
            
            /* read the last map if edited and prepare the data before sending the form */
            /* id="Ser2Bro" is the only input with a name attribute                        */
            this.MySubmitForm = function(){
                if( EditedMap === true){
                    AllSettings[ActiveMap].longitude = longi_tude.value;
                    AllSettings[ActiveMap].latitude = lati_tude.value;
                    AllSettings[ActiveMap].zoom = zo_om.value;

                    AllSettings[ActiveMap].mptype = mp_type.options[mp_type.selectedIndex].value;

                    AllSettings[ActiveMap].mapopup = map_popup.value;
                    AllSettings[ActiveMap].height = al_tezza.value;
                    AllSettings[ActiveMap].maxwtd = max_wtd.value;
                    AllSettings[ActiveMap].noWheel = no_wheel.checked; 
                    
                    AllSettings[ActiveMap].ApiJs = idApiJs.options[idApiJs.selectedIndex].value;
                    
                    AllSettings[ActiveMap].tiles = sourceTiles.options[sourceTiles.selectedIndex].value;
                    
                    AllSettings[ActiveMap].ckPopup = ck_Popup.checked;
                    /* --- */
                    RemoveMapboxInput();
                }
            /*  prepare string of all settings for server   */
                Ser2Bro.value = JSON.stringify(AllSettings);
            };            
            

            /* add a new map to AllSettings variable            */
            /* sets the initial options of the new map          */
            /* adds a new row to the table in the first section */
            /* select the new map                               */
            this.AddNewMap = function(event){
                event.preventDefault();
                if(new_map.value === ''){
                    alert('No Map!'); 
                    return;
                }
                if(new_map.value === 'sTiles'){
                    alert('Reserved name.'); 
                    return;
                }
                /* double map name error    */
                var MapsKey = Object.keys(AllSettings); 
                var nameError;
                MapsKey.forEach(function(item){
                    if(item === new_map.value){
                        nameError = 1;
                        alert('double map name error !');                            
                    }
                });
                if(nameError === 1) {return;}

                /* new map */
                newMap = {
                    longitude: 0,
                    latitude: 0,
                    zoom: 4,
                    mptype: 1,
                    mapopup: '',
                    height: '350px',
                    maxwtd: '',
                    noWheel: 'checked',
                    ApiJs: 'oplj',           //  default new map
                    tiles: 'OpenStreet',
                    ckPopup: ''             //  defalult no modal popup
                };
                AllSettings[new_map.value] = newMap;                    console.log(Object.keys(AllSettings));

                /* new html row */
                var genit = document.getElementById('the-list'), riga = document.createElement('tr');
                riga.id = 'r_' + new_map.value;
                riga.innerHTML = 
                    '<th scope="row" class="check-column">'  +
                        '<label class="screen-reader-text">Seleziona una mappa</label>' +
                        '<input type="checkbox" id="' + new_map.value + '" data-id="'+ new_map.value +'" onclick="OSMx.clickCheckbox(this);">' +
                    '</th>' +
                    '<td class="mapName column-mapName">' +
                        '<strong>' + new_map.value + '</strong>'+
                    '</td>'+
                    '<td class="mapDelete column-mapDelete">'+
                        '<strong><a id="a_' + new_map.value + '" href="" onclick="OSMx.deleteMap(this);">Delete</a></strong>'+
                    '</td>'+
                    '<td class="mapShCode column-mapShCode">'+
                        '<strong>['+ ShortDefault.value + ' ' + 'id=' + '"'+ new_map.value + '"' + ']</strong>'+
                    '</td>';
                genit.appendChild(riga);

                /* new map checked */
                var cBox = document.querySelectorAll('[data-id]');
                cBox.forEach( function(item){
                    if(item.id === new_map.value){
                        item.checked = true;
                        ActiveMap = item.id;
                        ValueSelected();
                    }
                    else {
                        item.checked = false;
                    }
                });

                /* close detail */
                new_map.value = '';
                OSMaddMap.removeAttribute("open");
                add_del_warning.style = "visibility:visible;";
            };
            

            /* the user selects a map  */
             this.clickCheckbox = function(box){
                if(box.id === ActiveMap){
                    event.preventDefault();
                }
                else {
                    document.getElementById(ActiveMap).checked = false; 
                    ActiveMap = box.id;
                    box.checked = true;
                    ValueSelected();
                }
            };
            
            
            this.deleteMap = function(box){
                event.preventDefault();
                let mapToDelete = box.id.substring(2);
                var dlt = confirm('Delete map '+ mapToDelete + ' Are you sure?');
                if(dlt === false){return;}
                
                document.getElementById(ActiveMap).checked = false;                
                delete AllSettings[mapToDelete];                        console.log(Object.keys(AllSettings));
                document.getElementById('r_' + mapToDelete).style.display = 'none';
                document.getElementById('r_' + mapToDelete).remove();
                ActiveMap = 'mapDefault';
                document.getElementById(ActiveMap).checked = true;
                ValueSelected();
                add_del_warning.style = "visibility:visible;";
            };

            /* display options for selected map */
            function ValueSelected (){
                osm_selected.innerHTML = ActiveMap;

                longi_tude.value = AllSettings[ActiveMap].longitude;
                lati_tude.value = AllSettings[ActiveMap].latitude;
                zo_om.value = AllSettings[ActiveMap].zoom;

                for(var i=0; i < mp_type.options.length; i++){
                    if(mp_type.options[i].value === AllSettings[ActiveMap].mptype.toString()) { 
                        mp_type.options[i].selected = true;
                        mapTypeTextarea(mp_type);
                    }
                }

                map_popup.value = AllSettings[ActiveMap].mapopup;
                al_tezza.value = AllSettings[ActiveMap].height;
                max_wtd.value = AllSettings[ActiveMap].maxwtd;
                no_wheel.checked = AllSettings[ActiveMap].noWheel;
                
                for(var m=0; m < idApiJs.options.length; m++){
                    if(idApiJs.options[m].value === AllSettings[ActiveMap].ApiJs) { 
                        idApiJs.options[m].selected = true;
                    }                    
                }
                
                for(var n=0; n < sourceTiles.options.length; n++){
                    if(sourceTiles.options[n].value === AllSettings[ActiveMap].tiles) { 
                        sourceTiles.options[n].selected = true;
                    }                    
                }
                
                ck_Popup.checked = AllSettings[ActiveMap].ckPopup;
            }

            /* display map popup for input text. Optional */
            function mapTypeTextarea(cheType){
                var a = cheType.options[cheType.selectedIndex].value;
                switch ( a ){
                    case '1':
                    case '2':
                        map_popup.disabled = true;
                        hid_tr.style.visibility = 'hidden';
                        break;
                    case '3':
                    case '4':
                    case '5':
                        map_popup.disabled = false;
                        hid_tr.style.visibility = 'visible';
                        break;
                    default:
                        alert( 'error #6: unknown error in popup' );
                        break;
                }
            }
            
            
            /* check if exists Mapbox Token */
            this.MissingKeyMapbox = function(){
                if(sourceTiles.value === 'mapb'){
                    if( '' === AllSettings['sTiles'].mapbox.trim()){
                        alert('Warning. Missing key for Mapbox tiles.');
                        sourceTiles.value = 'OpenStreet';
                    }
                }
            };            
            
            /* set new key in AllSettings */
            this.ModifyMapboxToken = function(){
                AllSettings['sTiles'].mapbox = MapboxToken.value;
                TilesMapbox_key.innerHTML = AllSettings['sTiles'].mapbox;
                MapboxToken.value = '';
            };

            /* check if Mapbox is select with OpenLayers API    */
            this.InvalidTiles = function(){
                if(sourceTiles.value === 'mapb'){
                    if(idApiJs.value !== 'leaf'){
                        alert('Warning. Tiles Mapbox run with API Leaflet!');
                        idApiJs.value = 'leaf';
                    }
                }
            };
            

        }

var OSMx = new OSMsettingsJS;
        
document.addEventListener('DOMContentLoaded', ()=>{
        OSMx.ParseSettings();

        wpt_allMaps_tab.addEventListener('click', function(){
            OSMx.displayAll();
        });

        wpt_selMap_tab.addEventListener('click', function(){
            OSMx.displaySel();
        });
        
        wpt_sourceKey_tab.addEventListener('click', function(){
            OSMx.displayTil();
        });

        osmaps_form.addEventListener('submit', function(){
            OSMx.MySubmitForm();
        });

        useless_form.addEventListener('submit', function(event){
            OSMx.AddNewMap(event);
        });
        
        btMapbox_tok.addEventListener('click', function(){
            OSMx.ModifyMapboxToken();
        });
        
        document.querySelectorAll('.groupElementschange').forEach(item => {
            if(item.id === 'sourceTiles'){
                item.addEventListener('change', ()=> {
                    OSMx.MissingKeyMapbox();
                });
            }            
            if(item.id === 'idApiJs'){
                item.addEventListener('change', ()=> {
                    OSMx.InvalidTiles();
                });                 
            }
            if(item.id === 'sourceTiles'){
                item.addEventListener('change', ()=> {
                    OSMx.InvalidTiles();
                });                 
            }        
            item.addEventListener('change', ()=> {
                OSMx.edited();
            }); 
        });        
        
  });        
/* document.getElementById("my-element").remove();*/

/* 
* class groupElementschange 
* id="longi_tude"
* id="lati_tude"
* id="zo_om"
* id="mp_type"
* id="map_popup"
* id="al_tezza"
* id="max_wtd"
* id="no_wheel"
* id="idApiJs"
* id="sourceTiles"
* id="ck_Popup"
 */
        </script>
    </div>
    </body>
</html>