var leaflet_map;


function createLeafletMap(){
    leaflet_map = L.map('leafletmap', {
        scrollWheelZoom: false,
        preferCanvas: true
    }).setView([12.788929, 121.938415], 6);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map);
}



function putResetZoomBtn(geoJSON_current, leaflet_map) {
    (function() {
        var control = new L.Control({
            position: 'topright'
        });
        control.onAdd = function(map) {
            var azoom = L.DomUtil.create('a', 'resetzoom');
            // <a class='btn btn-white tableb text-black' role='button'><i class='bi bi-arrow-clockwise'></i></a>
            azoom.innerHTML = " <button class='btn btn-light border' type='button' aria-haspopup='true' aria-expanded='false' data-html2canvas-ignore='true' style='font-size:small'>Reset zoom</button>";
            L.DomEvent
                .disableClickPropagation(azoom)
                .addListener(azoom, 'click', function() {
                    //map.setView(map.options.center, map.options.zoom);
                    let current_bounds = geoJSON_current.getBounds();
                    map.fitBounds(current_bounds);
                    map.setMaxBounds(current_bounds)
                }, azoom);
            return azoom;
        };
        return control;
    }()).addTo(leaflet_map);
}



function resetMap() {
    leaflet_map = leaflet_map.off();
    leaflet_map = leaflet_map.remove();
}

