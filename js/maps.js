// Global variable to store the layer reference
var mapLayer;

// Update the opacity of the map
function updateRangeAppearance() {
    var rangeInput = document.getElementById('opacity');
    var opacityValue = rangeInput.value;

    // Update the opacity text
    document.getElementById('opacityValue').textContent = 'Map Opacity: ' + opacityValue + '%';

    // Calculate the percentage value
    var percentage = (opacityValue - rangeInput.min) / (rangeInput.max - rangeInput.min) * 100;

    // Update the background gradient of the track
    rangeInput.style.background = `linear-gradient(to right, #C5C9F1 ${percentage}%, #ddd ${percentage}%)`;

    // Update the map layer opacity
    if (mapLayer) {
        var opacity = opacityValue / 100; // Convert percentage to decimal
        mapLayer.eachLayer(function (layer) {
            layer.setStyle({
                fillOpacity: opacity,
                opacity: opacity
            });
        });
    }
}


// Call the function on page load
updateRangeAppearance();

// Add event listener for input changes
document.getElementById('opacity').addEventListener('input', updateRangeAppearance);

///////////////////////////////////// GET CURRENT OPACITY
function getCurrentOpacity() {
    var rangeInput = document.getElementById('opacity');
    var opacityValue = rangeInput.value;

    // Convert the slider value to a decimal opacity value
    var opacity = opacityValue / 100;

    // Return the opacity value
    return opacity;
}

///////////////////////////// Function to reset the slider to its default value
function resetSlider() {
    var defaultValue = 95;
    $('#opacity').val(defaultValue);
    updateRangeAppearance();

    if (typeof mapLayer !== 'undefined' && mapLayer) {
        const newOpacity = getCurrentOpacity();
        mapLayer.setStyle({ fillOpacity: newOpacity });
    }
}

