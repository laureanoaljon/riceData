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

  <!-- Loading Overlay -->
  <script src="<?php echo base_url(); ?>resources/loadingoverlay.min.js"></script>

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
		width: 650px;
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
		width: 420px;
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
		box-shadow: 0 4px 10px rgba(0,0,0,0.2);
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
		font-size: 0.7rem;
	}

	.add-btn{
		font-size: 0.8rem;
	}

	.legend-box {
		width: 25px;
		height: 12px;
		margin: 0 5px;
		display: inline-block;
	}

	/* Loading Overlay Styles */
	#loadingOverlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(52, 100, 70, 0.6); /* green semi-transparent */
		color: white;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		z-index: 9999;
		font-family: Arial, sans-serif;
	}

	/* Spinner Animation */
	.spinner {
		border: 4px solid #fff;
		border-top: 4px solid #297746;
		border-radius: 50%;
		width: 50px;
		height: 50px;
		animation: spin 1s linear infinite;
		margin-bottom: 10px;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}


  </style>
</head>
<body>

	<!-- Loading Overlay -->
	<div id="loadingOverlay">
		<div class="spinner"></div>
		<p>Loading. Please wait ...</p>
	</div>


	<!-- Map container -->
	<div id="leafletmapmain"></div>

	<!-- Location Filter -->
	<div class="filter-modal rounded-pill px-3">
		<div class="form-group mb-1 mt-1 d-flex align-items-center gap-2">
			<img class="d-flex align-items-center justify-content-center" src="<?php echo base_url(); ?>assets/Department_of_Agriculture_of_the_Philippines.svg" height="50px" width="auto" title="DA Logo">

			<img class="d-flex align-items-center justify-content-center" src="<?php echo base_url(); ?>assets/DAC-Logo.png" height="45px" width="auto" title="DAC Logo">

			<!-- Dropdown -->
			<div class="custom-select-wrapper flex-grow-1 mx-2 position-relative">

				<select class="form-control form-select custom-select-arrow rounded-pill" id="selectLocation" style="height: 40px; width: 100%;">
					<option value="999" data-loc-type="2" data-psgc="PHL" selected>The Philippines</option>

					<?php
						// Output regions as options
						foreach ($regions as $region) {
							echo '<option value="' . $region['id'] . '" 
										data-loc-type="' . $region['loc_type'] . '" 
										data-psgc="' . $region['psgc_code'] . '">' 
										. $region['location_name'] . 
								'</option>';
						}

						// Output provinces grouped under their respective regions
						$current_region = '';
						foreach ($provinces as $province) {
							if ($province['region_name'] !== $current_region) {
								if ($current_region !== '') {
									echo '</optgroup>';
								}
								$current_region = $province['region_name'];
								echo '<optgroup label="' . htmlspecialchars($current_region) . '">';
							}

							echo '<option value="' . $province['province_id'] . '" 
										data-loc-type="' . $province['loc_type'] . '" 
										data-psgc="' . $province['psgc_code'] . '">' 
										. htmlspecialchars($province['province']) . 
								'</option>';
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

	<!-- Side Panel -->
    <?php echo $side_panel; ?>

  	<!-- Right Floating Modal -->
	<div class="right-modal" id="rightModal">
		<div class="modal-header d-flex justify-content-between align-items-center mb-2">
			<h6 class="mb-0">Data Description</h6>
			<!-- <button class="btn btn-sm btn-outline-primary" id="rightBtn">
				<i class="fa fa-refresh"></i> Refresh
			</button> -->
		</div>

		<div class="modal-body">
			<!-- <p class="text-muted mb-3">
				Key indicators of the rice industry based on the latest data.
			</p> -->

			<!-- <div class="row g-3 mt-n1 px-0 text-center">
			
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

			</div> -->

			<div class="row">
				<div class="col-12 text-center mt-1">

					<div class="row mt-1 mb-n1">
						<div class="col-12">
							<div class="px-0" style="font-size: 0.83rem;">
								<div class="legend-box mt-0" id="legend-box-1" style="background:#FF7F00;"></div>
								<p id="series-1" class="d-inline-block text-retain"></p>
								<div class="legend-box" id="legend-box-2" style="background:#FFD92F;"></div>
								<p id="series-2" class="d-inline-block text-retain"></p>
								<div class="legend-box" id="legend-box-3" style="background:#4DAF4A;"></div>
								<p id="series-3" class="d-inline-block text-retain"></p>
								<div class="legend-box" id="legend-box-4" style="background:#1F78B4;"></div>
								<p id="series-4" class="d-inline-block text-retain"></p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row mt-2 mb-0" style="font-size: 0.75rem;">
				<div class="col-2">
					<p id="opacityValue" class="font-weight-bold">Opacity: 90%</p>
				</div>
				<div class="col-10">
					<div class="px-2 mb-0">
						<input class="mb-0 slider success-slider" type="range" id="opacity" name="opacity" min="0" max="100" value="90" class="" style="width:100%;">
					</div>
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
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-city-geo.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-reg-geo-james.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-prov-geo.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-regcity-geo-james.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-provcity-geo.js"></script> -->

	<script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-geo-reg-psgc.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-geo-prov-psgc.js"></script>

	<script type="text/javascript" src="<?php echo base_url(); ?>js/maps.js"></script>

	<script>
		// ###################### Initially hide right modal ##########################
		$('#rightModal').hide();

		// Hide overlay after 2 seconds
		setTimeout(function() {
			$('#loadingOverlay').fadeOut();
		}, 500);


		// ##################### CODE FOR DRAGGABLE MODAL ##########################
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

		// document.addEventListener('DOMContentLoaded', () => {
		// 	document.querySelectorAll('.left-modal, .right-modal').forEach(makeDraggable);
		// });


		// ##################### TOGGLE LAYER CONTROLS ##########################

		// Toggle visibility of corresponding map controls
		$(function() {
			$('#load_pay').click(function() {
				$('#pay_layer_controls').slideToggle();
				$('#pad_layer_controls').slideUp();
			});
			
			$('#load_pad').click(function() {
				$('#pad_layer_controls').slideToggle();
				$('#pay_layer_controls').slideUp();
			});
			
			// $('#load_water, #load_climate, #load_damages').click(function() {
			// 	$('.map-layer-controls').slideUp();
			// });
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

		// Add the custom rice area overlay by default
		addRiceAreaLayer(leaflet_map);


		// ############### ADD BUTTON CLICK EVENT #######################
		$('#addBtn').on('click', function() {

			// Show overlay
			$('#loadingOverlay').show();

			// Hide overlay after 2 seconds
			setTimeout(function() {
				$('#loadingOverlay').fadeOut();
			}, 1500);


			const locCode = $('#selectLocation').val();
            const locType = $('#selectLocation option:selected').data('loc-type');

			// Read data attributes
			const layer = $(this).data('layer');

			console.log('Clicked:', { layer });

			let currentDataLayer = null;

			// Example: use in logic
			if (layer === 'pay') {
				// alert('You selected PAY layer!');
				$('#rightModal').show();

				var selectedYear = $('#payYearFilter').val();
				var selectedPeriod = $('#payPeriodFilter').val();
				var selectedYearType = $('#payPeriodFilter option:selected').data('year-type');
				var selectedPeriodText = $('#payPeriodFilter option:selected').text(); // optional
				var selectedEcosystem = $('#payEcosystemFilter').val();
				var selectedLayer = $('input[name="pay_layers"]:checked').val();

				// alert(selectedLayer);

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
						console.log(response);

						var dbRegsMap = [];

						if (selectedLayer === 'production') {
							dbRegsMap = JSON.parse(response['regional_production_geocoded']);
						} else if (selectedLayer === 'yield') {
							dbRegsMap = JSON.parse(response['regional_yield_geocoded']);
						} else if (selectedLayer === 'area_harvested') {
							dbRegsMap = JSON.parse(response['regional_area_geo']);	
						}


						if (dbRegsMap && Object.keys(dbRegsMap).length > 0) {
							if (selectedLayer === 'production') {
								regionalProductionMap(dbRegsMap, null, selectedPeriodText);
								$('#legend-box-5').remove();
							} else if (selectedLayer === 'area_harvested') {
								regionalAreaHarvestedMap(dbRegsMap, null, selectedPeriodText);
								$('#legend-box-5').remove();
							} else if (selectedLayer === 'yield') {
								regionalYieldMap(dbRegsMap);
							}
							
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
		
		
		// document.getElementById('rightBtn').addEventListener('click', () => {
		// 	alert('Right modal button clicked!');
		// });


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
