

function loadJS(FILE_URL, async = true,functionToInvoke ) {
    let scriptEle = document.createElement("script");
  
    scriptEle.setAttribute("src", FILE_URL);
    scriptEle.setAttribute("type", "text/javascript");
    scriptEle.setAttribute("async", async);
  
    document.body.appendChild(scriptEle);
  
   // success event 
    scriptEle.addEventListener("load", () => {
    //   console.log("File loaded")
        if(functionToInvoke == "loadnewMapdata_prov_noMuniLayer")
            loadnewMapdata_prov_noMuniLayer();
        else if(functionToInvoke == "loadnewMapdata_prov")
            loadnewMapdata_prov();
        else if(functionToInvoke == "loadnewMapdata_reg_noMuniLayer")
            loadnewMapdata_reg_noMuniLayer();
        else if(functionToInvoke == "loadnewMapdata_city")
            loadnewMapdata_city();
    });
     // error event
    scriptEle.addEventListener("error", (ev) => {
      console.log("Error on loading file", ev);
    });
}

var leaflet_map;

function createLeafletMap(leaflet_map_position_x, leaflet_map_position_y){

    // Check if the map exists and is initialized
    if (leaflet_map !== undefined && leaflet_map !== null) {
        leaflet_map.remove();  // Remove the existing map instance from the container
    }

    var width = window.screen.width;
    var lat, lon;

    var locType = $('#selectLocation option:selected').data('loc-type');

    // Check if leaflet_map_position_x and leaflet_map_position_y are provided
    if (leaflet_map_position_x && leaflet_map_position_y) {
        lat = leaflet_map_position_x;
        lon = leaflet_map_position_y;

        if(locType == 1){
            zoom = 8;
        } else {
            zoom = 9; 
        }
    } else {
        lat = 12.788929;
        lon = 121.938415;

        var zoom = 6;
    }

    leaflet_map = L.map('leafletmap', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10, // max zoom level
        minZoom: 6, // min zoom level
    }).setView([lat , lon], zoom); // default LAT AND LAN: 12.788929, 121.938415

    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', { // no design
    // // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { // mejo dark
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    // // L.tileLayer('https://tile.openstreetmap.de/{z}/{x}/{y}.png', {
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>', // no design
    //     // attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community', // mejo dark
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    //     maxZoom: 18,
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map);
}

// For location
function createLeafletMapForLocation(leaflet_map_position_x, leaflet_map_position_y, zoom = null){

    // Check if the map exists and is initialized
    if (leaflet_map !== undefined && leaflet_map !== null) {
        leaflet_map.remove();  // Remove the existing map instance from the container
    }

    var z = 7;

    var lat = parseInt(leaflet_map_position_x, 10);
    var lon = parseInt(leaflet_map_position_y, 10);
    var zoom = parseInt(zoom, 10);

    if (zoom){
        z = zoom;
        lat = lat + .5; // top and bottom
        lon = lon + .5; // left and right
    } 

    leaflet_map = L.map('leafletmap', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], z);
    
    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    //     maxZoom: 19,
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // subdomains: 'abcd',
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map);
}

function createNoDataLeafletMap(){

    // Check if the map exists and is initialized
    if (leaflet_map !== undefined && leaflet_map !== null) {
        leaflet_map.remove();  // Remove the existing map instance from the container
    }
    
    var lat = 12.788929;
    var lon = 121.938415;
    var zoom = 6;

    leaflet_map = L.map('leafletmap', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], zoom);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map);
}

var leaflet_map1;

function createLeafletMap1(leaflet_map_position_x, leaflet_map_position_y){

    // Check if the map exists and is initialized
    if (leaflet_map1 !== undefined && leaflet_map1 !== null) {
        leaflet_map1.remove();  // Remove the existing map instance from the container
    }

    var lat, lon;

    // Check if leaflet_map_position_x and leaflet_map_position_y are provided
    if (leaflet_map_position_x && leaflet_map_position_y) {
        lat = leaflet_map_position_x;
        lon = leaflet_map_position_y;

        if(locType == 1){
            zoom = 8;
        } else {
            zoom = 9; 
        }
    } else {
        lat = 12.788929;
        lon = 121.938415;

        var zoom = 6;
    }

    leaflet_map1 = L.map('leafletmap1', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat , lon], zoom); // default LAT AND LAN: 12.788929, 121.938415

    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', { // no design
    // // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { // mejo dark
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    // // L.tileLayer('https://tile.openstreetmap.de/{z}/{x}/{y}.png', {
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>', // no design
    //     // attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community', // mejo dark
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    //     maxZoom: 18,
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map1);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map1);
}