function renderProductionMap(mapType, dbData, locationCoordinatesData, periodText = null) {

    resetSlider();

    // --- Color setup ---
    const heatmapColors = {
        firsQ: { color: '#FF7F00' },
        secoQ: { color: '#FFC107' },
        thirQ: { color: '#4DAF4A' },
        fourQ: { color: '#1F78B4' },
        defaultFill: { color: 'rgba(165,215,224)' }
    };

    // --- Data filtering ---
    const validData = dbData.filter(item => item.value !== null && item.value !== undefined && item.value !== '0');
    if (!validData.length) return console.warn("No valid data to display.");

    let q1, q2, q3;

    if (mapType === 'regional' && periodText === "Annual") {
        q1 = 500000;
        q2 = 1000000;
        q3 = 2000000;
    } else {

        // --- Quartile calculations ---
        const values = validData.map(item => parseFloat(item.value));
        q1 = quantile(values, 0.25);
        q2 = quantile(values, 0.50);
        q3 = quantile(values, 0.75);

        q1 = dynamicRound(q1, 1);
        q2 = dynamicRound(q2, 1);
        q3 = dynamicRound(q3, 1);
    }

    // --- Legend setup ---
    const scales = [
        { minValue: q1 },
        { minValue: q2 },
        { minValue: q3 },
    ];

    const colors = ['#FF7F00', '#FFD92F', '#4DAF4A', '#1F78B4'];
    $('.legend-box').each(function (i) {
        $(this).css('background', colors[i]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scales[0].minValue));
    $('#series-2').html("> " + numberWithCommas(scales[0].minValue) + " to " + numberWithCommas(scales[1].minValue));
    $('#series-3').html("> " + numberWithCommas(scales[1].minValue) + " to " + numberWithCommas(scales[2].minValue));
    $('#series-4').html("> " + numberWithCommas(scales[2].minValue));

    // --- Map data prep ---
    const totalValue = validData.reduce((acc, d) => acc + parseFloat(d.value), 0);
    const mapData = {};

    validData.forEach(item => {
        const val = parseFloat(item.value);
        const perc = (val / totalValue) * 100;
        const fillKey =
            val > q3 ? 'fourQ' :
                val > q2 ? 'thirQ' :
                    val > q1 ? 'secoQ' : 'firsQ';

        mapData[item.map_ID] = {
            Location_name: item.location_name,
            Year: item.year,
            Value: (val < 1 ? val.toFixed(2) : Math.round(val)).toLocaleString(),
            fillKey,
            Percentage: perc.toFixed(2)
        };
    });

    var lat = "";
    var lon = "";
    var zoom = "";

    if (locationCoordinatesData) {
        var coord = locationCoordinatesData;
        var zoom = coord['zoom'];

        var lat = coord['latitude'];
        var lon = coord['longitude'];
    }

    // --- Determine boundary type and geojson source ---
    let geoBoundary, geojsonSource;
    if (mapType === 'regional') {
        geoBoundary = 'nat_reg';
        geojsonSource = geojsonFeature_reg;
    } else if (mapType === 'province') {
        geoBoundary = 'reg_prov';
        geojsonSource = geojsonFeature_prov;
    } else {
        geoBoundary = 'prov_city';
        geojsonSource = geojsonFeature_provcity;
    }

    // --- Create map ---
    createLeafletMap(lat, lon, geoBoundary);

    // --- Feature interaction ---
    function onEachFeature(feature, layer) {

        if (mapData[feature.id]) {
            const d = mapData[feature.id];

            layer.bindTooltip(
                "<strong>" + d.Location_name.replace(/\\n/g, ', ').trim() + ": " + d.Value + " mt (" + d.Year + ")</strong><br><strong>Percent Share to Total Production: " + d.Percentage + "%</strong>"
            );

            const color = heatmapColors[d.fillKey].color;
            layer.on('mouseover', () => layer.setStyle({ fillOpacity: 0.3 }));
            layer.on('mouseout', () => layer.setStyle({ fillOpacity: getCurrentOpacity() || 0.9, fillColor: color }));
        }
    }

    // --- Add layer ---
    const mapLayer = L.geoJSON(geojsonSource, {
        filter: f => !!mapData[f.id],
        style: f => {
            if (mapData[f.id]) {
                const c = heatmapColors[mapData[f.id].fillKey].color;
                return { color: '#000', weight: 1, fillColor: c, fillOpacity: 0.9 };
            }
            return { fill: false, stroke: false };
        },
        onEachFeature
    }).addTo(leaflet_map);

    // --- Live opacity update ---
    $('#opacity').on('input', function () {
        const newOpacity = getCurrentOpacity();
        mapLayer.setStyle({ fillOpacity: newOpacity });
    });

    // --- Final appearance ---
    updateRangeAppearance();
}

function renderAreaHarvestedMap(mapType, dbData, locationCoordinatesData, periodText = null) {
    resetSlider();

    // --- Color setup ---
    const heatmapColors = {
        firsQ: { color: '#E78A1F' },
        secoQ: { color: '#FDD49E' },
        thirQ: { color: '#66C2A5' },
        fourQ: { color: '#377EB8' },
        defaultFill: { color: 'rgba(165,215,224)' }
    };

    // --- Data filtering ---
    const validData = dbData.filter(item => item.value !== null && item.value !== undefined && item.value !== '0');
    if (!validData.length) return console.warn("No valid data to display.");

    // --- Quartile calculations ---
    const values = validData.map(item => parseFloat(item.value));
    let q1 = quantile(values, 0.25),
        q2 = quantile(values, 0.50),
        q3 = quantile(values, 0.75);

    q1 = dynamicRound(q1, 1);
    q2 = dynamicRound(q2, 1);
    q3 = dynamicRound(q3, 1);

    console.log('Quartiles Area Harvested Map:', q1, q2, q3);

    // --- Legend setup ---
    const scales = [
        { minValue: q1 },
        { minValue: q2 },
        { minValue: q3 },
    ];

    const colors = ['#E78A1F', '#FDD49E', '#66C2A5', '#377EB8'];
    $('.legend-box').each(function (i) {
        $(this).css('background', colors[i]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scales[0].minValue));
    $('#series-2').html("> " + numberWithCommas(scales[0].minValue) + " to " + numberWithCommas(scales[1].minValue));
    $('#series-3').html("> " + numberWithCommas(scales[1].minValue) + " to " + numberWithCommas(scales[2].minValue));
    $('#series-4').html("> " + numberWithCommas(scales[2].minValue));

    // --- Map data prep ---
    const totalValue = validData.reduce((acc, d) => acc + parseFloat(d.value), 0);
    const mapData = {};

    validData.forEach(item => {
        const val = parseFloat(item.value);
        const perc = (val / totalValue) * 100;
        const fillKey =
            val > q3 ? 'fourQ' :
                val > q2 ? 'thirQ' :
                    val > q1 ? 'secoQ' : 'firsQ';

        mapData[item.map_ID] = {
            Location_name: item.location_name,
            Year: item.year,
            Value: (val < 1 ? val.toFixed(2) : Math.round(val)).toLocaleString(),
            fillKey,
            Percentage: perc.toFixed(2)
        };
    });

    var lat = "";
    var lon = "";
    var zoom = "";

    if (locationCoordinatesData) {
        var coord = locationCoordinatesData;
        var zoom = coord['zoom'];

        var lat = coord['latitude'];
        var lon = coord['longitude'];
    }

    // --- Determine boundary type and geojson source ---
    let geoBoundary, geojsonSource;
    if (mapType === 'regional') {
        geoBoundary = 'nat_reg';
        geojsonSource = geojsonFeature_reg;
    } else if (mapType === 'province') {
        geoBoundary = 'reg_prov';
        geojsonSource = geojsonFeature_prov;
    } else {
        geoBoundary = 'prov_city';
        geojsonSource = geojsonFeature_provcity;
    }

    // --- Create map ---
    createLeafletMap(lat, lon, geoBoundary);

    // --- Feature interaction ---
    function onEachFeature(feature, layer) {
        if (mapData[feature.id]) {
            const d = mapData[feature.id];
            layer.bindTooltip(
                "<strong>" + d.Location_name.replace(/\\n/g, ', ').trim() + ": " + d.Value + " ha (" + d.Year + ")</strong><br><strong>Percent Share to Total Area Harvested: " + d.Percentage + "%</strong>"
            );

            const color = heatmapColors[d.fillKey].color;
            layer.on('mouseover', () => layer.setStyle({ fillOpacity: 0.3 }));
            layer.on('mouseout', () => layer.setStyle({ fillOpacity: getCurrentOpacity() || 0.9, fillColor: color }));
        }
    }

    // --- Add layer ---
    const mapLayer = L.geoJSON(geojsonSource, {
        filter: f => !!mapData[f.id],
        style: f => {
            if (mapData[f.id]) {
                const c = heatmapColors[mapData[f.id].fillKey].color;
                return { color: '#000', weight: 1, fillColor: c, fillOpacity: 0.9 };
            }
            return { fill: false, stroke: false };
        },
        onEachFeature
    }).addTo(leaflet_map);

    $('#opacity').on('input', function () {
        const newOpacity = getCurrentOpacity();
        mapLayer.setStyle({ fillOpacity: newOpacity });
    });

    // --- Final appearance ---
    updateRangeAppearance();
}


function renderYieldMap(mapType, dbData, locationCoordinatesData, periodText = null) {
    resetSlider();

    // --- Color setup ---
    const heatmapColors = {
        firsQ: { color: '#F1A63C' },
        secoQ: { color: '#FFF883' },
        thirQ: { color: '#29883E' },
        fourQ: { color: '#3B93E4' },
        fiftQ: { color: '#B07AA1' },
        defaultFill: { color: 'rgba(165,215,224)' }
    };

    // --- Scales ---
    const scales = [
        { min: 0, max: 3.00 },
        { min: 3.00, max: 4.00 },
        { min: 4.00, max: 5.00 },
        { min: 5.00, max: 6.00 },
        { min: 6.00, max: 100.00 }
    ];

    // --- Legend setup ---
    const colors = ['#F1A63C', '#FFF883', '#29883E', '#3B93E4', '#B07AA1'];
    $('.legend-box').each(function (i) {
        $(this).css('background', colors[i]);
    });

    $('#series-1').text('≤ 3');
    $('#series-2').text('> 3 to 4');
    $('#series-3').text('> 4 to 5');
    $('#series-4').text('> 5 to 6');

    // Remove and re-add the 5th scale for alignment
    $('#legend-box-5, #series-5').remove();
    $('.legend-box:last').parent().append(`
        <span class="legend-item">
            <div class="legend-box" id="legend-box-5" style="background:#B07AA1;"></div>
            <p id="series-5" class="d-inline-block text-retain mb-0">> 6</p>
        </span>
    `);

    // --- Data filtering ---
    const validData = dbData.filter(item => item.value && item.value !== '0');
    if (!validData.length) return console.warn("No valid yield data to display.");

    // --- Map data prep ---
    const totalValue = validData.reduce((acc, d) => acc + parseFloat(d.value), 0);
    const mapData = {};

    validData.forEach(item => {
        const val = parseFloat(item.value);
        const perc = (val / totalValue) * 100;

        let fillKey = 'firsQ';
        if (val > scales[4].min) fillKey = 'fiftQ';
        else if (val > scales[3].min) fillKey = 'fourQ';
        else if (val > scales[2].min) fillKey = 'thirQ';
        else if (val > scales[1].min) fillKey = 'secoQ';

        mapData[item.map_ID] = {
            Location_name: item.location_name,
            Year: item.year,
            Value: val.toFixed(2),
            fillKey,
            Percentage: perc.toFixed(2)
        };
    });

    // --- Coordinates setup ---
    let lat = 12.788929, lon = 121.938415, zoom = 6;
    if (locationCoordinatesData) {
        lat = locationCoordinatesData.latitude;
        lon = locationCoordinatesData.longitude;
        zoom = locationCoordinatesData.zoom;
    }

    // --- Determine boundary type and geojson source ---
    let geoBoundary, geojsonSource;
    if (mapType === 'regional') {
        geoBoundary = 'nat_reg';
        geojsonSource = geojsonFeature_reg;
    } else if (mapType === 'province') {
        geoBoundary = 'reg_prov';
        geojsonSource = geojsonFeature_prov;
    } else {
        geoBoundary = 'prov_city';
        geojsonSource = geojsonFeature_provcity;
    }

    // --- Create map ---
    createLeafletMap(lat, lon, geoBoundary);

    // --- Feature interaction ---
    function onEachFeature(feature, layer) {
        if (mapData[feature.id]) {
            const d = mapData[feature.id];
            const color = heatmapColors[d.fillKey].color;

            layer.bindTooltip(
                "<strong>" + d.Location_name.replace(/\\n/g, ', ').trim() + ": " + d.Value + " mt/ha (" + d.Year + ")</strong>"
            );

            layer.on('mouseover', () => layer.setStyle({ fillOpacity: 0.3 }));
            layer.on('mouseout', () =>
                layer.setStyle({ fillOpacity: getCurrentOpacity() || 0.9, fillColor: color })
            );
        }
    }

    // --- Add layer ---
    const mapLayer = L.geoJSON(geojsonSource, {
        filter: f => !!mapData[f.id],
        style: f => {
            if (mapData[f.id]) {
                const c = heatmapColors[mapData[f.id].fillKey].color;
                return { color: '#000', weight: 1, fillColor: c, fillOpacity: 0.9 };
            }
            return { fill: false, stroke: false };
        },
        onEachFeature
    }).addTo(leaflet_map);

    $('#opacity').on('input', function () {
        const newOpacity = getCurrentOpacity();
        mapLayer.setStyle({ fillOpacity: newOpacity });
    });

    // --- Final appearance ---
    updateRangeAppearance();
}

// MAP CODE PRODUCTION - REGIONAL
function regionalProductionMap(dbRegsMapData, locationCoordinatesData = null, periodText = null) {

    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#FF7F00'
    };
    heatmap_colors['secoQ'] = {
        color: '#FFD92F'
    };
    heatmap_colors['thirQ'] = {
        color: '#4DAF4A'
    };
    heatmap_colors['fourQ'] = {
        color: '#1F78B4'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var dbRegsMap = dbRegsMapData.filter(function (item) {
        // Check if palay_value is not null, undefined, or empty, and cast to integer
        return item.value !== null && item.value !== undefined && item.value !== '0';
    });

    // region - annual
    var quartile1 = 500000;
    var quartile2 = 1000000;
    var quartile3 = 2000000;

    if (periodText != "ANNUAL") {
        var data = dbRegsMap.map(item => parseFloat(item.value));

        var quartile1 = quantile(data, 0.25); // 25th percentile
        var quartile2 = quantile(data, 0.50); // 50th percentile (median)
        var quartile3 = quantile(data, 0.75); // 50th percentile (median)

        quartile1 = dynamicRound(quartile1, 1);
        quartile2 = dynamicRound(quartile2, 1);
        quartile3 = dynamicRound(quartile3, 1);
    }

    var scales = [{
        "range": 0,
        "minValue": quartile1,
        "maxValue": "499999"
    },
    {
        "range": 1,
        "minValue": quartile2,
        "maxValue": "999999"
    },
    {
        "range": 2,
        "minValue": quartile3,
        "maxValue": "1999999.00"
    },
    {
        "range": 3,
        "minValue": "2000000",
        "maxValue": "2999999"
    },
    ];

    $('.legend-box').each(function (index) {
        var colors = ['#FF7F00', '#FFD92F', '#4DAF4A', '#1F78B4'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scales[0]["minValue"]));
    $('#series-2').html("> " + numberWithCommas(scales[0]["minValue"]) + " to " + numberWithCommas(scales[1]["minValue"]));
    $('#series-3').html("> " + numberWithCommas(scales[1]["minValue"]) + " to " + numberWithCommas(scales[2]["minValue"]));
    $('#series-4').html("> " + numberWithCommas(scales[2]["minValue"]));

    var totalValue = dbRegsMap.reduce((acc, data) => acc + parseFloat(data.value), 0);

    let mapDataR = [];

    for (i = 0; i < dbRegsMap.length; i++) {
        var gc = dbRegsMap[i].map_ID;
        var value = parseFloat(dbRegsMap[i].value);
        var percentage = (value / totalValue) * 100;

        var fill_color = "";
        if (value > scales[2].minValue) {
            fill_color = "fourQ"; // Corresponds to the highest range
        } else if (value > scales[1].minValue) {
            fill_color = "thirQ"; // Corresponds to the third range
        } else if (value > scales[0].minValue) {
            fill_color = "secoQ"; // Corresponds to the second range
        } else {
            fill_color = "firsQ"; // Corresponds to the lowest range
        }

        mapDataR[gc] = {
            Location_name: dbRegsMap[i].location_name,
            Year: dbRegsMap[i].year,
            Value: parseInt(dbRegsMap[i].value).toLocaleString(),
            // Value: (dbRegsMap[i].value).toFixed(2).toLocaleString(),
            fillKey: fill_color,
            Percentage: percentage.toFixed(2), // Round to 2 decimal places
        };
    }

    var mapDataR_ready = mapDataR;

    var leaflet_map_position_x = "";
    var leaflet_map_position_y = "";
    var zoom = "";

    if (locationCoordinatesData) {
        var coord = locationCoordinatesData;
        var zoom = coord['zoom'];

        var leaflet_map_position_x = coord['latitude'];
        var leaflet_map_position_y = coord['longitude'];
    }

    // COMMENT OUT
    // let previousLayer = null; // Variable to store the previously clicked polygon layer

    function onEachFeature_reg(feature, layer) {

        if (mapDataR_ready[feature.id]) {
            var value = mapDataR_ready[feature.id].Value;
            var percentage = mapDataR_ready[feature.id].Percentage;

            layer.bindTooltip(
                "<strong>" + mapDataR_ready[feature.id].Location_name +
                ": " + value + " mt (" + mapDataR_ready[feature.id].Year +
                ")</strong><br><strong>Percent Share to Total Production: " + percentage + "%</strong>"
            ).openTooltip();

            var fill_key = mapDataR_ready[feature.id].fillKey;
            layer.on('mouseover', function (e) {
                // Unset highlight
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity() || 0.9; // Default opacity if function returns undefined
                layer.setStyle({
                    'opacity': opacity,
                    'fillColor': color,
                    'fillOpacity': opacity
                });
            });

        }

    }

    function loadRegionalProductionMap() {

        createLeafletMap();

        mapLayer = L.geoJSON(geojsonFeature_reg, {
            style: function (feature) {

                if (mapDataR_ready[feature.id]) {

                    var fill_key = mapDataR_ready[feature.id].fillKey;
                    return { color: "#000000", weight: 1, fill: true, fillColor: heatmap_colors[fill_key].color, fillOpacity: 0.9 };
                } else {
                    //  return {color: "#000000",weight: 0.5,opacity:0.5, fill: true, fillColor: heatmap_colors['defaultFill'].color, fillOpacity: 0.9}; 
                    return { fill: false, stroke: false };
                }
            }, onEachFeature: onEachFeature_reg
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // Add custom zoom control at the top left
        // L.control.zoom({
        // 	position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadRegionalProductionMap();
}

// ////////////////////////////////////////////////// MAP CODE PRODUCTION REGIONAL BY PROVINCE
function regionProductionMapByProvince(dbProvsMapData, locationCoordinatesData, periodText = null) {

    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#FF7F00'
    };
    heatmap_colors['secoQ'] = {
        color: '#FFD92F'
    };
    heatmap_colors['thirQ'] = {
        color: '#4DAF4A'
    };
    heatmap_colors['fourQ'] = {
        color: '#1F78B4'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var dbProvsMap = dbProvsMapData.filter(function (item) {
        // Check if palay_value is not null, undefined, or empty, and cast to integer
        return item.value !== null && item.value !== undefined && item.value !== '0';
    });

    var data = dbProvsMap.map(item => parseFloat(item.value));

    var quartile1 = quantile(data, 0.25); // 25th percentile
    var quartile2 = quantile(data, 0.50); // 50th percentile (median)
    var quartile3 = quantile(data, 0.75); // 75th percentile

    quartile1 = dynamicRound(quartile1, 1);
    quartile2 = dynamicRound(quartile2, 1);
    quartile3 = dynamicRound(quartile3, 1);

    var scalesC = [{
        "range": 0,
        "minValue": quartile1,
        "maxValue": "499999"
    },
    {
        "range": 1,
        "minValue": quartile2,
        "maxValue": "999999"
    },
    {
        "range": 2,
        "minValue": quartile3,
        "maxValue": "1999999.00"
    },
    {
        "range": 3,
        "minValue": "2000000",
        "maxValue": "2999999"
    },
    ];

    $('.legend-box').each(function (index) {
        var colors = ['#FF7F00', '#FFD92F', '#4DAF4A', '#1F78B4'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scalesC[0]["minValue"]));
    $('#series-2').html("> " + numberWithCommas(scalesC[0]["minValue"]) + " to " + numberWithCommas(scalesC[1]["minValue"]));
    $('#series-3').html("> " + numberWithCommas(scalesC[1]["minValue"]) + " to " + numberWithCommas(scalesC[2]["minValue"]));
    $('#series-4').html("> " + numberWithCommas(scalesC[2]["minValue"]));

    let mapData = [];

    var totalValue = dbProvsMap.reduce((acc, data) => acc + parseFloat(data.value), 0);

    for (i = 0; i < dbProvsMap.length; i++) {
        var gc = dbProvsMap[i].map_ID;
        var value = parseFloat(dbProvsMap[i].value);
        var percentage = (value / totalValue) * 100;

        var fill_color = "";

        if (value > scalesC[2].minValue) {
            fill_color = "fourQ"; // Corresponds to the highest range
        } else if (value > scalesC[1].minValue) {
            fill_color = "thirQ"; // Corresponds to the third range
        } else if (value > scalesC[0].minValue) {
            fill_color = "secoQ"; // Corresponds to the second range
        } else {
            fill_color = "firsQ"; // Corresponds to the lowest range
        }

        mapData[gc] = {
            Location_name: dbProvsMap[i].location_name,
            Year: dbProvsMap[i].year,
            Value: Math.round(dbProvsMap[i].value).toLocaleString(),
            fillKey: fill_color,
            Percentage: percentage.toFixed(2), // Round to 2 decimal places
        };
    }

    var coord = locationCoordinatesData;
    var zoom = coord['zoom'];

    var mapData_ready = mapData;

    var leaflet_map_position_x = coord['latitude'];
    var leaflet_map_position_y = coord['longitude'];

    // Provincial
    function onEachFeature_RegionProductionProvince(feature, layer) {

        if (mapData_ready[feature.id]) {
            var value = mapData_ready[feature.id].Value;
            var percentage = mapData_ready[feature.id].Percentage;

            layer.bindTooltip(
                "<strong>" + mapData_ready[feature.id].Location_name +
                ": " + value + " mt (" + mapData_ready[feature.id].Year +
                ")</strong><br><strong>Percent Share to Total Production: " + percentage + "%</strong>"
            ).openTooltip();

            // layer.bindTooltip("<strong>" + mapData_ready[feature.id].Location_name + ": " + value + " mt (" + mapData_ready[feature.id].Year + ")</strong>").openTooltip();

            var fill_key = mapData_ready[feature.id].fillKey;
            layer.on('mouseover', function (e) {
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity();
                layer.setStyle({
                    'opacity': opacity,
                    'fillColor': color,
                    'fillOpacity': opacity
                });
            });

        }
    }

    function loadnewMapdata_RegionProductionMapProvince() {
        var geoBoundary = "reg_prov";

        createLeafletMap(leaflet_map_position_x, leaflet_map_position_y, geoBoundary);

        mapLayer = L.geoJSON(geojsonFeature_prov, {
            filter: function (feature) {

                if (mapData_ready[feature.id]) {
                    return true;

                } else {
                    return false;
                }
            },
            style: function (feature) {

                if (mapData_ready[feature.id]) {
                    // return {color: "#000000",weight: 1, fill: true, fillColor: heatmap_colors['defaultFill'].color, fillOpacity: 0.9}; 

                    var fill_key = mapData_ready[feature.id].fillKey;

                    return {
                        color: "#000000",
                        weight: 1,
                        fill: true,
                        fillColor: heatmap_colors[fill_key].color,
                        fillOpacity: 0.9
                    };

                } else {
                    return { fill: false, stroke: false };
                }


            },
            onEachFeature: onEachFeature_RegionProductionProvince
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // Add custom zoom control at the top left
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadnewMapdata_RegionProductionMapProvince();
}

// // ////////////////////////////////////////////////// MAP CODE PROVINCE BY MUNICIPALITY
function provinceMapByMunicipality(dbMuniMapData, locationCoordinatesData, periodText = null) {

    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#FF7F00'
    };
    heatmap_colors['secoQ'] = {
        color: '#FFD92F'
    };
    heatmap_colors['thirQ'] = {
        color: '#4DAF4A'
    };
    heatmap_colors['fourQ'] = {
        color: '#1F78B4'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var dbMuniMap = dbMuniMapData.filter(function (item) {
        // Check if palay_value is not null, undefined, or empty, and cast to integer
        return item.value !== null && item.value !== undefined;
    });


    var coord = locationCoordinatesData;
    var x = coord['longitude'];
    var y = coord['latitude'];
    var zoom = coord['zoom'];

    var data = dbMuniMap.map(item => parseFloat(item.value));

    var quartile1 = quantile(data, 0.25); // 25th percentile
    var quartile2 = quantile(data, 0.50); // 50th percentile (median)
    var quartile3 = quantile(data, 0.75); // 75th percentile

    quartile1 = dynamicRound(quartile1, 1);
    quartile2 = dynamicRound(quartile2, 1);
    quartile3 = dynamicRound(quartile3, 1);


    var scalesX = [{
        "range": 0,
        "minValue": quartile1,
        "maxValue": "499999"
    },
    {
        "range": 1,
        "minValue": quartile2,
        "maxValue": "999999"
    },
    {
        "range": 2,
        "minValue": quartile3,
        "maxValue": "1999999.00"
    },
    {
        "range": 3,
        "minValue": "2000000",
        "maxValue": "2999999"
    },
    ];

    $('.legend-box').each(function (index) {
        var colors = ['#FF7F00', '#FFD92F', '#4DAF4A', '#1F78B4'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scalesX[0]["minValue"]));
    $('#series-2').html("> " + numberWithCommas(scalesX[0]["minValue"]) + " to " + numberWithCommas(scalesX[1]["minValue"]));
    $('#series-3').html("> " + numberWithCommas(scalesX[1]["minValue"]) + " to " + numberWithCommas(scalesX[2]["minValue"]));
    $('#series-4').html("> " + numberWithCommas(scalesX[2]["minValue"]));

    let muniData = [];

    var totalValue = dbMuniMap.reduce((acc, data) => acc + parseFloat(data.value), 0);

    for (i = 0; i < dbMuniMap.length; i++) {
        var gc = dbMuniMap[i].map_ID;
        var value = parseFloat(dbMuniMap[i].value);
        var percentage = (value / totalValue) * 100;

        var fill_color = "";
        if (value > scalesX[2].minValue) {
            fill_color = "fourQ"; // Corresponds to the highest range
        } else if (value > scalesX[1].minValue) {
            fill_color = "thirQ"; // Corresponds to the third range
        } else if (value > scalesX[0].minValue) {
            fill_color = "secoQ"; // Corresponds to the second range
        } else {
            fill_color = "firsQ"; // Corresponds to the lowest range
        }

        muniData[gc] = {
            Location_name: dbMuniMap[i].location_name,
            Year: dbMuniMap[i].year,
            Value: dbMuniMap[i].value < 1 ? parseFloat(dbMuniMap[i].value).toFixed(2) : parseInt(dbMuniMap[i].value).toLocaleString(),
            fillKey: fill_color,
            Percentage: percentage.toFixed(2), // Round to 2 decimal places
        };
    }

    var muniData_ready = muniData;

    var leaflet_map_position_x = coord['latitude'];
    var leaflet_map_position_y = coord['longitude'];

    function onEachFeature_ProvinceMapMunicipality(feature, layer) {

        if (muniData_ready[feature.id]) {
            var value = muniData_ready[feature.id].Value;
            var percentage = muniData_ready[feature.id].Percentage;

            layer.bindTooltip("<strong>" + feature.properties.ADM3_EN + ", " + feature.properties.ADM2_EN + ": " + value + " mt (" + muniData_ready[feature.id].Year +
                ")</strong><br><strong>Percent Share to Total Production: " + percentage + "%</strong>"
            ).openTooltip();

            layer.on('mouseover', function (e) {
                // Unset highlight
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var fill_key = muniData_ready[feature.id].fillKey;
            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity();
                layer.setStyle({
                    'opacity': opacity,
                    'fillColor': color,
                    'fillOpacity': opacity
                });
            });
        }

    }

    function loadnewMapdata_ProvinceMapMunicipality() {

        var geoBoundary = "prov_city";

        createLeafletMap(leaflet_map_position_x, leaflet_map_position_y, geoBoundary);

        mapLayer = L.geoJSON(geojsonFeature_provcity, {
            filter: function (feature) {
                if (muniData_ready[feature.id]) {
                    return true;
                } else {
                    return false;
                }
            },
            style: function (feature) {
                //alert(feature.id);
                if (muniData_ready[feature.id]) {
                    var fill_key = muniData_ready[feature.id].fillKey;
                    if (fill_key != "") {
                        return {
                            color: "#000000",
                            weight: 0.5,
                            fill: true,
                            fillColor: heatmap_colors[fill_key].color,
                            fillOpacity: 0.9
                        };
                    } else {
                        return { fill: false, stroke: false };
                    }

                } else {
                    return { fill: false, stroke: false };
                }


            }, onEachFeature: onEachFeature_ProvinceMapMunicipality
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // // Add custom zoom control at the top left
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadnewMapdata_ProvinceMapMunicipality();
}

// ////////////////////////////////////////////////// MAP CODE YIELD - REGIONAL
function regionalYieldMap(dbRegsMapData, locationCoordinatesData = null) {

    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#F1A63C'
    };
    heatmap_colors['secoQ'] = {
        color: '#FFF883'
    };
    heatmap_colors['thirQ'] = {
        color: '#29883E'
    };
    heatmap_colors['fourQ'] = {
        color: '#3B93E4'
    };
    heatmap_colors['fiftQ'] = {
        color: '#B07AA1'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var scalesC_formaps = [{
        "range": 0,
        "minValue": "3.00",
        "maxValue": "4.00"
    },
    {
        "range": 1,
        "minValue": "4.00",
        "maxValue": "5.00"
    },
    {
        "range": 2,
        "minValue": "5.00",
        "maxValue": "20.00"
    },
    {
        "range": 3,
        "minValue": "6.00",
        "maxValue": "100.00"
    },
    ];

    // //regional
    var dbRegsMap = dbRegsMapData;

    // Step 1: Calculate total value
    var totalValue = dbRegsMap.reduce((acc, data) => acc + parseFloat(data.value), 0);

    // console.log('Total Value:', totalValue);


    let mapDataR = [];

    for (i = 0; i < dbRegsMap.length; i++) {
        var gc = dbRegsMap[i].map_ID;
        var value = parseFloat(dbRegsMap[i].value);
        var percentage = (value / totalValue) * 100;

        var fill_color = "";
        if (value > scalesC_formaps[3].minValue) {
            fill_color = "fiftQ";
        } else if (value > scalesC_formaps[2].minValue) {
            fill_color = "fourQ";
        } else if (value > scalesC_formaps[1].minValue) {
            fill_color = "thirQ";
        } else if (value > scalesC_formaps[0].minValue) {
            fill_color = "secoQ";
        } else if (value <= scalesC_formaps[0].minValue) {
            fill_color = "firsQ";
        }

        mapDataR[gc] = {
            Location_name: dbRegsMap[i].location_name,
            Year: dbRegsMap[i].year,
            Value: parseFloat(dbRegsMap[i].value).toLocaleString(),
            Percentage: percentage.toFixed(2), // Round to 2 decimal places
            fillKey: fill_color
        };
    }

    $('.legend-box').each(function (index) {
        var colors = ['#F1A63C', '#FFF883', '#29883E', '#3B93E4'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').text('≤ 3');
    $('#series-2').text('> 3 to 4');
    $('#series-3').text('> 4 to 5');
    $('#series-4').text('> 5 to 6');
    // $('#series-5').text('> 6');

    /// Remove existing series-5 (if re-rendering)
    $('#legend-box-5').remove();
    $('#series-5').remove();

    // Add series-5 properly aligned
    $('.legend-box:last').parent().append(`
        <span class="legend-item">
            <div class="legend-box" id="legend-box-5" style="background:#B07AA1;"></div>
            <p id="series-5" class="d-inline-block text-retain mb-0">> 6</p>
        </span>
    `);

    var mapDataR_ready = mapDataR;

    var leaflet_map_position_x = "";
    var leaflet_map_position_y = "";
    var zoom = "";

    if (locationCoordinatesData) {
        var coord = locationCoordinatesData;
        var x = coord['longitude'];
        var y = coord['latitude'];
        var zoom = coord['zoom'];

        var leaflet_map_position_x = coord['latitude'];
        var leaflet_map_position_y = coord['longitude'];
    }

    // //REGIONAL YIELD
    function onEachFeatureRegionalYieldMap(feature, layer) {

        if (mapDataR_ready[feature.id]) {
            var value = mapDataR_ready[feature.id].Value;
            var fill_key = mapDataR_ready[feature.id].fillKey;
            var percentage = mapDataR_ready[feature.id].Percentage;

            layer.bindTooltip("<strong>" + mapDataR_ready[feature.id].Location_name + ": " + value + " mt/ha (" +
                mapDataR_ready[feature.id].Year +
                ")</strong>"
            ).openTooltip();

            layer.on('mouseover', function (e) {
                // Unset highlight
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity();
                layer.setStyle({
                    'opacity': opacity,
                    'fillOpacity': opacity,
                    'fillColor': color
                });
            });
        }
    }

    function loadRegionalYieldMap() {

        createLeafletMap();

        mapLayer = L.geoJSON(geojsonFeature_reg, {
            style: function (feature) {

                if (mapDataR_ready[feature.id]) {
                    var fill_key = mapDataR_ready[feature.id].fillKey;
                    if (fill_key != "")
                        return { color: "#000000", weight: 0.5, opacity: 0.5, fill: true, fillColor: heatmap_colors[fill_key].color, fillOpacity: 0.9 };
                    else
                        return { fill: false, stroke: false };
                } else if (mapDataR_ready[feature.id]) {
                    // return {color: "#000000",weight: 1, fill: true, fillColor: heatmap_colors['defaultFill'].color, fillOpacity: 0.9}; 
                    return { color: "#000000", weight: 1.5, fill: false, fillColor: "#FFFFFF", fillOpacity: 0.5 };
                } else {
                    // return {color: "#000000",weight: 0.5,opacity:0.5, fill: true, fillColor: heatmap_colors['defaultFill'].color, fillOpacity: 0.9}; 
                    return { fill: false, stroke: false };
                }
            }, onEachFeature: onEachFeatureRegionalYieldMap
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // Add custom zoom control at the top left
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadRegionalYieldMap();
}

// ////////////////////////////////////////////////// MAP CODE YIELD - REGIONAL BY PROVINCE
function regionYieldMapByProvince(dbProvsMapData, locationCoordinatesData) {
    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#F1A63C'
    };
    heatmap_colors['secoQ'] = {
        color: '#FFF883'
    };
    heatmap_colors['thirQ'] = {
        color: '#29883E'
    };
    heatmap_colors['fourQ'] = {
        color: '#3B93E4'
    };
    heatmap_colors['fiftQ'] = {
        color: '#B07AA1'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var scalesP_formaps = [{
        "range": 0,
        "minValue": "3.00",
        "maxValue": "4.00"
    },
    {
        "range": 1,
        "minValue": "4.00",
        "maxValue": "5.00"
    },
    {
        "range": 2,
        "minValue": "5.00",
        "maxValue": "20.00"
    },
    {
        "range": 3,
        "minValue": "6.00",
        "maxValue": "100.00"
    },
    ];

    // //regional
    var dbProvsMap = dbProvsMapData;

    let mapData = [];

    for (i = 0; i < dbProvsMap.length; i++) {
        var gc = dbProvsMap[i].map_ID;
        var value = parseFloat(dbProvsMap[i].value);

        var fill_color = "";
        if (value > scalesP_formaps[3].minValue) {
            fill_color = "fiftQ";
        } else if (value > scalesP_formaps[2].minValue) {
            fill_color = "fourQ";
        } else if (value > scalesP_formaps[1].minValue) {
            fill_color = "thirQ";
        } else if (value > scalesP_formaps[0].minValue) {
            fill_color = "secoQ";
        } else if (value <= scalesP_formaps[0].minValue) {
            fill_color = "firsQ";
        }

        mapData[gc] = {
            Location_name: dbProvsMap[i].location_name,
            Year: dbProvsMap[i].year,
            Value: parseFloat(dbProvsMap[i].value).toLocaleString(),
            fillKey: fill_color
        };
    }

    var coord = locationCoordinatesData;
    var x = coord['longitude'];
    var y = coord['latitude'];
    var zoom = coord['zoom'];

    $('.legend-box').each(function (index) {
        var colors = ['#F1A63C', '#FFF883', '#29883E', '#3B93E4'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').text('≤ 3');
    $('#series-2').text('> 3 to 4');
    $('#series-3').text('> 4 to 5');
    $('#series-4').text('> 5 to 6');
    // $('#series-5').text('> 6');

    /// Remove existing series-5 (if re-rendering)
    $('#legend-box-5').remove();
    $('#series-5').remove();

    // Add series-5 properly aligned
    $('.legend-box:last').parent().append(`
        <span class="legend-item">
            <div class="legend-box" id="legend-box-5" style="background:#B07AA1;"></div>
            <p id="series-5" class="d-inline-block text-retain mb-0">> 6</p>
        </span>
    `);

    var mapData_ready = mapData;

    var leaflet_map_position_x = coord['latitude'];
    var leaflet_map_position_y = coord['longitude'];

    // Provincial
    function onEachFeature_RegionYieldMapProvince(feature, layer) {

        if (mapData_ready[feature.id]) {
            var value = mapData_ready[feature.id].Value;
            var fill_key = mapData_ready[feature.id].fillKey;

            layer.bindTooltip("<strong>" + mapData_ready[feature.id].Location_name + ": " + value + " mt/ha (" + mapData_ready[feature.id].Year + ")</strong>").openTooltip();
            layer.on('mouseover', function (e) {
                // Unset highlight
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity();
                layer.setStyle({
                    'opacity': opacity,
                    'fillColor': color,
                    'fillOpacity': opacity
                });
            });
        }
    }

    function loadnewMapdata_RegionYieldMapProvince() {
        var geoBoundary = "reg_prov";

        createLeafletMap(leaflet_map_position_x, leaflet_map_position_y, geoBoundary);

        mapLayer = L.geoJSON(geojsonFeature_prov, {
            filter: function (feature) {
                if (mapData_ready[feature.id]) {
                    if (feature.properties.locationtype)
                        return true;
                    else
                        return false;
                } else {
                    return false
                }
            },
            style: function (feature) {
                if (mapData_ready[feature.id]) {
                    var fill_key = mapData_ready[feature.id].fillKey;
                    return {
                        color: "#000000",
                        weight: 0.5,
                        opacity: 0.5,
                        fill: true,
                        fillColor: heatmap_colors[fill_key].color,
                        fillOpacity: 0.9
                    };
                } else {
                    return {
                        fill: false,
                        stroke: false
                    };
                }

            }, onEachFeature: onEachFeature_RegionYieldMapProvince
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // // Add custom zoom control at the top left
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadnewMapdata_RegionYieldMapProvince();
}

// ////////////////////////////////////////////////// MAP CODE AREA HARVESTED - REGIONAL
function regionalAreaHarvestedMap(dbRegsMapData, locationCoordinatesData = null, periodText = null) {
    // Regional
    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#E78A1F'
    };
    heatmap_colors['secoQ'] = {
        color: '#FDD49E'
    };
    heatmap_colors['thirQ'] = {
        color: '#66C2A5'
    };
    heatmap_colors['fourQ'] = {
        color: '#377EB8'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var dbRegsMap = dbRegsMapData.filter(function (item) {
        // Check if palay_value is not null, undefined, or empty, and cast to integer
        return item.value !== null && item.value !== undefined && item.value !== '0';
    });

    // var quartile1 = 200000;
    // var quartile2 = 500000;

    // if (periodText != "ANNUAL"){
    var data = dbRegsMap.map(item => parseFloat(item.value));

    var quartile1 = quantile(data, 0.25); // 25th percentile
    var quartile2 = quantile(data, 0.50); // 50th percentile (median)
    var quartile3 = quantile(data, 0.75); // 50th percentile (median)

    quartile1 = dynamicRound(quartile1, 1);
    quartile2 = dynamicRound(quartile2, 1);
    quartile3 = dynamicRound(quartile3, 1);
    // }


    var scales = [
        {
            "range": 0,
            "minValue": quartile1,
            "maxValue": ""
        },
        {
            "range": 1,
            "minValue": quartile2,
            "maxValue": ""
        },
        {
            "range": 2,
            "minValue": quartile3,
            "maxValue": ""
        },
    ];

    let mapDataR = [];

    var totalValue = dbRegsMap.reduce((acc, data) => acc + parseFloat(data.value), 0);

    for (i = 0; i < dbRegsMap.length; i++) {
        var gc = dbRegsMap[i].map_ID;
        var value = parseFloat(dbRegsMap[i].value);
        var percentage = (value / totalValue) * 100;

        var fill_color = "";

        if (value > scales[2].minValue) {
            fill_color = "fourQ"; // Corresponds to the highest range
        } else if (value > scales[1].minValue) {
            fill_color = "thirQ"; // Corresponds to the third range
        } else if (value > scales[0].minValue) {
            fill_color = "secoQ"; // Corresponds to the second range
        } else {
            fill_color = "firsQ"; // Corresponds to the lowest range
        }

        mapDataR[gc] = {
            Location_name: dbRegsMap[i].location_name,
            Year: dbRegsMap[i].year,
            Value: parseInt(dbRegsMap[i].value).toLocaleString(),
            fillKey: fill_color,
            Percentage: percentage.toFixed(2), // Round to 2 decimal places
        };
    }

    $('.legend-box').each(function (index) {
        var colors = ['#E78A1F', '#FDD49E', '#66C2A5', '#377EB8'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scales[0]["minValue"]));
    $('#series-2').html("> " + numberWithCommas(scales[0]["minValue"]) + " to " + numberWithCommas(scales[1]["minValue"]));
    $('#series-3').html("> " + numberWithCommas(scales[1]["minValue"]) + " to " + numberWithCommas(scales[2]["minValue"]));
    $('#series-4').html("> " + numberWithCommas(scales[2]["minValue"]));

    var mapDataR_ready = mapDataR;

    var leaflet_map_position_x = "";
    var leaflet_map_position_y = "";
    var zoom = "";

    if (locationCoordinatesData) {
        var coord = locationCoordinatesData;
        var x = coord['longitude'];
        var y = coord['latitude'];
        var zoom = coord['zoom'];

        var leaflet_map_position_x = coord['latitude'];
        var leaflet_map_position_y = coord['longitude'];
    }

    //REGIONAL
    function onEachFeatureRegionalAreaHarvested(feature, layer) {

        if (mapDataR_ready[feature.id]) {
            var value = mapDataR_ready[feature.id].Value;
            var percentage = mapDataR_ready[feature.id].Percentage;

            layer.bindTooltip("<strong>" + mapDataR_ready[feature.id].Location_name +
                ": " + value + " ha (" + mapDataR_ready[feature.id].Year +
                ")</strong><br><strong>Percent Share to Total Area Harvested: " + percentage + "%</strong>"
            ).openTooltip();

            var fill_key = mapDataR_ready[feature.id].fillKey;
            layer.on('mouseover', function (e) {
                // Unset highlight
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity();
                layer.setStyle({
                    'opacity': opacity,
                    'fillColor': color,
                    'fillOpacity': opacity
                });
            });
        }
    }

    function loadRegionalAreaHarvestedMap() {

        createLeafletMap();

        mapLayer = L.geoJSON(geojsonFeature_reg, {
            style: function (feature) {

                if (mapDataR_ready[feature.id]) {
                    var fill_key = mapDataR_ready[feature.id].fillKey;

                    return {
                        color: "#000000",
                        weight: 1,
                        fill: true,
                        fillColor: heatmap_colors[fill_key].color,
                        fillOpacity: 0.9
                    };

                } else {
                    return { fill: false, stroke: false };
                }

            },
            onEachFeature: onEachFeatureRegionalAreaHarvested
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // // Add custom zoom control at the top left
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadRegionalAreaHarvestedMap();
}

// // ////////////////////////////////////////////////// MAP CODE AREA HARVESTED REGIONAL BY PROVINCE
function regionAreaHarvestedMapByProvince(dbProvsMapData, locationCoordinatesData, prov_y3Data, periodText = null) {
    // Regional
    var heatmap_colors = [];

    heatmap_colors['firsQ'] = {
        color: '#E78A1F'
    };
    heatmap_colors['secoQ'] = {
        color: '#FDD49E'
    };
    heatmap_colors['thirQ'] = {
        color: '#66C2A5'
    };
    heatmap_colors['fourQ'] = {
        color: '#377EB8'
    };
    heatmap_colors['defaultFill'] = {
        color: 'rgba(165,215,224)'
    };

    var dbProvsMap_withgeo = dbProvsMapData.filter(function (item) {
        // Check if palay_value is not null, undefined, or empty, and cast to integer
        return item.value !== null && item.value !== undefined && item.value !== '0';
    });

    // if (periodText != "ANNUAL"){
    var data = dbProvsMap_withgeo.map(item => parseFloat(item.value));

    var quartile1 = quantile(data, 0.25); // 25th percentile
    var quartile2 = quantile(data, 0.50); // 50th percentile (median)
    var quartile3 = quantile(data, 0.75); // 75th percentile

    quartile1 = dynamicRound(quartile1, 1);
    quartile2 = dynamicRound(quartile2, 1);
    quartile3 = dynamicRound(quartile3, 1);
    // }

    var scalesC = [{
        "range": 0,
        "minValue": quartile1,
        "maxValue": "499999"
    },
    {
        "range": 1,
        "minValue": quartile2,
        "maxValue": "999999"
    },
    {
        "range": 2,
        "minValue": quartile3,
        "maxValue": "1999999.00"
    },
    {
        "range": 3,
        "minValue": "2000000",
        "maxValue": "2999999"
    },
    ];

    $('.legend-box').each(function (index) {
        var colors = ['#E78A1F', '#FDD49E', '#66C2A5', '#377EB8'];
        $(this).css('background', colors[index]);
    });

    $('#series-1').html("≤ " + numberWithCommas(scalesC[0]["minValue"]));
    $('#series-2').html("> " + numberWithCommas(scalesC[0]["minValue"]) + " to " + numberWithCommas(scalesC[1]["minValue"]));
    $('#series-3').html("> " + numberWithCommas(scalesC[1]["minValue"]) + " to " + numberWithCommas(scalesC[2]["minValue"]));
    $('#series-4').html("> " + numberWithCommas(scalesC[2]["minValue"]));

    let mapData = [];

    var totalValue = dbProvsMap_withgeo.reduce((acc, data) => acc + parseFloat(data.value), 0);

    for (i = 0; i < dbProvsMap_withgeo.length; i++) {
        var gc = dbProvsMap_withgeo[i].map_ID;
        var value = parseFloat(dbProvsMap_withgeo[i].value);
        var percentage = (value / totalValue) * 100;

        var fill_color = "";
        if (value > scalesC[2].minValue) {
            fill_color = "fourQ"; // Corresponds to the highest range
        } else if (value > scalesC[1].minValue) {
            fill_color = "thirQ"; // Corresponds to the third range
        } else if (value > scalesC[0].minValue) {
            fill_color = "secoQ"; // Corresponds to the second range
        } else {
            fill_color = "firsQ"; // Corresponds to the lowest range
        }

        mapData[gc] = {
            Location_name: dbProvsMap_withgeo[i].location_name,
            Year: dbProvsMap_withgeo[i].year,
            Value: parseInt(dbProvsMap_withgeo[i].value).toLocaleString(),
            fillKey: fill_color,
            Percentage: percentage.toFixed(2)
        };
    }

    var coord = locationCoordinatesData;
    var zoom = coord['zoom'];

    var mapData_ready = mapData;

    var leaflet_map_position_x = coord['latitude'];
    var leaflet_map_position_y = coord['longitude'];

    // Provincial
    function onEachFeature_RegionAreaHarvestedMapProvince(feature, layer) {

        if (mapData_ready[feature.id]) {
            var value = mapData_ready[feature.id].Value;
            var percentage = mapData_ready[feature.id].Percentage;

            layer.bindTooltip("<strong>" + mapData_ready[feature.id].Location_name + ": " + value + " ha (" +
                mapData_ready[feature.id].Year +
                ")</strong><br><strong>Percent Share to Total Area Harvested: " + percentage + "%</strong>"
            ).openTooltip();

            var fill_key = mapData_ready[feature.id].fillKey;
            layer.on('mouseover', function (e) {
                // Unset highlight
                layer.setStyle({
                    'opacity': 0,
                    //'weight': 2.5,
                    'fill': true,
                    'fillColor': heatmap_colors[fill_key].color,
                    'fillOpacity': 0.3
                });
            });

            var color = heatmap_colors[fill_key].color;
            layer.on('mouseout', function (e) {
                var opacity = getCurrentOpacity();
                layer.setStyle({
                    'opacity': opacity,
                    'fillColor': color,
                    'fillOpacity': opacity
                });
            });

        }
    }

    function loadnewMapdata_RegionAreaharvestedMapProvince() {
        var geoBoundary = "reg_prov";

        createLeafletMap(leaflet_map_position_x, leaflet_map_position_y, geoBoundary);

        mapLayer = L.geoJSON(geojsonFeature_prov, {
            filter: function (feature) {
                if (mapData_ready[feature.id]) {
                    return true;
                } else {
                    return false;
                }
            },
            style: function (feature) {
                if (mapData_ready[feature.id]) {
                    var fill_key = mapData_ready[feature.id].fillKey;

                    return {
                        color: "#000000",
                        weight: 1,
                        fill: true,
                        fillColor: heatmap_colors[fill_key].color,
                        fillOpacity: 0.9
                    };

                } else {
                    return { fill: false, stroke: false };
                }

            },
            onEachFeature: onEachFeature_RegionAreaHarvestedMapProvince
        }).addTo(leaflet_map);

        // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
        updateRangeAppearance();

        // let geoJSON_current_bounds = mapLayer.getBounds();
        // leaflet_map.fitBounds(geoJSON_current_bounds);
        // leaflet_map.setMaxBounds(geoJSON_current_bounds);

        // putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

        // // Add custom zoom control at the top left
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(leaflet_map);

        // putLegendBtn(leaflet_map);

        // // Add extra info button
        // putDownloadBtn(leaflet_map);

        // Add extra info button
        // putInfoBtn(leaflet_map);
    }

    loadnewMapdata_RegionAreaharvestedMapProvince();
}


function createLeafletMap(leaflet_map_position_x, leaflet_map_position_y, geoBoundary = null) {

    // Check if the map exists and is initialized
    if (leaflet_map) {
        leaflet_map.remove();
        leaflet_map = null;
    }

    // Default center and zoom
    let zoom = 6;
    let defaultLat = 12.788929;
    let defaultLon = 121.938415;

    // Convert inputs to numbers
    var lat = parseFloat(leaflet_map_position_x);
    var lon = parseFloat(leaflet_map_position_y);
    if (isNaN(lat) || isNaN(lon)) {
        lat = defaultLat;
        lon = defaultLon;
    }

    // Adjust zoom based on boundary type
    if (geoBoundary === "reg_prov") zoom = 8;
    else if (geoBoundary === "prov_city") zoom = 9;


    leaflet_map = L.map('leafletmapmain', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10, // max zoom level
        minZoom: 5, // min zoom level
    }).setView([lat, lon], zoom); // default LAT AND LAN: 12.788929, 121.938415


    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map);

    // Optional rice area overlay
    var riceLayer = addRiceAreaLayer(leaflet_map);

    // Add zoom control at bottom-right
    L.control.zoom({ position: 'bottomleft' }).addTo(leaflet_map);

    // ✅ Add a button to toggle rice layer visibility
    addButtonBottomLeft(leaflet_map, riceLayer);

}

function createNoDataLeafletMap() {

    if (leaflet_map) {
        leaflet_map.remove();
        leaflet_map = null;
    }

    var lat = 12.788929;
    var lon = 121.938415;
    var zoom = 6;

    leaflet_map = L.map('leafletmapmain', {
        scrollWheelZoom: true,
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
        maxZoom: 10,
        minZoom: 6,
    }).setView([lat, lon], zoom);

    // Official
    // Official
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
    }).addTo(leaflet_map);

    // Optional rice area overlay
    var riceLayer = addRiceAreaLayer(leaflet_map);

    // Add zoom control at bottom-right
    L.control.zoom({ position: 'bottomleft' }).addTo(leaflet_map);
}


// 🧩 Function: Add Rice Area Layer
function addRiceAreaLayer(map) {

    // ✅ Create a custom pane for rice area layer
    if (!map.getPane('riceAreaPane')) {
        map.createPane('riceAreaPane');
        map.getPane('riceAreaPane').style.zIndex = 450; // Above basemap (tilePane=200), below overlays (overlayPane=400+)
    }

    // ✅ Assign the rice layer to the custom pane
    var riceAreaLayer = L.tileLayer(
        'https://ricelytics.philrice.gov.ph/descriptive_map/tiles/ricearea_tiles/RA_Tiles_2025Sem2/{z}/{x}/{y}.png',
        {
            pane: 'riceAreaPane', // important!
            maxZoom: 10,
            minZoom: 0,
            opacity: 0.8,
            tms: true
        }
    ).addTo(map);

    return riceAreaLayer;
}

// 🧩 Function: Add Layer Toggle Button
function addButtonBottomLeft(map, layer) {
    var control = L.control({ position: 'bottomleft' });

    control.onAdd = function () {
        var div = L.DomUtil.create('div', 'leaflet-bar');
        div.innerHTML = `
            <button id="toggleRiceLayerBtn" class="btn btn-sm"
                style="background-color: #297746; font-size: 14px; border: none; color: white;"
                title="Toggle Rice Area Layer">
                🌾 Rice Area
            </button>`;

        // Prevent map dragging when clicking
        L.DomEvent.disableClickPropagation(div);

        div.querySelector('#toggleRiceLayerBtn').addEventListener('click', function () {
            if (map.hasLayer(layer)) {
                map.removeLayer(layer);
                this.style.backgroundColor = 'transparent';
                this.style.color = '#297746'; // success green text when off
            } else {
                map.addLayer(layer);
                this.style.backgroundColor = '#297746'; // green background when on
                this.style.color = 'white'; // white text when on
            }
        });

        return div;
    };

    control.addTo(map);
}