$(document).ready(function () {

    $('a.dl-leafletmap').click(function () {

        let mapContainer = document.getElementById("leafletmap");
        let renderingArea = document.getElementById("rendering_area");
        renderingArea.innerHTML = "";
    
        var extractChart = this.parentElement.parentElement;
        
        var titleChart = this.parentElement.querySelector('h5.my-0.pt-2.pb-2').innerHTML + ".png";
    
        html2canvas(mapContainer, {
            allowTaint: true,
            useCORS: true
        }).then(function(canvas) {
            renderingArea.appendChild(canvas);
            renderingArea.style.display = 'block';
            mapContainer.style.display = 'none';
    
            $('div.scrollable-chart').css('height', 'auto');
            html2canvas(extractChart).then(function (canvas) {
                saveAs(canvas.toDataURL(), titleChart);
            });
            
            $('div.scrollable-chart').css('height', '950px');
            renderingArea.style.display = 'none';
            mapContainer.style.display = 'block';
        });
       
    });

    //if ($('input[name="toggle_type_v2"]:checked').val() == 'Regional') {



    var current_mapdata;

    

    function onEachFeature_new(feature, layer) {
            
        if(current_mapdata[feature.id]){
                var value = current_mapdata[feature.id].Value;
               
                layer.bindTooltip("<strong>"+current_mapdata[feature.id].Location_name+": P"+value+" ("+current_mapdata[feature.id].Year+")</strong>").openTooltip();

                var fill_key = current_mapdata[feature.id].fillKey;
                if(fill_key != ""){
                    layer.on('mouseover', function(e) {
                    // Unset highlight
                        layer.setStyle({
                            'fillColor': '#FFFFFF',
                            'fillOpacity': 0.5
                        });
                    });

                    var color = heatmap_colors[fill_key].color; 
                    layer.on('mouseout', function(e) {
                        // Unset highlight
                        layer.setStyle({
                            'fillColor': color,
                            'fillOpacity': 0.9
                        });
                    });
                }
           }
    }


    function loadnewMapdata(current_mapcoordinates) {
        
       createLeafletMap();

       let geoJSON_current = L.geoJSON(current_mapcoordinates,{
            style: function(feature) {
               
                 if(current_mapdata[feature.id]){
                    var fill_key = current_mapdata[feature.id].fillKey;
                    if(fill_key != "")
                        return {color: "#000000",weight: 1, fill: true, fillColor: heatmap_colors[fill_key].color, fillOpacity: 0.9}; 
                    else
                        return { fill: false, stroke:false};  
                }else{
                    // return {color: "#000000",weight: 1, fill: true, fillColor: heatmap_colors['defaultFill'].color, fillOpacity: 0.9}; 
                    return { fill: false, stroke:false}; 
                }
            },onEachFeature:onEachFeature_new
        }).addTo(leaflet_map);

        let geoJSON_current_bounds = geoJSON_current.getBounds();
        leaflet_map.fitBounds(geoJSON_current_bounds);
        leaflet_map.setMaxBounds(geoJSON_current_bounds);


        putResetZoomBtn(geoJSON_current,leaflet_map);
    }



            
        $('input[type=radio][name=toggle_type_v2]').change(function() {
            
            if ($('input[name="toggle_type_v2"]:checked').val() == 'Regional') {
                $('#ctxRegionUreaContainer').removeClass("scrollable-chart");
                topRegUPChart.aspectRatio = 0.55;
                topRegUPChart.resize();
                
                switch($("#showFert_v2 option:selected").val()) {
                case "1":
                    topRegUPChart.data.labels = regionsUG;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorUG;
                        dataset.borderColor = topProvColorUG;
                        dataset.data = topProvDataUG;
                    });
                    topRegUPChart.update();
                    
                    //mapDataUreaG;
                    current_mapdata = mapDataUreaG;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                   
                    $("#chart2-header").html("Average Retail Price of Urea (Granular) by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleUrea[0]));
                    $("#series-2").html(numberWithCommas(scaleUrea[0]) + "-" + numberWithCommas(scaleUrea[1]));
                    $("#series-3").html(numberWithCommas(scaleUrea[1]) + "-" + numberWithCommas(scaleUrea[2]));
                    $("#series-4").html(numberWithCommas(scaleUrea[2]) + "-" + numberWithCommas(scaleUrea[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleUrea[3]));
                    break;
                case "2":
                    topRegUPChart.data.labels = regionsLabelsCo;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorCo;
                        dataset.borderColor = topProvColorCo;
                        dataset.data = topProvDataCo;
                    });
                    topRegUPChart.update();
                    
                    //mapDataCompl;
                    current_mapdata = mapDataCompl;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Complete by Region");

                    $("#series-1").html("< " + numberWithCommas(scaleComplete[0]));
                    $("#series-2").html(numberWithCommas(scaleComplete[0]) + "-" + numberWithCommas(scaleComplete[1]));
                    $("#series-3").html(numberWithCommas(scaleComplete[1]) + "-" + numberWithCommas(scaleComplete[2]));
                    $("#series-4").html(numberWithCommas(scaleComplete[2]) + "-" + numberWithCommas(scaleComplete[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleComplete[3]));
                    break;
                case "3":
                    topRegUPChart.data.labels = regionsLabelsAs;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorAs;
                        dataset.borderColor = topProvColorAs;
                        dataset.data = topProvDataAs;
                    });
                    topRegUPChart.update();
                    
                    //mapDataAmmos;
                    current_mapdata = mapDataAmmos;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Ammosul by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleAmmosul[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmosul[0]) + "-" + numberWithCommas(scaleAmmosul[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmosul[1]) + "-" + numberWithCommas(scaleAmmosul[2]));
                    $("#series-4").html(numberWithCommas(scaleAmmosul[2]) + "-" + numberWithCommas(scaleAmmosul[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleAmmosul[3]));
                    break;
                case "4":
                    topRegUPChart.data.labels = regionsLabelsAp;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorAp;
                        dataset.borderColor = topProvColorAp;
                        dataset.data = topProvDataAp;
                    });
                    topRegUPChart.update();
                    
                    //mapDataAmmop;
                    current_mapdata = mapDataAmmop;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Ammophos by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleAmmophos[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmophos[0]) + "-" + numberWithCommas(scaleAmmophos[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmophos[1]) + "-" + numberWithCommas(scaleAmmophos[2]));
                    $("#series-4").html(numberWithCommas(scaleAmmophos[2]) + "-" + numberWithCommas(scaleAmmophos[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleAmmophos[3]));
                    break; 
                case "5":
                    topRegUPChart.data.labels = regionsLabelsMop;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorMop;
                        dataset.borderColor = topProvColorMop;
                        dataset.data = topProvDataMop;
                    });
                    topRegUPChart.update();
                    
                    // mapDataMop;
                    current_mapdata = mapDataMop;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of MOP by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleMop[0]));
                    $("#series-2").html(numberWithCommas(scaleMop[0]) + "-" + numberWithCommas(scaleMop[1]));
                    $("#series-3").html(numberWithCommas(scaleMop[1]) + "-" + numberWithCommas(scaleMop[2]));
                    $("#series-4").html(numberWithCommas(scaleMop[2]) + "-" + numberWithCommas(scaleMop[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleMop[3]));
                    break;		
                }
            } else {
                $('#ctxRegionUreaContainer').addClass("scrollable-chart");
                topRegUPChart.aspectRatio = 0.15;
                topRegUPChart.resize();
                
                switch($("#showFert_v2 option:selected").val()) {
                case "1":
                    topRegUPChart.data.labels = provLabelsUG;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorUG;
                        dataset.borderColor = topColorUG;
                        dataset.data = topDataUG;
                    });
                    topRegUPChart.update();
                    
                    
                    current_mapdata = mapDataUreaGP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Urea (Granular) by Province");
                    
                    $("#series-1").html("< " + numberWithCommas(scaleUrea[0]));
                    $("#series-2").html(numberWithCommas(scaleUrea[0]) + "-" + numberWithCommas(scaleUrea[1]));
                    $("#series-3").html(numberWithCommas(scaleUrea[1]) + "-" + numberWithCommas(scaleUrea[2]));
                    $("#series-4").html(numberWithCommas(scaleUrea[2]) + "-" + numberWithCommas(scaleUrea[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleUrea[3]));
                    break;
                case "2":
                    topRegUPChart.data.labels = provLabelsCo;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorCo;
                        dataset.borderColor = topColorCo;
                        dataset.data = topDataCo;
                    });
                    topRegUPChart.update();
                    
                    //mapDataComplP;
                    current_mapdata = mapDataComplP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Complete by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleComplete[0]));
                    $("#series-2").html(numberWithCommas(scaleComplete[0]) + "-" + numberWithCommas(scaleComplete[1]));
                    $("#series-3").html(numberWithCommas(scaleComplete[1]) + "-" + numberWithCommas(scaleComplete[2]));
                    $("#series-4").html(numberWithCommas(scaleComplete[2]) + "-" + numberWithCommas(scaleComplete[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleComplete[3]));
                    break;
                case "3":
                    topRegUPChart.data.labels = provLabelsAs;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorAs;
                        dataset.borderColor = topColorAs;
                        dataset.data = topDataAs;
                    });
                    topRegUPChart.update();

                    //mapDataAmmosP;
                    current_mapdata = mapDataAmmosP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Ammosul by Province");
                    
                    $("#series-1").html("< " + numberWithCommas(scaleAmmosul[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmosul[0]) + "-" + numberWithCommas(scaleAmmosul[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmosul[1]) + "-" + numberWithCommas(scaleAmmosul[2]));
                    $("#series-4").html(numberWithCommas(scaleAmmosul[2]) + "-" + numberWithCommas(scaleAmmosul[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleAmmosul[3]));
                    break;
                case "4":
                    topRegUPChart.data.labels = provLabelsAp;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorAp;
                        dataset.borderColor = topColorAp;
                        dataset.data = topDataAp;
                    });
                    topRegUPChart.update();

                    //mapDataAmmopP;
                    current_mapdata = mapDataAmmopP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Ammophos by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleAmmophos[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmophos[0]) + "-" + numberWithCommas(scaleAmmophos[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmophos[1]) + "-" + numberWithCommas(scaleAmmophos[2]));
                    $("#series-4").html(numberWithCommas(scaleAmmophos[2]) + "-" + numberWithCommas(scaleAmmophos[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleAmmophos[3]));
                        
                    break; 
                case "5":
                    topRegUPChart.data.labels = provLabelsMop;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorMop;
                        dataset.borderColor = topColorMop;
                        dataset.data = topDataMop;
                    });
                    topRegUPChart.update();
                    
                    //mapDataMopP;
                    current_mapdata = mapDataMopP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of MOP by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleMop[0]));
                    $("#series-2").html(numberWithCommas(scaleMop[0]) + "-" + numberWithCommas(scaleMop[1]));
                    $("#series-3").html(numberWithCommas(scaleMop[1]) + "-" + numberWithCommas(scaleMop[2]));
                    $("#series-4").html(numberWithCommas(scaleMop[2]) + "-" + numberWithCommas(scaleMop[3]));
                    $("#series-5").html("> " + numberWithCommas(scaleMop[3]));
                    break;			
                }
            }
        });






        $("#showFert_v2").change(function() {
            
            if ($('input[name="toggle_type_v2"]:checked').val() == 'Regional') {
                $('#ctxRegionUreaContainer').removeClass("scrollable-chart");
                topRegUPChart.aspectRatio = 0.55;
                topRegUPChart.resize();
                
                switch($("#showFert_v2 option:selected").val()) {
                  case "1":
                    topRegUPChart.data.labels = regionsUG;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorUG;
                        dataset.borderColor = topProvColorUG;
                        dataset.data = topProvDataUG;
                    });
                    topRegUPChart.update();
                    
                    //mapDataUreaG;
                    current_mapdata = mapDataUreaG;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Urea (Granular) by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleUrea[0]));
                    $("#series-2").html(numberWithCommas(scaleUrea[0]) + "-" + numberWithCommas(scaleUrea[1]));
                    $("#series-3").html(numberWithCommas(scaleUrea[1]) + "-" + numberWithCommas(scaleUrea[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleUrea[3]));
                    break;
                  case "2":
                    topRegUPChart.data.labels = regionsLabelsCo;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorCo;
                        dataset.borderColor = topProvColorCo;
                        dataset.data = topProvDataCo;
                    });
                    topRegUPChart.update();
                    
                    //mapDataCompl;
                    current_mapdata = mapDataCompl;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Complete by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleComplete[0]));
                    $("#series-2").html(numberWithCommas(scaleComplete[0]) + "-" + numberWithCommas(scaleComplete[1]));
                    $("#series-3").html(numberWithCommas(scaleComplete[1]) + "-" + numberWithCommas(scaleComplete[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleComplete[2]));
                    break;
                  case "3":
                    topRegUPChart.data.labels = regionsLabelsAs;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorAs;
                        dataset.borderColor = topProvColorAs;
                        dataset.data = topProvDataAs;
                    });
                    topRegUPChart.update();
                    
                    //mapDataAmmos;
                    current_mapdata = mapDataAmmos;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Ammosul by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleAmmosul[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmosul[0]) + "-" + numberWithCommas(scaleAmmosul[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmosul[1]) + "-" + numberWithCommas(scaleAmmosul[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleAmmosul[2]));
                    break;
                  case "4":
                    topRegUPChart.data.labels = regionsLabelsAp;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorAp;
                        dataset.borderColor = topProvColorAp;
                        dataset.data = topProvDataAp;
                    });
                    topRegUPChart.update();
                    
                    //mapDataAmmop;
                    current_mapdata = mapDataAmmop;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    $("#chart2-header").html("Average Retail Price of Ammophos by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleAmmophos[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmophos[0]) + "-" + numberWithCommas(scaleAmmophos[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmophos[1]) + "-" + numberWithCommas(scaleAmmophos[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleAmmophos[2]));
                    break; 
                  case "5":
                    topRegUPChart.data.labels = regionsLabelsMop;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topProvColorMop;
                        dataset.borderColor = topProvColorMop;
                        dataset.data = topProvDataMop;
                    });
                    topRegUPChart.update();
                    
                    //mapDataMop;
                    current_mapdata = mapDataMop;
                    resetMap();
                    loadnewMapdata(geojsonFeature_reg);
                    
                    
                    $("#chart2-header").html("Average Retail Price of MOP by Region");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleMop[0]));
                    $("#series-2").html(numberWithCommas(scaleMop[0]) + "-" + numberWithCommas(scaleMop[1]));
                    $("#series-3").html(numberWithCommas(scaleMop[1]) + "-" + numberWithCommas(scaleMop[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleMop[2]));
                    break;		
                }
            } else {
                $('#ctxRegionUreaContainer').addClass("scrollable-chart");
                topRegUPChart.aspectRatio = 0.15;
                topRegUPChart.resize();

                switch($("#showFert_v2 option:selected").val()) {
                  case "1":
                    topRegUPChart.data.labels = provLabelsUG;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorUG;
                        dataset.borderColor = topColorUG;
                        dataset.data = topDataUG;
                    });
                    topRegUPChart.update();

                    //mapDataUreaGP;
                    current_mapdata = mapDataUreaGP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
            
                    $("#chart2-header").html("Average Retail Price of Urea (Granular) by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleUrea[0]));
                    $("#series-2").html(numberWithCommas(scaleUrea[0]) + "-" + numberWithCommas(scaleUrea[1]));
                    $("#series-3").html(numberWithCommas(scaleUrea[1]) + "-" + numberWithCommas(scaleUrea[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleUrea[2]));
                    break;
                  case "2":
                    topRegUPChart.data.labels = provLabelsCo;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorCo;
                        dataset.borderColor = topColorCo;
                        dataset.data = topDataCo;
                    });
                    topRegUPChart.update();
                    
                    //mapDataComplP;
                    //mapDataUreaGP;
                    current_mapdata = mapDataComplP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Complete by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleComplete[0]));
                    $("#series-2").html(numberWithCommas(scaleComplete[0]) + "-" + numberWithCommas(scaleComplete[1]));
                    $("#series-3").html(numberWithCommas(scaleComplete[1]) + "-" + numberWithCommas(scaleComplete[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleComplete[2]));
                    break;
                  case "3":
                    topRegUPChart.data.labels = provLabelsAs;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorAs;
                        dataset.borderColor = topColorAs;
                        dataset.data = topDataAs;
                    });
                    topRegUPChart.update();
                    
                    //mapDataAmmosP;
                    current_mapdata = mapDataAmmosP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Ammosul by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleAmmosul[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmosul[0]) + "-" + numberWithCommas(scaleAmmosul[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmosul[1]) + "-" + numberWithCommas(scaleAmmosul[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleAmmosul[2]));
                    break;
                  case "4":
                    topRegUPChart.data.labels = provLabelsAp;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorAp;
                        dataset.borderColor = topColorAp;
                        dataset.data = topDataAp;
                    });
                    topRegUPChart.update();
                    
                    //mapDataAmmopP;
                    current_mapdata = mapDataAmmopP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    $("#chart2-header").html("Average Retail Price of Ammophos by Province");
                    
                    $("#series-1").html("< " + numberWithCommas(scaleAmmophos[0]));
                    $("#series-2").html(numberWithCommas(scaleAmmophos[0]) + "-" + numberWithCommas(scaleAmmophos[1]));
                    $("#series-3").html(numberWithCommas(scaleAmmophos[1]) + "-" + numberWithCommas(scaleAmmophos[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleAmmophos[2]));
                    break; 
                  case "5":
                    topRegUPChart.data.labels = provLabelsMop;
                    topRegUPChart.data.datasets.forEach((dataset) => {
                        dataset.backgroundColor = topColorMop;
                        dataset.borderColor = topColorMop;
                        dataset.data = topDataMop;
                    });
                    topRegUPChart.update();
                    
                    //mapDataMopP;
                    current_mapdata = mapDataMopP;
                    resetMap();
                    loadnewMapdata(geojsonFeature_prov);
                    
                    
                    $("#chart2-header").html("Average Retail Price of MOP by Province");
                        
                    $("#series-1").html("< " + numberWithCommas(scaleMop[0]));
                    $("#series-2").html(numberWithCommas(scaleMop[0]) + "-" + numberWithCommas(scaleMop[1]));
                    $("#series-3").html(numberWithCommas(scaleMop[1]) + "-" + numberWithCommas(scaleMop[2]));
                    $("#series-4").html("> " + numberWithCommas(scaleMop[2]));
                    break;			
                }
            }
        });


})