// For location
function createLeafletMapForLocation1(leaflet_map_position_x, leaflet_map_position_y, zoom = null){

    // Check if the map exists and is initialized
    if (leaflet_map1 !== undefined && leaflet_map1 !== null) {
        leaflet_map1.remove();  // Remove the existing map instance from the container
    }

    var z = 7;

    var lat = parseInt(leaflet_map_position_x, 10);
    var lon = parseInt(leaflet_map_position_y, 10);
    var zoom = parseInt(zoom, 10);

    if (zoom){
        z = zoom;
        lat = lat + .5; // top and bottom
        lon = lon + .5; // left and right
    } 

    leaflet_map1 = L.map('leafletmap1', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], z);
    
    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    //     maxZoom: 19,
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // subdomains: 'abcd',
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map1);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map1);
}

function createNoDataLeafletMap1(){

    // Check if the map exists and is initialized
    if (leaflet_map1 !== undefined && leaflet_map1 !== null) {
        leaflet_map1.remove();  // Remove the existing map instance from the container
    }
    
    var lat = 12.788929;
    var lon = 121.938415;
    var zoom = 6;

    leaflet_map1 = L.map('leafletmap1', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], zoom);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map1);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map1);
}

// //////////////////// FOR LEAFLET MAP 2

var leaflet_map2;

function createLeafletMap2(leaflet_map_position_x, leaflet_map_position_y){

    // Check if the map exists and is initialized
    if (leaflet_map2 !== undefined && leaflet_map2 !== null) {
        leaflet_map2.remove();  // Remove the existing map instance from the container
    }

    var lat, lon;

    // Check if leaflet_map_position_x and leaflet_map_position_y are provided
    if (leaflet_map_position_x && leaflet_map_position_y) {
        lat = leaflet_map_position_x;
        lon = leaflet_map_position_y;

        if(locType == 1){
            zoom = 8;
        } else {
            zoom = 9; 
        }
    } else {
        lat = 12.788929;
        lon = 121.938415;

        var zoom = 6;
    }

    leaflet_map2 = L.map('leafletmap2', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat , lon], zoom); // default LAT AND LAN: 12.788929, 121.938415

    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', { // no design
    // // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { // mejo dark
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    // // L.tileLayer('https://tile.openstreetmap.de/{z}/{x}/{y}.png', {
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>', // no design
    //     // attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community', // mejo dark
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    //     maxZoom: 18,
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map2);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map2);
}

// For location
function createLeafletMapForLocation2(leaflet_map_position_x, leaflet_map_position_y, zoom = null){

    // Check if the map exists and is initialized
    if (leaflet_map2 !== undefined && leaflet_map2 !== null) {
        leaflet_map2.remove();  // Remove the existing map instance from the container
    }

    var z = 8;

    var lat = parseInt(leaflet_map_position_x, 10);
    var lon = parseInt(leaflet_map_position_y, 10);
    var zoom = parseInt(zoom, 10);

    if (zoom){
        z = zoom;
        lat = lat + .5; // top and bottom
        lon = lon + .5; // left and right
    } 

    leaflet_map2 = L.map('leafletmap2', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], z);
    
    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    //     maxZoom: 19,
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // subdomains: 'abcd',
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map2);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map2);
}

function createNoDataLeafletMap2(){

    // Check if the map exists and is initialized
    if (leaflet_map2 !== undefined && leaflet_map2 !== null) {
        leaflet_map2.remove();  // Remove the existing map instance from the container
    }
    
    var lat = 12.788929;
    var lon = 121.938415;
    var zoom = 6;

    leaflet_map2 = L.map('leafletmap2', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], zoom);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map2);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map2);
}


////////////////////////////// FOR LEAFLET MAP 3
var leaflet_map3;

function createLeafletMap3(leaflet_map_position_x, leaflet_map_position_y){

    // Check if the map exists and is initialized
    if (leaflet_map3 !== undefined && leaflet_map3 !== null) {
        leaflet_map3.remove();  // Remove the existing map instance from the container
    }

    var lat, lon;

    // Check if leaflet_map_position_x and leaflet_map_position_y are provided
    if (leaflet_map_position_x && leaflet_map_position_y) {
        lat = leaflet_map_position_x;
        lon = leaflet_map_position_y;

        if(locType == 1){
            zoom = 8;
        } else {
            zoom = 9; 
        }
    } else {
        lat = 12.788929;
        lon = 121.938415;

        var zoom = 6;
    }

    leaflet_map3 = L.map('leafletmap3', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat , lon], zoom); // default LAT AND LAN: 12.788929, 121.938415

    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', { // no design
    // // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { // mejo dark
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    // // L.tileLayer('https://tile.openstreetmap.de/{z}/{x}/{y}.png', {
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>', // no design
    //     // attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community', // mejo dark
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    //     maxZoom: 18,
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map3);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map3);
}

