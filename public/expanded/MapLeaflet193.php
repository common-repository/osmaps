<?php
/* 
 * http://leafletjs.com
 * 
 */


$leafStyleContainer = <<<Leaf
<style id="leafStyleContainer">
    #mapLeaflet, #mapid { height: 350px; width: 50%; }
</style>        
Leaf;
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MapLeaflet193</title>
        <!-- <link rel="stylesheet" href="../leaflet.css"> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <?php echo $leafStyleContainer; ?>
        <script>
            (function lded(){
                console.log('leaf loaded');
            })();
        </script>
    </head>
    <body>
        <!-- <script id='LeafJS' src="../leaflet.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js" integrity="sha512-Dqm3h1Y4qiHUjbhxTuBGQsza0Tfppn53SHlu/uj1f+RT+xfShfe7r6czRf5r2NmllO2aKx+tYJgoxboOkn1Scg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <div id="mapLeaflet"></div>
        
        <br><hr><br>
        
        <div id="mapid"></div>
        
        <script>            
            let mapLeaf_js = L.map( 'mapLeaflet',{
                scrollWheelZoom: false
            })
            .setView([45.425925, 11.859537], 13);    //  definita mappa mapLeaflet
            
            let mymap = L.map('mapid').setView([45.425925, 11.859537], 13);    //  definita mappa Openstreetmap
            let marker = L.marker([45.425925, 11.859537]).addTo(mymap);          //  aggiungi marker
            marker.bindPopup("<b>Hello world!</b><br>I am a popup.").openPopup();   //  aggiunge un popup
            

          L.popup()                                   //  oppure un singolo popup senza marker
            .setLatLng([45.425925, 11.859537])
            .setContent("I am a standalone popup.")
            .openOn(mapLeaf_js);  
                        
            
    /*aggiunte tessere mapbox */
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/satellite-v9',  //mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'your Mapbox token'
}).addTo(mapLeaf_js);


/*     /*  aggiunte tessere OpenstreetMap */
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mymap);

(function HiddenTipPopup(){
    try{
        var id = 'mapid', pElem = '#' + id + ' ' + '.leaflet-popup-tip', tip = document.querySelector(pElem);
        tip.style.setProperty('visibility', 'hidden');
    }
    catch(e){
        console.warn(e);
    }
})();


        </script>
        

    </body>
</html>
