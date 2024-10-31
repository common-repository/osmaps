<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Maps Mapbox</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../leaflet.css">

<style id="mapboxStyleContainer">
    #mapMapbox { height: 500px; }
</style>   

    </head>
    <body>
        <script id='LeafJS' src="../leaflet.js"></script>
        <div id="mapMapbox"></div>
        <script>
        document.addEventListener('DOMContentLoaded', ()=>{
            var mapMapBoxjs = L.map( 'mapMapbox',{
                center:[45.293064,12.028763],
                zoom: 17,
                scrollWheelZoom: false
            });
            
            L.marker([45.293064, 12.028763],{
                draggable:true,
                autoPan:true,
                
            })
            .on(
                'click', function(e) {          /*  'dragend' event*/
                    alert(e.latlng); 
                    }
                )
            .addTo(mapMapBoxjs);
    
            
            /* tilesize:512 se presente allora zoomOffset:-1 deve anche essere presente   */
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/satellite-streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'your Mapbox token'
        }).addTo(mapMapBoxjs);
            
            
});
        </script>
    </body>