// For location
function createLeafletMapForLocation3(leaflet_map_position_x, leaflet_map_position_y, zoom = null){

    // Check if the map exists and is initialized
    if (leaflet_map3 !== undefined && leaflet_map3 !== null) {
        leaflet_map3.remove();  // Remove the existing map instance from the container
    }

    var z = 7;

    var lat = parseInt(leaflet_map_position_x, 10);
    var lon = parseInt(leaflet_map_position_y, 10);
    var zoom = parseInt(zoom, 10);

    if (zoom){
        z = zoom;
        lat = lat + .5; // top and bottom
        lon = lon + .5; // left and right
    } 

    leaflet_map3 = L.map('leafletmap1', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], z);
    
    // // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { // ???
    //     maxZoom: 19,
    //     // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
    //     // subdomains: 'abcd',
    // }).addTo(leaflet_map);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map3);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map3);
}

function createNoDataLeafletMap3(){

    // Check if the map exists and is initialized
    if (leaflet_map3 !== undefined && leaflet_map3 !== null) {
        leaflet_map3.remove();  // Remove the existing map instance from the container
    }
    
    var lat = 12.788929;
    var lon = 121.938415;
    var zoom = 6;

    leaflet_map3 = L.map('leafletmap1', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], zoom);

    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map3);

    // // backup
    // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //     maxZoom: 16,
    //     attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
    // }).addTo(leaflet_map3);
}

function putResetZoomBtn(geoJSON_current, leaflet_map, leaflet_map_position_x = null, leaflet_map_position_y = null) {
    var width = window.screen.width;
    var selectedLocation = $('#selectLocation').val();
    var locType = $('#selectLocation option:selected').data('loc-type');

    let lat = leaflet_map_position_x || 12.788929; // Default latitude
    let lon = leaflet_map_position_y || 121.938415; // Default longitude
    let zoom = 6; // Default zoom level

    if (selectedLocation) {
        if (locType == 1) {
            zoom = 8;
        } else {
            zoom = 9;
        }
    } else {
        // Use the default values for lat, lon, and zoom
        lat = 12.788929;
        lon = 121.938415;
        zoom = 6;
    }

    (function() {
        var control = new L.Control({
            position: 'topright'
        });
        control.onAdd = function(map) {
            var azoom = L.DomUtil.create('a', 'resetzoom');
            // <a class='btn btn-white tableb text-black' role='button'><i class='bi bi-arrow-clockwise'></i></a>
            azoom.innerHTML = `
                                <button id='resetZoomBtn' class='btn border text-white' 
                                    type='button' aria-haspopup='true' aria-expanded='false' 
                                    data-html2canvas-ignore='true' 
                                    style='margin-top: 18px !important; margin-right: -2px !important; font-size: small; background-color: #FE7F00; border-radius: 30%;'
                                    title='Reset Zoom'> <!-- Set hover information -->
                                    <i class='fa fa-refresh' aria-hidden='true'></i>
                                </button>`;
            
            L.DomEvent
                .disableClickPropagation(azoom)
                .addListener(azoom, 'click', function() {
                    //map.setView([lat, lon], zoom);
                    if (selectedLocation) {
                        let current_bounds = geoJSON_current.getBounds();
                        map.fitBounds(current_bounds);
                        map.setMaxBounds(current_bounds);
                    } else {
                        map.setMaxBounds(null); // Reset max bounds if no location is selected
                    }
                }, azoom);
            return azoom;
        };
        return control;
    }()).addTo(leaflet_map);
}

function putResetZoomBtn1(geoJSON_current, leaflet_map1, leaflet_map_position_x = null, leaflet_map_position_y = null) {
    var width = window.screen.width;
    var selectedLocation = $('#selectLocation').val();
    var locType = $('#selectLocation option:selected').data('loc-type');

    let lat = leaflet_map_position_x || 12.788929; // Default latitude
    let lon = leaflet_map_position_y || 121.938415; // Default longitude
    let zoom = 6; // Default zoom level

    if (selectedLocation) {
        if (locType == 1) {
            zoom = 8;
        } else {
            zoom = 9;
        }
    } else {
        // Use the default values for lat, lon, and zoom
        lat = 12.788929;
        lon = 121.938415;
        zoom = 6;
    }

    (function() {
        var control = new L.Control({
            position: 'topright'
        });
        control.onAdd = function(map) {
            var azoom = L.DomUtil.create('a', 'resetzoom');
            // <a class='btn btn-white tableb text-black' role='button'><i class='bi bi-arrow-clockwise'></i></a>
            azoom.innerHTML = `
                                <button id='resetZoomBtn' class='btn border text-white' 
                                    type='button' aria-haspopup='true' aria-expanded='false' 
                                    data-html2canvas-ignore='true' 
                                    style='margin-top: 18px !important; margin-right: -2px !important; font-size: small; background-color: #FE7F00; border-radius: 30%;'
                                    title='Reset Zoom'> <!-- Set hover information -->
                                    <i class='fa fa-refresh' aria-hidden='true'></i>
                                </button>`;
            
            L.DomEvent
                .disableClickPropagation(azoom)
                .addListener(azoom, 'click', function() {
                    //map.setView([lat, lon], zoom);
                    if (selectedLocation) {
                        let current_bounds = geoJSON_current.getBounds();
                        map.fitBounds(current_bounds);
                        map.setMaxBounds(current_bounds);
                    } else {
                        map.setMaxBounds(null); // Reset max bounds if no location is selected
                    }
                }, azoom);
            return azoom;
        };
        return control;
    }()).addTo(leaflet_map1);
}

