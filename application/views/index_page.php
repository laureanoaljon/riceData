<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rice Data</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FA Icons -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>resources/font-awesome-4.7.0/css/font-awesome.min.css">

  <link href="<?php echo base_url(); ?>resources/bootstrap5.min.css" rel="stylesheet"/>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

  <style>

	/* Add ./ in url in local */

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-Light.ttf') format('truetype');
		font-weight: 300;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-Regular.ttf') format('truetype');
		font-weight: 400;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-Medium.ttf') format('truetype');
		font-weight: 500;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-SemiBold.ttf') format('truetype');
		font-weight: 600;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-Bold.ttf') format('truetype');
		font-weight: 700;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-ExtraBold.ttf') format('truetype');
		font-weight: 800;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('/webfonts/Poppins-Black.ttf') format('truetype');
		font-weight: 900;
		font-style: normal;
	}

    * {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
		font-family: 'Poppins', sans-serif;
	}

	body {
		margin: 0;
		padding: 0;
		height: 100%;
		width: 100%;
		/* overflow: auto; */
		display: flex;
		flex-direction: column;
		font-family: 'Poppins', sans-serif;
		/* position: relative; */
		/* z-index: 0; */
	}

    #leafletmapmain {
		height: 100vh;
		width: 100%;
		position: relative;
		z-index: 1;
    }

	.filter-modal {
		top: 20px;
		width: 650px;
		overflow-y: auto;
		max-height: 80vh;
		left: 20px;
    }

	.right-modal .card {
		border-radius: 10px;
		transition: transform 0.2s ease;
	}

	.right-modal .card:hover {
		transform: translateY(-3px);
	}

	.filter-modal,
	.left-modal,
	.right-modal {
		position: absolute;
		z-index: 999; /* must be > 1000 and > map panes */
		background: white;
		border-radius: 10px;
		box-shadow: 0 4px 10px rgba(0,0,0,0.2);
		padding: 5px;
	}

	.right-modal {
		right: 20px;
		width: 600px;
		top: 110px;
		cursor: move;
		padding: 15px;
	}

	.pay-card{
		/* background-color: #2138b7;  */
		background: linear-gradient(to top, #7a8ff5, #4a5fd3, #2138b7);
		border: none !important;
		padding: 2px;
	}

	/* Side panel */
	.side-panel {
		position: fixed;
		top: 90px;
		left: 20px;
		width: 450px;
		background: transparent;
		border-radius: 10px;
		padding: 15px;
		z-index: 2000;
    }
	
    .map-layer-controls {
		display: none; /* hidden by default */
		padding: 10px;
		background: #fff;
		/* border: 1px solid #ddd; */
		border-radius: 8px;
		margin-top: 10px;
    }
    .action-button {
      width: 100%;
      text-align: left;
      margin-top: 8px;
	  border-radius: 12px;
    }

    .icon-container {
      display: inline-block;
      width: 25px;
    }

    .text-container {
      display: inline-block;
      vertical-align: middle;
    }

	.form-label
	.form-select
	.form-check-label{
		font-size: 0.8rem;
	}

	.add-btn{
		font-size: 0.8rem;
	}


  </style>
</head>
<body>

	<!-- Map container -->
	<div id="leafletmapmain"></div>

	<!-- Location Filter -->
	<div class="filter-modal rounded-pill px-3">
		<div class="form-group mb-1 mt-1 d-flex align-items-center gap-2">
			<img class="d-flex align-items-center justify-content-center" src="<?php echo base_url(); ?>assets/Department_of_Agriculture_of_the_Philippines.svg" height="50px" width="auto" title="DAC Logo">

			<!-- Dropdown -->
			<div class="custom-select-wrapper flex-grow-1 mx-2 position-relative">

				<select class="form-control form-select custom-select-arrow rounded-pill" id="selectLocation" style="height: 40px; width: 100%;">
					<option value="999" data-loc-type="2" selected>The Philippines</option>

					<?php
						// Output regions as options
						foreach ($regions as $region) {
						echo '<option value="' . $region['id'] . '" data-loc-type="' . $region['loc_type'] . '">' . $region['location_name'] . '</option>';
						}

						// Output provinces grouped under their respective regions
						$current_region = '';
						foreach ($provinces as $province) {
						if ($province['region_name'] !== $current_region) {
							if ($current_region !== '') {
							echo '</optgroup>';
							}
							$current_region = $province['region_name'];
							echo '<optgroup label="' . $current_region . '">';
						}
						echo '<option value="' . $province['province_id'] . '" data-loc-type="' . $province['loc_type'] . '">' . $province['province'] . '</option>';
						}
						if ($current_region !== '') {
						echo '</optgroup>';
						}
					?>
				</select>
			</div>

			<a href="#" class="btn d-flex align-items-center justify-content-center" type="button" id="resetBtn" style="height: 45px; width: 45px;" title="Reset Map">
				<span>
					<i class="fa fa-search" aria-hidden="true"></i>
				</span>
			</a>

			<a href="#" class="btn d-flex align-items-center justify-content-center" type="button" id="resetBtn" style="height: 45px; width: 45px;" title="Reset Map">
				<span>
					<i class="fa fa-database" aria-hidden="true"></i>
				</span>
			</a>

			<!-- Search Button   -->
			<!--<button class="btn btn-primary d-flex align-items-center justify-content-center" type="button" id="goBtn" style="height: 45px; width: 45px;">
				<i class="fa fa-search"></i>
			</button> -->
		</div>

	</div>

	<!-- Fixed Container for Left Controls -->
	<div class="side-panel">
		<!-- Button 1 -->
		<button class="btn btn-success action-button" id="load_pay">
			<div class="icon-container text-white"><i class="fa fa-line-chart"></i></div>
			<div class="text-container">Production, Area and Yield Maps</div>
		</button>

		<!-- Map controls -->
		<div class="map-layer-controls" id="pay_layer_controls">
			<p><b>Select Filter:</b></p>

			<div class="container-fluid px-3">
				<!-- Year -->
				<div class="row align-items-center mb-3">
					<div class="col-3">
					<h6 class="mb-0 form-label">Year:</h6>
					</div>
					<div class="col-9">
					<select class="form-select form-select-sm" id="payYearFilter">
						<option class="text-success" value="" disabled>Select Option</option>
						<option value="2024" selected>2024</option>
						<option value="2023">2023</option>
						<option value="2022">2022</option>
						<option value="2021">2021</option>
						<option value="2020">2020</option>
						<option value="2019">2019</option>
						<option value="2018">2018</option>
					</select>
					</div>
				</div>

				<!-- Period -->
				<div class="row align-items-center mb-3">
					<div class="col-3">
					<h6 class="mb-0 form-label">Period:</h6>
					</div>
					<div class="col-9">
					<select class="form-select form-select-sm" id="payPeriodFilter">
						<option class="text-success" value="" disabled>Select Option</option>
						<option value="1" data-year-type="1" selected>Annual</option>
						<option value="1" data-year-type="2">Semester 1</option>
						<option value="2" data-year-type="2">Semester 2</option>
						<option value="1" data-year-type="3">Quarter 1</option>
						<option value="2" data-year-type="3">Quarter 2</option>
						<option value="3" data-year-type="3">Quarter 3</option>
						<option value="4" data-year-type="3">Quarter 4</option>
					</select>
					</div>
				</div>

				<!-- Another Period (example) -->
				<div class="row align-items-center mb-3">
					<div class="col-3">
					<h6 class="mb-0 form-label">Ecosystem:</h6>
					</div>
					<div class="col-9">
					<select class="form-select form-select-sm" id="payEcosystemFilter">
						<option class="text-success" value="" disabled>Select Option</option>
						<option value="2" selected>All Ecosystems</option>
						<option value="1">Irrigated Areas</option>
						<option value="0">Non-Irrigated Areas</option>      
					</select>
					</div>
				</div>

				<!-- Radio buttons -->
				<div class="row mt-2 text-center">
					<div class="col-12">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="pay_layers" id="layer_production" value="production" checked>
						<label class="form-check-label" for="layer_production">Production</label>
					</div>

					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="pay_layers" id="layer_area" value="area_raster">
						<label class="form-check-label" for="layer_area">Area Harvested</label>
					</div>

					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="pay_layers" id="layer_yield" value="yield">
						<label class="form-check-label" for="layer_yield">Yield</label>
					</div>
					</div>
				</div>
			</div>

			
			<div class="text-center mt-3 mb-3">
				<button class="btn btn-success btn-sm w-50 add-btn" id="addBtn" style="border-radius: 12px;" data-layer="pay">
					<i class="fa fa-plus"></i> &emsp;Add
				</button>
			</div>
		</div>

		<!-- Button 2 -->
		<!-- <button class="btn btn-success action-button" id="load_land">
			<div class="icon-container"><i class="fa fa-mountain"></i></div>
			<div class="text-container">Land Maps</div>
		</button>

		<div class="map-layer-controls" id="land_layer_controls">
			<p><b>Select Layer:</b></p>
			<div class="form-check">
			<input class="form-check-input" type="radio" name="land_layers" id="land1" value="land_cover" checked>
			<label class="form-check-label" for="land1">Land Cover</label>
			</div>
			<div class="form-check">
			<input class="form-check-input" type="radio" name="land_layers" id="land2" value="soil_type">
			<label class="form-check-label" for="land2">Soil Type</label>
			</div>
			<div class="form-check">
			<input class="form-check-input" type="radio" name="land_layers" id="land3" value="soil_nutrients">
			<label class="form-check-label" for="land3">Soil Nutrient</label>
			</div>
			<button class="btn btn-success-info btn-sm w-100 mt-2" id="add_land_map">
			<i class="fas fa-circle-plus"></i> Add Map
			</button>
		</div> -->

		<!-- Other buttons (no controls yet) -->
		<!-- <button class="btn btn-success action-button" id="load_water">
			<div class="icon-container"><i class="fa fa-water"></i></div>
			<div class="text-container">Water Resource Maps</div>
		</button>

		<button class="btn btn-success action-button" id="load_climate">
			<div class="icon-container"><i class="fa fa-cloud-sun"></i></div>
			<div class="text-container">Climate Maps</div>
		</button>

		<button class="btn btn-success action-button" id="load_damages">
			<div class="icon-container"><i class="fa fa-triangle-exclamation"></i></div>
			<div class="text-container">Damages Maps</div>
		</button> -->
	</div>

  	<!-- Right Floating Modal -->
	<div class="right-modal" id="rightModal">
		<div class="modal-header d-flex justify-content-between align-items-center mb-2">
			<h5 class="mb-0">Rice Data Description</h5>
			<button class="btn btn-sm btn-outline-primary" id="rightBtn">
			<i class="fa fa-refresh"></i> Refresh
			</button>
		</div>

		<div class="modal-body">
			<p class="text-muted mb-3">
				Key indicators of the rice industry based on the latest data.
			</p>

			<div class="row g-3 mt-n1 px-0 text-center">
			
			<!-- Palay Production -->
			<div class="col-4">
				<a href="#" id="palay-production-link" class="text-decoration-none">
				<div class="card card-btn text-center pay-card bg-success">
					<div class="card-body p-2">
					<div class="col-12 d-flex align-items-center justify-content-between">
						<h6 class="card-title mb-1 mt-2 text-white text-start" style="font-size: 0.9rem;" id="palayproduction_data_datelabel">Palay Production</h6>
						<span class="mt-1"><i class="fa fa-chevron-right text-white"></i></span>
					</div>
					<h1 class="value-np fw-bold mb-0 mt-n2 text-white" id="palay_production_value">19.09</h1>
					<div class="mx-auto mt-1">
						<p class="text-note-pay"><span class="badge bg-warning px-2">million metric tons</span></p>
					</div>
					</div>
				</div>
				</a>
			</div>

			<!-- Area Harvested -->
			<div class="col-4">
				<a href="#" id="area-harvested-link" class="text-decoration-none">
				<div class="card card-btn text-center pay-card bg-primary">
					<div class="card-body p-2">
					<div class="col-12 d-flex align-items-center justify-content-between">
						<h6 class="card-title mb-1 mt-2 text-white text-start" style="font-size: 0.9rem;" id="areaharvested_data_datelabel">Area Harvested</h6>
						<span class="mt-1"><i class="fa fa-chevron-right text-white"></i></span>
					</div>
					<h1 class="value-np fw-bold mb-0 mt-n2 text-white" id="area_harvested_value">4.65</h1>
					<div class="mx-auto mt-1">
						<p class="text-note-pay"><span class="badge bg-warning px-2">million hectares</span></p>
					</div>
					</div>
				</div>
				</a>
			</div>

			<!-- Average Yield -->
			<div class="col-4">
				<a href="#" id="yield-link" class="text-decoration-none">
				<div class="card card-btn text-center pay-card bg-info">
					<div class="card-body p-2">
					<div class="col-12 d-flex align-items-center justify-content-between">
						<h6 class="card-title mb-1 mt-2 text-white text-start" style="font-size: 0.9rem;" id="averageyield_data_datelabel">Average Yield</h6>
						<span class="mt-1"><i class="fa fa-chevron-right text-white"></i></span>
					</div>
					<h1 class="value-np fw-bold mb-0 mt-n2 text-white" id="average_yield_value">4.11</h1>
					<div class="mx-auto mt-1">
						<p class="text-note-pay"><span class="badge bg-warning px-2">metric tons per hectare</span></p>
					</div>
					</div>
				</div>
				</a>
			</div>

			</div>
		</div>
	</div>

	<!-- jQuery (must come before your script) -->
	<script src="<?php echo base_url(); ?>resources/jquery-3.6.4.min.js"></script>

  	<!-- JS Libraries -->
  	<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
  	<script src="<?php echo base_url(); ?>resources/bootstrap5.min.js"></script>
	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


	<!-- GEOJSON -->
	<!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/phl.city.js"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-city-geo.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-reg-geo-james.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-prov-geo.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-regcity-geo-james.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-provcity-geo.js"></script>

	<script>
		// Initially hide right modal
		$('#rightModal').hide();

		function makeDraggable(modal) {
			let offsetX, offsetY, isDragging = false;

			modal.addEventListener('mousedown', (e) => {
				isDragging = true;
				offsetX = e.clientX - modal.getBoundingClientRect().left;
				offsetY = e.clientY - modal.getBoundingClientRect().top;
				modal.style.zIndex = 3000; // bring on top while dragging
			});

			document.addEventListener('mousemove', (e) => {
				if (isDragging) {
				modal.style.left = (e.clientX - offsetX) + 'px';
				modal.style.top = (e.clientY - offsetY) + 'px';
				}
			});

			document.addEventListener('mouseup', () => {
				isDragging = false;
			});
		}

		document.addEventListener('DOMContentLoaded', () => {
			document.querySelectorAll('.left-modal, .right-modal').forEach(makeDraggable);
		});


		// Toggle visibility of corresponding map controls
		$(function() {
			$('#load_pay').click(function() {
				$('#pay_layer_controls').slideToggle();
				$('#land_layer_controls').slideUp();
			});
			
			$('#load_land').click(function() {
				$('#land_layer_controls').slideToggle();
				$('#pay_layer_controls').slideUp();
			});
			
			$('#load_water, #load_climate, #load_damages').click(function() {
				$('.map-layer-controls').slideUp();
			});
		});



		// FOR CREATE MAP #######################################################################

		// Default lat/lon/zoom for PH
		const lat = 12.788929;
		const lon = 121.938415;
		const zoom = 6;

		// Initialize Leaflet map
		var leaflet_map = L.map('leafletmapmain', {
			scrollWheelZoom: true,
			preferCanvas: true,
			zoomControl: false,
			attributionControl: false,
			maxZoom: 8,
			minZoom: 6
		}).setView([lat, lon], zoom);

		// Add zoom control manually at bottom right
		L.control.zoom({
			position: 'bottomright' // can be 'topleft', 'topright', 'bottomleft', or 'bottomright'
		}).addTo(leaflet_map);

		// Base map - CARTO Voyager (no labels)
		L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
			subdomains: 'abcd',
		}).addTo(leaflet_map);

		// Custom Rice Area Tile Overlay
		var riceAreaLayer = L.tileLayer(
			'https://ricelytics.philrice.gov.ph/descriptive_map/tiles/ricearea_tiles/RA_Tiles_2025Sem2/{z}/{x}/{y}.png',
		{
			maxZoom: 9,
			minZoom: 6,
			opacity: 0.9,
			tms: true,
			// errorTileUrl: 'https://tile.openstreetmap.org/6/63/35.png'
		}
		).addTo(leaflet_map);


		$('#addBtn').on('click', function() {

			const locCode = $('#selectLocation').val();
            const locType = $('#selectLocation option:selected').data('loc-type');

			// Read data attributes
			const layer = $(this).data('layer');

			console.log('Clicked:', { layer });

			let currentDataLayer = null;

			// Example: use in logic
			if (layer === 'pay') {
				alert('You selected PAY layer!');
				$('#rightModal').show();

				var selectedYear = $('#payYearFilter').val();
				var selectedPeriod = $('#payPeriodFilter').val();
				var selectedYearType = $('#payPeriodFilter option:selected').data('year-type');
				var selectedPeriodText = $('#payPeriodFilter option:selected').text(); // optional
				var selectedEcosystem = $('#payEcosystemFilter').val();
				var selectedLayer = $('input[name="pay_layers"]:checked').val();

				$.ajax({
					url: "<?php echo base_url(); ?>fetch/get_data",
					method: 'POST',
					dataType: "JSON",
					data: {
						year: selectedYear,
						ecosystem: selectedEcosystem,
						period: selectedPeriod,
						year_type: selectedYearType,
					},
					success: function(response) {
						// console.log(response);

						var dbRegsMap = JSON.parse(response['regional_production_geocoded']);
						var reg_y3 = JSON.parse(response['regional_production_y3']);

						if (dbRegsMap && Object.keys(dbRegsMap).length > 0) {
							regionalProductionMap(dbRegsMap, reg_y3, null, selectedPeriodText);
						} else {
							$.confirm({
								title: '<span class="text-warning">Message</span>',
								content: "No Available data.",
								theme: 'supervan',
								type: 'green',
								buttons: {
									OK: function () {
										createNoDataLeafletMap();
									}
								}
							});
						}

					},
					error: function (request, status, error) {
						console.log('Error: ', error);
						console.log('Status: ', status);
						console.log('Response: ', request.responseText);
						alert('Error occurred while fetching data.');
					}
				});


			}
		});

		// END FOR CREATE MAP #######################################################################
		
		
		document.getElementById('rightBtn').addEventListener('click', () => {
			alert('Right modal button clicked!');
		});


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

		// MAPPPPPPP CODE
		function regionalProductionMap(dbRegsMapData, reg_y3Data, locationCoordinatesData = null, periodText = null){
                // // MAP CODE PRODUCTION
                // GET DATA
                // var dbRegsMap = dbRegsMapData
                var reg_y3 = reg_y3Data;

                var dbRegsMap = dbRegsMapData.filter(function (item) {
                    // Check if palay_value is not null, undefined, or empty, and cast to integer
                    return item.value !== null && item.value !== undefined && item.value !== '0';
                });

                // region - annual
                var quartile1 = 500000;
                var quartile2 = 1000000;
                var quartile3 = 2000000;

                if (periodText != "ANNUAL"){
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

                if (locationCoordinatesData){
                    var coord = locationCoordinatesData;  
                    var x = coord['longitude'];
                    var y = coord['latitude'];
                    var zoom = coord['zoom']; 

                    var leaflet_map_position_x = coord['latitude'];
                    var leaflet_map_position_y = coord['longitude'];
                }

                // COMMENT OUT
                // let previousLayer = null; // Variable to store the previously clicked polygon layer

                function onEachFeature_reg(feature, layer) {
                    
                    if(mapDataR_ready[feature.id]){
                        var value = mapDataR_ready[feature.id].Value;
                        var percentage = mapDataR_ready[feature.id].Percentage;

                        layer.bindTooltip(
                            "<strong>" + mapDataR_ready[feature.id].Location_name + 
                            ": " + value + " mt (" + mapDataR_ready[feature.id].Year + 
                            ")</strong><br><strong>Percent Share to Total Production: " + percentage + "%</strong>"
                        ).openTooltip();

                        var fill_key = mapDataR_ready[feature.id].fillKey;
                        layer.on('mouseover', function(e) {
                            // Unset highlight
                            layer.setStyle({
                                'opacity':0,
                                //'weight': 2.5,
                                'fill': true,
                                'fillColor': heatmap_colors[fill_key].color,
                                'fillOpacity': 0.3
                            });
                        });

                        var color = heatmap_colors[fill_key].color;
                        layer.on('mouseout', function(e) {
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

                    createLeafletMap(leaflet_map_position_x, leaflet_map_position_y);

                    mapLayer =  L.geoJSON(geojsonFeature_reg,{
                        style: function(feature) {

                            if(mapDataR_ready[feature.id]){
                                
                                var fill_key = mapDataR_ready[feature.id].fillKey;
                                return {color: "#000000",weight: 1, fill: true, fillColor: heatmap_colors[fill_key].color, fillOpacity: 0.9};      
                            }else{
                                //  return {color: "#000000",weight: 0.5,opacity:0.5, fill: true, fillColor: heatmap_colors['defaultFill'].color, fillOpacity: 0.9}; 
                                return { fill: false, stroke:false}; 
                            }
                        },onEachFeature:onEachFeature_reg
                    }).addTo(leaflet_map);

                    // Call updateRangeAppearance() after adding the layer to ensure it reflects the initial opacity value
                    updateRangeAppearance();

                    let geoJSON_current_bounds = mapLayer.getBounds();
                    // leaflet_map.fitBounds(geoJSON_current_bounds);
                    // leaflet_map.setMaxBounds(geoJSON_current_bounds);

                    putResetZoomBtn(mapLayer, leaflet_map, leaflet_map_position_x, leaflet_map_position_y);

                    // Add custom zoom control at the top left
                    L.control.zoom({
                        position: 'topright'
                    }).addTo(leaflet_map);
                            
                    // putLegendBtn(leaflet_map);

                    // // Add extra info button
                    // putDownloadBtn(leaflet_map);

                    // Add extra info button
                    // putInfoBtn(leaflet_map);
                }

                loadRegionalProductionMap();
            }


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

				leaflet_map = L.map('leafletmapmain', {
					scrollWheelZoom: true,
					preferCanvas: true,
					zoomControl: false,
					attributionControl: false,
					maxZoom: 10, // max zoom level
					minZoom: 6, // min zoom level
				}).setView([lat , lon], zoom); // default LAT AND LAN: 12.788929, 121.938415


				// Official
				L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
					maxZoom: 19,
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
					subdomains: 'abcd',
				}).addTo(leaflet_map);

			}

			// Dynamically decide the rounding factor based on the values
			function dynamicRound(value, scale = null) {
				let magnitude = Math.pow(10, Math.floor(Math.log10(value)) - scale); // Adjust "-2" to control rounding scale
				return Math.round(value / magnitude) * magnitude;
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

			function numberWithCommas(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}


  	</script>

</body>
</html>