function putResetZoomBtn2(geoJSON_current, leaflet_map2, leaflet_map_position_x = null, leaflet_map_position_y = null) {
    var width = window.screen.width;
    var selectedLocation = $('#selectLocation').val();
    var locType = $('#selectLocation option:selected').data('loc-type');

    var lat, lon, zoom;

    if (selectedLocation){
        lat = leaflet_map_position_x;
        lon = leaflet_map_position_y; 

        if(locType == 1){
            zoom = 8;
        } else {
            zoom = 9; 
        }
        
    } else { 
        
        lat = 12.788929;
        lon = 121.938415;

        var zoom = 6;
    }

    (function() {
        var control = new L.Control({
            position: 'topright'
        });
        control.onAdd = function(map) {
            var azoom = L.DomUtil.create('a', 'resetzoom');
            // <a class='btn btn-white tableb text-black' role='button'><i class='bi bi-arrow-clockwise'></i></a>
            azoom.innerHTML = `
                                <button id='resetZoomBtn' class='btn border text-white' 
                                    type='button' aria-haspopup='true' aria-expanded='false' 
                                    data-html2canvas-ignore='true' 
                                    style='margin-top: 18px !important; margin-right: -2px !important; font-size: small; background-color: #FE7F00; border-radius: 30%;'
                                    title='Reset Zoom'> <!-- Set hover information -->
                                    <i class='fa fa-refresh' aria-hidden='true'></i>
                                </button>`;
            
            L.DomEvent
                .disableClickPropagation(azoom)
                .addListener(azoom, 'click', function() {
                    //map.setView([lat, lon], zoom);
                    if (selectedLocation) {
                        let current_bounds = geoJSON_current.getBounds();
                        map.fitBounds(current_bounds);
                        map.setMaxBounds(current_bounds);
                    } else {
                        map.setMaxBounds(null); // Reset max bounds if no location is selected
                    }
                }, azoom);
            return azoom;
        };
        return control;
    }()).addTo(leaflet_map2);
}

function putResetZoomBtn3(geoJSON_current, leaflet_map3, leaflet_map_position_x = null, leaflet_map_position_y = null) {
    var width = window.screen.width;
    var selectedLocation = $('#selectLocation').val();
    var locType = $('#selectLocation option:selected').data('loc-type');

    var lat, lon, zoom;

    if (selectedLocation){
        lat = leaflet_map_position_x;
        lon = leaflet_map_position_y; 

        if(locType == 1){
            zoom = 8;
        } else {
            zoom = 9; 
        }
        
    } else { 
        
        lat = 12.788929;
        lon = 121.938415;

        var zoom = 6;
    }

    (function() {
        var control = new L.Control({
            position: 'topright'
        });
        control.onAdd = function(map) {
            var azoom = L.DomUtil.create('a', 'resetzoom');
            // <a class='btn btn-white tableb text-black' role='button'><i class='bi bi-arrow-clockwise'></i></a>
            azoom.innerHTML = `
                                <button id='resetZoomBtn' class='btn border text-white' 
                                    type='button' aria-haspopup='true' aria-expanded='false' 
                                    data-html2canvas-ignore='true' 
                                    style='margin-top: 18px !important; margin-right: -2px !important; font-size: small; background-color: #FE7F00; border-radius: 30%;'
                                    title='Reset Zoom'> <!-- Set hover information -->
                                    <i class='fa fa-refresh' aria-hidden='true'></i>
                                </button>`;
            
            L.DomEvent
                .disableClickPropagation(azoom)
                .addListener(azoom, 'click', function() {
                    //map.setView([lat, lon], zoom);
                    if (selectedLocation) {
                        let current_bounds = geoJSON_current.getBounds();
                        map.fitBounds(current_bounds);
                        map.setMaxBounds(current_bounds);
                    } else {
                        map.setMaxBounds(null); // Reset max bounds if no location is selected
                    }
                }, azoom);
            return azoom;
        };
        return control;
    }()).addTo(leaflet_map3);
}

// function putResetZoomBtn(geoJSON_current, leaflet_map, leaflet_map_position_x = null, leaflet_map_position_y = null) {
//     var width = window.screen.width;
//     var selectedLocation = $('#selectLocation').val();

//     var lat, lon, zoom;

//     if (selectedLocation) {
//         lat = parseInt(leaflet_map_position_x, 10);
//         lon = parseInt(leaflet_map_position_y, 10); 
//         zoom = 8;
//     } else { 
//         if (width > 2000) {
//             lat = 12.788;
//             lon = 121.938 - 1.8;
//         } else if (width > 1900) {
//             lat = 12.788;
//             lon = 121.938 - 1.8;
//         } else if (width > 1800) {
//             lat = 12.788 - 1.8;
//             lon = 121.938 - 1.8;
//         } else if (width > 1700) {
//             lat = 12.788;
//             lon = 121.938 - 1.8;
//         } else if (width > 1600) {
//             lat = 12.788;
//             lon = 121.938 - 1.8;
//         } else if (width > 1400) {
//             lat = 12.788;
//             lon = 121.938 - 1.8;
//         } else {
//             lat = 11.788929;
//             lon = 127.338415;
//         }

//         zoom = 6;
//     }

//     var resetButtonControl = new L.Control({
//         position: 'topleft'
//     });

//     resetButtonControl.onAdd = function(map) {
//         var button = L.DomUtil.create('a', 'reset-zoom-btn');
//         button.innerHTML = `
//             <button id='resetZoomBtn' class='btn btn-success border text-white' 
//                 type='button' aria-haspopup='true' aria-expanded='false' 
//                 data-html2canvas-ignore='true' 
//                 style='width: 30px; height: 30px; background-color: #0A9444; border: none; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;'
//                 title='Reset Zoom'>
//                 <i class='fa fa-refresh' aria-hidden='true'></i>
//             </button>`;
        
//         L.DomEvent
//             .disableClickPropagation(button)
//             .addListener(button, 'click', function() {
//                 map.setView([lat, lon], zoom);
//                 if (selectedLocation) {
//                     let current_bounds = geoJSON_current.getBounds();
//                     map.fitBounds(current_bounds);
//                     map.setMaxBounds(current_bounds);
//                 } else {
//                     map.setMaxBounds(null); // Reset max bounds if no location is selected
//                 }
//             }, button);

//         return button;
//     };

//     resetButtonControl.addTo(leaflet_map);
// }

function putInfoBtn(leaflet_map){
    // Define a custom control class
    L.Control.InfoButton = L.Control.extend({
        onAdd: function(map) {
            // Create a button element
            var button = L.DomUtil.create('button', 'info-button side-btn');
            button.innerHTML = '<i class="fa fa-info text-white"></i>'; // Info icon using Font Awesome

            // Set button styles
            button.style.width = '30px';
            button.style.height = '30px';
            button.style.backgroundColor = '#6368c7';
            button.style.border = 'none';
            button.style.borderRadius = '50%'; // Make the button circular
            button.style.cursor = 'pointer';
            button.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
            button.style.display = 'flex';
            button.style.alignItems = 'center';
            button.style.justifyContent = 'center';
            button.style.marginTop = '-30px'; // Add margin-top to the button
            button.style.marginRight = '90px';

            // Set hover information
            button.title = 'Info Button';

            // Prevent map interactions when clicking on the button
            L.DomEvent.disableClickPropagation(button);

            // Handle button click
            button.onclick = function() {
                if ($('#floating-legend').is(':hidden')) {
                    $('#floating-legend').show();
                }
            };

            return button;
        },

        onRemove: function(map) {
            // Nothing to do here
        }
    });

    // Add the custom control to the map
    L.control.infoButton = function(opts) {
        return new L.Control.InfoButton(opts);
    };

    // Add the info button to the map
    L.control.infoButton({ position: 'topright' }).addTo(leaflet_map);
}

function putDownloadBtn(leaflet_map) {
    // Define a custom control class
    L.Control.DownloadButton = L.Control.extend({
        onAdd: function(map) {
            // Create a button element
            var button = L.DomUtil.create('button', 'download-button side-btn');
            button.innerHTML = '<i class="fa fa-download text-white"></i>'; // Download icon using Font Awesome

            // Set button styles
            button.style.width = '30px';
            button.style.height = '30px';
            button.style.backgroundColor = '#c3c73c';
            button.style.border = 'none';
            button.style.borderRadius = '50%'; // Make the button circular
            button.style.cursor = 'pointer';
            button.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
            button.style.display = 'flex';
            button.style.alignItems = 'center';
            button.style.justifyContent = 'center';
            button.style.marginTop = '-30px';
            button.style.marginRight = '50px';

            // Set hover information
            button.title = 'Download Map';

            // Prevent map interactions when clicking on the button
            L.DomEvent.disableClickPropagation(button);

            // Handle button click
            button.onclick = function() {
                let mapContainer = document.getElementById("map-div");

                var titleChart = document.getElementById("legend-title").innerHTML + ".png";

                html2canvas(mapContainer, {
                    scale: 2, // Change this
                    allowTaint: true,
                    useCORS: true
                }).then(function(canvas) {
                    // Create a new canvas to crop the image
                    let croppedCanvas = document.createElement('canvas');
                    let croppedContext = croppedCanvas.getContext('2d');
                    
                    // Define the cropping area
                    let cropWidth = canvas.width; // less 1500 if ph map with no zoom
                    let cropHeight = canvas.height;
                    let marginLeft = 50;
                    
                    // Set the size of the cropped canvas
                    croppedCanvas.width = cropWidth;
                    croppedCanvas.height = cropHeight;

                    // Draw the cropped area onto the new canvas
                    croppedContext.drawImage(canvas, marginLeft, 0, cropWidth, cropHeight, 0, 0, cropWidth - marginLeft, cropHeight);

                    // Save the cropped canvas as an image
                    croppedCanvas.toBlob(function(blob) {
                        saveAs(blob, titleChart);
                    });
                });
            };

            return button;
        },

        onRemove: function(map) {
            // Nothing to do here
        }
    });

    // Add the custom control to the map
    L.control.downloadButton = function(opts) {
        return new L.Control.DownloadButton(opts);
    };

    // Add the download button to the map
    L.control.downloadButton({ position: 'topright' }).addTo(leaflet_map);
}

function putLegendBtn(leaflet_map) {
    // Define a custom control class
    L.Control.DownloadButton = L.Control.extend({
        onAdd: function(map) {
            // Create a button element
            var button = L.DomUtil.create('button', 'legend-button side-btn');
            button.innerHTML = '<i class="fa fa-caret-up text-white"></i>'; // Download icon using Font Awesome

            // Set button styles
            button.style.width = '30px';
            button.style.height = '30px';
            button.style.backgroundColor = '#0a9444';
            button.style.border = 'none';
            button.style.borderRadius = '50%'; // Make the button circular
            button.style.cursor = 'pointer';
            button.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
            button.style.display = 'flex';
            button.style.alignItems = 'center';
            button.style.justifyContent = 'center';
            button.style.marginTop = '15px';

            // Set hover information
            button.title = 'Hide Legend';

            // Prevent map interactions when clicking on the button
            L.DomEvent.disableClickPropagation(button);

            // Handle button click
            button.onclick = function() {
                var legend = document.getElementById('floating-legend');
                var mapDiv = document.getElementById('leafletmap');
                var width = window.screen.width;

                // Check if the floating legend is currently hidden
                if (legend.style.visibility === 'hidden' || legend.style.opacity === '0') {
                    // Show the floating legend and adjust map dimensions
                    legend.style.transition = 'opacity 1.5s ease, visibility 0.5s ease';
                    mapDiv.style.transition = 'margin-top 2.5s ease, height 0.5s ease';
                    
                    legend.style.opacity = '1';
                    legend.style.visibility = 'visible';
                    mapDiv.style.marginTop = '0px'; // Or whatever value it was before hiding

                    if (width > 2000){
                        mapDiv.style.height = '840px'; // Or whatever value it was before resizing
                    } else if (width > 1900){
                        mapDiv.style.height = '680px'; // Or whatever value it was before resizing
                    }

                    // Change the button icon to up caret
                    button.innerHTML = '<i class="fa fa-caret-up text-white"></i>';
                } else {
                    // Hide the floating legend and adjust map dimensions
                    legend.style.transition = 'opacity 1.5s ease, visibility 0.5s ease';
                    mapDiv.style.transition = 'margin-top 2.5s ease, height 0.5s ease';
                    
                    legend.style.opacity = '0';
                    legend.style.visibility = 'hidden';
                    mapDiv.style.marginTop = '0px';
                    mapDiv.style.height = '100vh';

                    // Change the button icon to down caret
                    button.innerHTML = '<i class="fa fa-caret-down text-white"></i>';

                    button.title = 'Show Legend';

                    // Allow the map to adjust to the new size after transition
                    map.invalidateSize();
                   
                }
                
            };

            return button;
        },

        onRemove: function(map) {
            // Nothing to do here
        }
    });

    // Add the custom control to the map
    L.control.downloadButton = function(opts) {
        return new L.Control.DownloadButton(opts);
    };

    // Add the download button to the map
    L.control.downloadButton({ position: 'topright' }).addTo(leaflet_map);
}

function resetMap() {
    if (leaflet_map) {
        leaflet_map.remove(); // Remove the existing map instance
        leaflet_map = null;   // Clear the map variable
    }

    if (leaflet_map1) {
        leaflet_map1.remove(); // Remove the existing map instance
        leaflet_map1 = null;   // Clear the map variable
    }

    if (leaflet_map2) {
        leaflet_map2.remove(); // Remove the existing map instance
        leaflet_map2 = null;   // Clear the map variable
    }
}

function generateArrayScales(array, columnName, n) {
    let min = Math.min(...array.map(obj => parseInt(obj[columnName])));
    let max = Math.max(...array.map(obj => parseInt(obj[columnName])));
    let interval = Math.floor((max - min) / n);
    let scales = [];
  
    for (let i = 0; i < n; i++) {
      let scale = {};
      if (i === 0) {
        scale.min = min.toString();
      } else {
        scale.min = (i * interval + min).toString();
      }
      if (i === n - 1) {
        scale.max = max.toString();
      } else {
        scale.max = ((i + 1) * interval + min - 1).toString();
      }
      scales.push(scale);
    }
  
    return scales;
}

function calculateQuartiles(array, columnName) {
    // Sort the array based on the column values
    let sortedArray = array.map(obj => parseInt(obj[columnName])).sort((a, b) => a - b);
    let n = sortedArray.length;

    // console.log(sortedArray);
    // console.log(n);

    // Helper function to calculate the value for a given quartile position
    function getQuartileValue(position) {
        let pos = (position * (n + 1)) / 4;
        let baseIndex = Math.floor(pos) - 1; // index starts from 0
        let fractionalPart = pos - Math.floor(pos);

        // Interpolate if the position is not an integer
        if (fractionalPart === 0) {
            return sortedArray[baseIndex];
        } else {
            return sortedArray[baseIndex] + fractionalPart * (sortedArray[baseIndex + 1] - sortedArray[baseIndex]);
        }
    }

    // Calculate Q1, Q2 (median), and Q3
    let Q1 = getQuartileValue(1);  // First Quartile (25%)
    let Q2 = getQuartileValue(2);  // Second Quartile (50%, or median)
    let Q3 = getQuartileValue(3);  // Third Quartile (75%)

    return { Q1, Q2, Q3 };
}

function calculateThreePercentilesWithDecimal(array, columnName, decimalPlaces = 2) {
    // Sort the array based on the column values
    let sortedArray = array.map(obj => parseFloat(obj[columnName])).sort((a, b) => a - b);
    let n = sortedArray.length;

    // Helper function to calculate the value for a given percentile position
    function getPercentileValue(percentile) {
        let pos = (percentile * (n + 1)) / 100;
        let baseIndex = Math.floor(pos) - 1; // index starts from 0
        let fractionalPart = pos - Math.floor(pos);

        // Interpolate if the position is not an integer
        if (fractionalPart === 0) {
            return sortedArray[baseIndex].toFixed(decimalPlaces);
        } else {
            return (sortedArray[baseIndex] + fractionalPart * (sortedArray[baseIndex + 1] - sortedArray[baseIndex])).toFixed(decimalPlaces);
        }
    }

    // Calculate the 25th, 50th, and 75th percentiles
    let Q1 = getPercentileValue(27.9);  // 25 Percentile
    let Q2 = getPercentileValue(50);  // 50 Percentile
    let Q3 = getPercentileValue(72.1);  // 75 Percentile

    return { Q1, Q2, Q3 };
}

function calculateThreePercentiles(array, columnName) {
    // Sort the array based on the column values
    let sortedArray = array.map(obj => parseInt(obj[columnName])).sort((a, b) => a - b);
    let n = sortedArray.length;

    // Helper function to calculate the value for a given percentile position
    function getPercentileValue(percentile) {
        let pos = (percentile * (n + 1)) / 100;
        let baseIndex = Math.floor(pos) - 1; // index starts from 0
        let fractionalPart = pos - Math.floor(pos);

        // Interpolate if the position is not an integer
        if (fractionalPart === 0) {
            return sortedArray[baseIndex];
        } else {
            return sortedArray[baseIndex] + fractionalPart * (sortedArray[baseIndex + 1] - sortedArray[baseIndex]);
        }
    }

    // Calculate the 33rd and 66th percentiles
    let Q1 = getPercentileValue(25.62);  // 25.5 Percentile
    let Q2 = getPercentileValue(50);  // 50 Percentile
    let Q3 = getPercentileValue(74.39);  // 74.5 Percentile

    return { Q1, Q2, Q3 };
}


function calculateTwoPercentiles(array, columnName) {
    // Sort the array based on the column values
    let sortedArray = array.map(obj => parseInt(obj[columnName])).sort((a, b) => a - b);
    let n = sortedArray.length;

    // Helper function to calculate the value for a given percentile position
    function getPercentileValue(percentile) {
        let pos = (percentile * (n + 1)) / 100;
        let baseIndex = Math.floor(pos) - 1; // index starts from 0
        let fractionalPart = pos - Math.floor(pos);

        // Interpolate if the position is not an integer
        if (fractionalPart === 0) {
            return sortedArray[baseIndex];
        } else {
            return sortedArray[baseIndex] + fractionalPart * (sortedArray[baseIndex + 1] - sortedArray[baseIndex]);
        }
    }

    // Calculate the 33rd and 66th percentiles
    let Q1 = getPercentileValue(33);  // 33rd Percentile
    let Q2 = getPercentileValue(66);  // 66th Percentile

    return { Q1, Q2 };
}

function quantile(arr, p) {
    // Sort the array
    arr.sort((a, b) => a - b);

    const n = arr.length;
    const h = (n - 1) * p + 1; // Linear interpolation formula
    const index = Math.floor(h) - 1; // Nearest lower index (0-based)
    const frac = h - Math.floor(h);  // Fractional part of index

    if (index < 0) {
        return arr[0]; // If the index is below range, return the minimum
    } else if (index >= n - 1) {
        return arr[n - 1]; // If the index is above range, return the maximum
    } else {
        // Linear interpolation between arr[index] and arr[index + 1]
        return arr[index] + frac * (arr[index + 1] - arr[index]);
    }
}


// Dynamically decide the rounding factor based on the values
function dynamicRound(value, scale = null) {
    let magnitude = Math.pow(10, Math.floor(Math.log10(value)) - scale); // Adjust "-2" to control rounding scale
    return Math.round(value / magnitude) * magnitude;
}

// $('#showFarm_profitkilo').click(function () {
//     $("#leg-prodcost").hide();
//     $("#leg-net").hide();
//     $("#leg-farmgate").show();
    
//     let current_year = $("#current_mapyear").val();
//     $("#chart2-fp-maptitle").html("Dry Palay Price per Region ("+current_year+")");
// });


// $('#showCost_profitkilo').click(function () {
//     $("#leg-prodcost").show();
//     $("#leg-net").hide();
//     $("#leg-farmgate").hide();
    
//     let current_year = $("#current_mapyear").val();
//     $("#chart2-fp-maptitle").html("Production Cost per Region ("+current_year+")");
// });

// $('#showReturns_profitkilo').click(function () {
//     $("#leg-prodcost").hide();
//     $("#leg-net").show();
//     $("#leg-farmgate").hide();
    
//     let current_year = $("#current_mapyear").val();
//     $("#chart2-fp-maptitle").html("Net Return Per Kilo per Region ("+current_year+")");
// });



// function hideYearsTagOfMap(){
//     // $("#map_year15").hide();
//     // $("#map_year16").hide();
//     // $("#map_year17").hide();
//     // $("#map_year18").hide();
//     // $("#map_year19").hide();
//     // $("#map_year20").hide();
//     // $("#map_year22").show();
//     // $("#map_year21").show();
//     $("select#showBarYears_map option[value='1']").hide();
// }

// function showYearsTagOfMap(){
//     // $("#map_year15").show();
//     // $("#map_year16").show();
//     // $("#map_year17").show();
//     // $("#map_year18").show();
//     // $("#map_year19").show();
//     // $("#map_year20").show();
//     // $("#map_year22").show();
//     // $("#map_year21").show();
//     $("select#showBarYears_map option[value='1']").show();
// }


// $(document).ready(function () {

//         $('a.dl-leafletmap').click(function () {

//             let mapContainer = document.getElementById("leafletmap");
//             let renderingArea = document.getElementById("rendering_area");
//             renderingArea.innerHTML = "";
        
//             var extractChart = this.parentElement.parentElement;
//             var titleChart = this.parentElement.querySelector('h5.my-0.pt-2.pb-2').innerHTML + ".png";
        
//             html2canvas(mapContainer, {
//                 allowTaint: true,
//                 useCORS: true
//             }).then(function(canvas) {
//                 renderingArea.appendChild(canvas);
//                 renderingArea.style.display = 'block';
//                 mapContainer.style.display = 'none';
        
//                 html2canvas(extractChart).then(function (canvas) {
//                     saveAs(canvas.toDataURL(), titleChart);
//                 });
                
//                 renderingArea.style.display = 'none';
//                 mapContainer.style.display = 'block';
//             });
//         });
        
// })