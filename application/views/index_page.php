<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rice Data</title>

  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link href="<?php echo base_url(); ?>resources/bootstrap5.min.css" rel="stylesheet"/>

  <!-- FA Icons -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>resources/font-awesome-4.7.0/css/font-awesome.min.css">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>resources/leaflet.css"/>

  <style>

	/* Add ./ in url in local */

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-Light.ttf') format('truetype');
		font-weight: 300;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-Regular.ttf') format('truetype');
		font-weight: 400;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-Medium.ttf') format('truetype');
		font-weight: 500;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-SemiBold.ttf') format('truetype');
		font-weight: 600;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-Bold.ttf') format('truetype');
		font-weight: 700;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-ExtraBold.ttf') format('truetype');
		font-weight: 800;
		font-style: normal;
	}

	@font-face {
		font-family: 'Poppins';
		src: url('./webfonts/Poppins-Black.ttf') format('truetype');
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
		top: 20px;
		/* cursor: move; */
		padding: 15px;
	}

	.pay-card{
		/* background-color: #2138b7;  */
		/* background: linear-gradient(to top, #7a8ff5, #4a5fd3, #2138b7); */
		background: linear-gradient(to top, #668b54ff, #2f7336);
		border: none !important;
		padding: 2px;
	}

	/* Side panel */
	.side-panel {
		position: fixed;
		top: 90px;
		left: 20px;
		width: 430px;
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

			<a href="#" class="btn d-flex align-items-center justify-content-center" type="button" id="searchBtn" style="height: 45px; width: 45px;" title="Search">
				<span>
					<i class="fa fa-search" aria-hidden="true"></i>
				</span>
			</a>

			<!-- <a href="#" class="btn d-flex align-items-center justify-content-center" type="button" id="resetBtn" style="height: 45px; width: 45px;" title="Reset Map">
				<span>
					<i class="fa fa-database" aria-hidden="true"></i>
				</span>
			</a> -->

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
			<h5 class="mb-0" id="title-description"></h5>
			<!-- <button class="btn btn-sm btn-outline-primary" id="rightBtn">
				<i class="fa fa-refresh"></i> Refresh
			</button> -->
		</div>

		<div class="modal-body" style="max-height: 850px; overflow-y: auto;">

			<div id="cards-container" class="mb-3">
				<div class="row g-3 mt-n1 px-0 text-center">
			
					<div class="col-4">
						<a href="#" id="palay-production-link" class="text-decoration-none">
							<div class="card card-btn text-center pay-card">
								<div class="card-body p-2">
									<div class="col-12 d-flex align-items-center justify-content-between">
										<h6 class="card-title mb-1 mt-2 text-white text-start" style="font-size: 0.8rem;" id="palayproduction_label"></h6>
										<!-- <span class="mt-1"><i class="fa fa-chevron-right text-white"></i></span> -->
									</div>
									<h1 class="value-np fw-bold mb-0 mt-n2 text-white" id="palayproduction_value"></h1>
									<div class="mx-auto mt-1">
										<p class="text-note-pay" style="font-size: 0.8rem;"><span class="badge bg-warning px-2" id="palayproduction_unit"></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>

					<div class="col-4">
						<a href="#" id="area-harvested-link" class="text-decoration-none">
							<div class="card card-btn text-center pay-card">
								<div class="card-body p-2">
									<div class="col-12 d-flex align-items-center justify-content-between">
										<h6 class="card-title mb-1 mt-2 text-white text-start" style="font-size: 0.8rem;" id="areaharvested_label"></h6>
										<!-- <span class="mt-1"><i class="fa fa-chevron-right text-white"></i></span> -->
									</div>
									<h1 class="value-np fw-bold mb-0 mt-n2 text-white" id="areaharvested_value"></h1>
									<div class="mx-auto mt-1">
										<p class="text-note-pay" style="font-size: 0.8rem;"><span class="badge bg-warning px-2" id="areaharvested_unit"></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>

					<div class="col-4">
						<a href="#" id="yield-link" class="text-decoration-none">
							<div class="card card-btn text-center pay-card">
								<div class="card-body p-2">
									<div class="col-12 d-flex align-items-center justify-content-between">
										<h6 class="card-title mb-1 mt-2 text-white text-start" style="font-size: 0.8rem;" id="averageyield_label"></h6>
										<!-- <span class="mt-1"><i class="fa fa-chevron-right text-white"></i></span> -->
									</div>
									<h1 class="value-np fw-bold mb-0 mt-n2 text-white" id="averageyield_value"></h1>
									<div class="mx-auto mt-1">
										<p class="text-note-pay" style="font-size: 0.8rem;"><span class="badge bg-warning px-2" id="averageyield_unit"></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>

				</div>
			</div>

			<div class="row mt-2">
				<div class="col-12 text-center mt-1">

					<div class="row mt-1 mb-n1">
						<div class="col-12">
							<div class="px-2" style="font-size: 0.75rem;">
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

			<div id="barchart-container" class="mt-0 mb-4">
				<canvas id="barChart" style="width:100%; height:550px;"></canvas>
			</div>

			<!-- <div class="pagination text-center mt-2">
				<button id="prevPage" class="btn btn-sm btn-outline-primary">Previous</button>
				<span id="pageInfo" class="mx-2"></span>
				<button id="nextPage" class="btn btn-sm btn-outline-primary">Next</button>
			</div> -->

			<div class="row mt-4 mb-n3" style="font-size: 0.7rem;">
				<div class="col-2">
					<p id="opacityValue" class="font-weight-bold">Map Opacity: 90%</p>
				</div>
				<div class="col-10">
					<div class="px-2 mb-0">
						<input class="mb-0 slider success-slider" type="range" id="opacity" name="opacity" min="0" max="100" value="90" class="" style="width:100%;">
					</div>
				</div>
			</div>
			
		</div>

		<div class="modal-footer d-flex justify-content-between align-items-center">
			<p id="data-source" style="font-size: 0.9rem"></p>
		</div>
	</div>

	<!-- Chart JS -->
	<script src="<?php echo base_url(); ?>resources/chart.umd.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>


	<!-- jQuery (must come before your script) -->
	<script src="<?php echo base_url(); ?>resources/jquery-3.6.4.min.js"></script>

	<!-- Loading Overlay -->
    <script src="<?php echo base_url(); ?>resources/loadingoverlay.min.js"></script>

  	<!-- JS Libraries -->
  	<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
  	
	<script src="<?php echo base_url(); ?>resources/bootstrap5.min.js"></script>
	<script src="<?php echo base_url(); ?>resources/leaflet.js"></script>

	<!-- SweetAlert2 -->
	<script src="<?php echo base_url(); ?>resources/sweet-alert.js"></script>

	<!-- GEOJSON -->
	<!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/phl.city.js"></script> -->
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-city-geo.js"></script> -->
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-reg-geo-james.js"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-prov-geo.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-regcity-geo-james.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-provcity-geo.js"></script>

	<script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-geo-reg-psgc.js"></script>
	<!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/maps_coordinates/phl-geo-prov-psgc.js"></script>  -->

	<!-- Custom JS file for map -->
	<script type="text/javascript" src="<?php echo base_url(); ?>js/maps.js"></script>

	<script>
		// ###################### Initially hide right modal ##########################
		$('#rightModal').hide();

		// ###################### Hide overlay after 2 seconds
		setTimeout(function() {
			$('#loadingOverlay').fadeOut();
		}, 500);

		// ######################## Hide all map-layer-controls initially
		$('.action-button').on('click', function () {
			// Reset all chevrons to down
			$('.action-button i.fa-chevron-up')
				.removeClass('fa-chevron-up')
				.addClass('fa-chevron-down');
			
			// Toggle the clicked one
			const $icon = $(this).find('i.fa-chevron-down, i.fa-chevron-up');
			$icon.toggleClass('fa-chevron-down fa-chevron-up');
		});


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
			position: 'bottomleft' // can be 'topleft', 'topright', 'bottomleft', or 'bottomright'
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
		$('#addBtn, #searchBtn').on('click', function() {

			const openLayer = $('.map-layer-controls:visible').attr('id');
			// Read data attributes


			if (window.barChartInstance) {
				window.barChartInstance.destroy();
			}

			if (!openLayer) {
				Swal.fire({
					title: 'No Layer Selected',
					text: 'Please select a layer to load data.',
					icon: 'success',
					showConfirmButton: false,
					timer: 2500, // Auto-close after 1 second
					timerProgressBar: true
				});
				return;
			}

			// Show overlay
			$('#loadingOverlay').show();

			// Hide overlay after 2 seconds
			setTimeout(function() {
				$('#loadingOverlay').fadeOut();
			}, 1000);

			const locCode = $('#selectLocation').val();
			const locName = $('#selectLocation option:selected').text();
            const locType = $('#selectLocation option:selected').data('loc-type');
			const psgcCode = $('#selectLocation option:selected').data('psgc');


			let currentDataLayer = null;

			// ################################################################ FOR PAY LAYER
			if (openLayer === 'pay_layer_controls') {
				// alert('You selected PAY layer!');
				$('#rightModal').show();

				var selectedYear = $('#payYearFilter').val();
				var selectedPeriod = $('#payPeriodFilter').val();
				var selectedYearType = $('#payPeriodFilter option:selected').data('year-type');
				var selectedPeriodText = $('#payPeriodFilter option:selected').text(); // optional
				var selectedEcosystem = $('#payEcosystemFilter').val();
				var selectedLayer = $('input[name="pay_layers"]:checked').val();

				// alert(selectedLayer);


				if (selectedLayer === 'production') {
					$('#title-description').text('Palay Production in ' + locName);
				} else if (selectedLayer === 'area_harvested') {
					$('#title-description').text('Area Harvested in ' + locName);
				} else if (selectedLayer === 'yield') {
					$('#title-description').text('Average Yield in ' + locName);
				}

				// ########################################################################## FOR NATIONAL LEVEL
				if (psgcCode === 'PHL') {

					$.ajax({
						url: "<?php echo base_url(); ?>fetch/get_data",
						method: 'POST',
						dataType: "JSON",
						data: {
							year: selectedYear,
							ecosystem: selectedEcosystem,
							period: selectedPeriod,
							year_type: selectedYearType,
							psgc_code: psgcCode
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
									// regionalProductionMap(dbRegsMap, null, selectedPeriodText);
									renderProductionMap('regional', dbRegsMap, null, selectedPeriodText);
									$('#series-5').remove();
									$('#legend-box-5').remove();
								} else if (selectedLayer === 'area_harvested') {
									// regionalAreaHarvestedMap(dbRegsMap, null, selectedPeriodText);
									renderAreaHarvestedMap('regional', dbRegsMap, null, selectedPeriodText);
									$('#series-5').remove();
									$('#legend-box-5').remove();
								} else if (selectedLayer === 'yield') {
									// regionalYieldMap(dbRegsMap);
									renderYieldMap('regional', dbRegsMap, null, selectedPeriodText);
								}
								
							} else {
								
								$('#loadingOverlay').hide();
								$('#rightModal').hide();

								Swal.fire({
									title: 'No Available Data',
									text: 'Please select another location.',
									icon: 'info',
									showConfirmButton: false,
									timer: 2000,
									timerProgressBar: true,
									didClose: () => {
										createNoDataLeafletMap(); // ✅ Run after it auto-closes
									}
								});
							}

							// FOR CARDS VALUES
							const prodData = JSON.parse(response['annual_production']); // parse the JSON string
							const productionValue = parseFloat(prodData.value); // convert string to number

							const areaData = JSON.parse(response['annual_areaharvested']);
							const areaharverstedValue = parseFloat(areaData.value);

							const yieldData = JSON.parse(response['annual_yield']);
							const averageYieldValue = parseFloat(yieldData.value);

							// FOR CARDS TITLE AND VALUES
							$('#palayproduction_label').html('Palay Production (' + selectedYear + ')');
							$('#palayproduction_value').html(numberWithCommas(productionValue.toFixed(2)));
							$('#palayproduction_unit').html('million metric tons');

							$('#areaharvested_label').html('Area Harvested (' + selectedYear + ')');
							$('#areaharvested_value').html(numberWithCommas(areaharverstedValue.toFixed(2)));
							$('#areaharvested_unit').html('million hectares');

							$('#averageyield_label').html('Average Yield (' + selectedYear + ')');
							$('#averageyield_value').html(averageYieldValue.toFixed(2));
							$('#averageyield_unit').html('metric tons per hectare');




							// ###################################### FOR BAR CHART 
							const ctx = document.getElementById('barChart');

							// Extract regions
							const regions = dbRegsMap.map(item => item.location_name);

							// Extract numeric values
							const values = dbRegsMap.map(item => parseFloat(item.value));

							// Filter out invalid or zero values
							const validData = dbRegsMap
								.filter(item => item.value !== null && item.value !== undefined && parseFloat(item.value) > 0)
								.map(item => ({ region: item.location_name, value: parseFloat(item.value) }));

							// 🔹 Combine & sort (descending)
							const sorted = validData.sort((a, b) => b.value - a.value);

							const sortedLabels = sorted.map(item => item.region);
							const sortedValues = sorted.map(item => item.value);

							// 🔹 Declare quartile variables
							let q1, q2, q3;

							if (psgcCode === 'PHL' && selectedPeriodText === "Annual" && selectedLayer === 'production') {
								// Static quartiles for national annual map
								q1 = 500000;
								q2 = 1000000;
								q3 = 2000000;
							} else if (selectedLayer === 'yield') {
								q1 = 3;
								q2 = 4;
								q3 = 5;
								q4 = 6;
							} else {	
								// --- 🔹 Quartile Calculation ---
								const sortedArr = [...sortedValues].sort((a, b) => a - b);

								q1 = quantile(sortedArr, 0.25);
								q2 = quantile(sortedArr, 0.50);
								q3 = quantile(sortedArr, 0.75);

								q1 = dynamicRound(q1, 1);
								q2 = dynamicRound(q2, 1);
								q3 = dynamicRound(q3, 1);
							}

							// --- 🔹 Assign colors based on quartile (pabaliktad) ---
							function getColor(value) {
								const val = parseFloat(value);
								if (isNaN(val)) return '#D3D3D3'; // Default color for invalid values

								if (selectedLayer === 'area_harvested') {
									if (val > q3) return '#377EB8';
									else if (val > q2) return '#66C2A5';
									else if (val > q1) return '#FDD49E';
									else return '#E78A1F';
								} else if (selectedLayer === 'yield') {
									if (val > q4) return '#B07AA1';
									else if (val > q3) return '#3B93E4';
									else if (val > q2) return '#29883E';
									else if (val > q1) return '#FFF883';
									else return '#F1A63C'; 
								} else {
									if (val > q3) return '#1F78B4';
									else if (val > q2) return '#4DAF4A';
									else if (val > q1) return '#FFC107';
									else return '#FF7F00';   
								}

							}

							// Apply colors based on the same logic as map fillKey
							const colors = sorted.map(item => getColor(item.value));

							// --- 🔹 Chart.js ---
							Chart.register(ChartDataLabels);

							window.barChartInstance = new Chart(ctx, {
								type: 'bar',
								data: {
									labels: sortedLabels,
									datasets: [{
									label: 'Palay Production (metric tons)',
									data: sortedValues,
									backgroundColor: colors,
									borderRadius: 2,
									barThickness: '20',     // allow flexible sizing
								barPercentage: 0.6,       // 🔹 smaller = thinner bars (default: 0.9)
								categoryPercentage: 0.7,  // 🔹 smaller = more spacing between bars
									}]
								},
								options: {
									indexAxis: 'y',
									responsive: true,
									maintainAspectRatio: false,
									layout: { padding: { right: 75 } },
									scales: {
									x: {
										beginAtZero: true,
										grid: { display: false },
										title: { display: false, text: 'Metric Tons' }
									},
									y: {
										grid: { display: false }
									}
									},
									plugins: {
									legend: { display: false },
									tooltip: { enabled: true },
									datalabels: {
										align: 'right',
										anchor: 'end',
										clip: false,
										color: '#000',
										font: { weight: 'bold' },
										formatter: (value) => {
											const num = parseFloat(value);
											if (isNaN(num)) return '';
											return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
										}
									}
									}
								},
								plugins: [ChartDataLabels]
							});

						},
						error: function (request, status, error) {
							console.log('Error: ', error);
							console.log('Status: ', status);
							console.log('Response: ', request.responseText);
							alert('Error occurred while fetching data.');
						}
					});

					$('#data-source').html('<b>Data Source:</b> Philippine Statistic Authority (PSA)');
				} else {

					// USE LOCTYPE FOR NOW
					// FOR PROVINCE LEVEL BY MUNICIPALITY
					if (locType == 2) {
						$.ajax({
							url: "<?php echo base_url(); ?>fetch/get_data_by_province",
							method: 'POST',
							dataType: "JSON",
							data: {
								year: selectedYear,
								location_id: locCode,
								ecosystem: selectedEcosystem,
								period: selectedPeriod,
								year_type: selectedYearType
							},
							success: function(response) {
								console.log(response);

								var dbMuniMap = [];

								if (selectedLayer === 'production') {
									dbMuniMap = JSON.parse(response['municipal_production_geocoded']);
								} else if (selectedLayer === 'yield') {
									dbMuniMap = JSON.parse(response['municipal_yield_geo']);
								} else if (selectedLayer === 'area_harvested') {
									dbMuniMap = JSON.parse(response['municipal_area_geocoded']);	
								}
						
								var locationCoordinates = JSON.parse(response['location_coordinates']);

								if (dbMuniMap && Object.keys(dbMuniMap).length > 0) {

									if (selectedLayer === 'production') {
										// provinceMapByMunicipality(dbMuniMap, locationCoordinates, selectedPeriodText);
										renderProductionMap('municipal', dbMuniMap, locationCoordinates, selectedPeriodText);
										$('#series-5').remove();
										$('#legend-box-5').remove();
									} else if (selectedLayer === 'area_harvested') {
										// regionalAreaHarvestedMap(dbRegsMap, null, selectedPeriodText);
										renderAreaHarvestedMap('municipal', dbMuniMap, locationCoordinates, selectedPeriodText);
										$('#series-5').remove();
										$('#legend-box-5').remove();
									} else if (selectedLayer === 'yield') {
										renderYieldMap('municipal', dbMuniMap, locationCoordinates, selectedPeriodText);
									}

								} else {

									$('#loadingOverlay').hide();
									$('#rightModal').hide();

									Swal.fire({
										title: 'No Available Data',
										text: 'Please select another location.',
										icon: 'info',
										showConfirmButton: false,
										timer: 2000,
										timerProgressBar: true,
										didClose: () => {
											createNoDataLeafletMap(); // ✅ Run after it auto-closes
										}
									});
								}

								// // FOR CARDS VALUES
								const prodData = JSON.parse(response['annual_production']); // parse the JSON string
								const productionValue = parseFloat(prodData.value); // convert string to number

								const areaData = JSON.parse(response['annual_areaharvested']);
								const areaharverstedValue = parseFloat(areaData.value);

								const yieldData = JSON.parse(response['annual_yield']);
								const averageYieldValue = parseFloat(yieldData.value);

								// FOR CARDS TITLE AND VALUES
								$('#palayproduction_label').html('Palay Production (' + selectedYear + ')');
								$('#palayproduction_value').html(numberWithCommas(productionValue.toFixed(2)));
								$('#palayproduction_unit').html('million metric tons');

								$('#areaharvested_label').html('Area Harvested (' + selectedYear + ')');
								$('#areaharvested_value').html(numberWithCommas(areaharverstedValue.toFixed(2)));
								$('#areaharvested_unit').html('million hectares');

								$('#averageyield_label').html('Average Yield (' + selectedYear + ')');
								$('#averageyield_value').html(averageYieldValue.toFixed(2));
								$('#averageyield_unit').html('metric tons per hectare');



								// ###################################### FOR BAR CHART 
								const ctx = document.getElementById('barChart');

								// Extract municipalities
								const municipalities = dbMuniMap.map(item => item.location_name);

								// Extract numeric values
								const values = dbMuniMap.map(item => parseFloat(item.value));

								// Filter out invalid or zero values
								const validData = dbMuniMap
									.filter(item => item.value !== null && item.value !== undefined && parseFloat(item.value) > 0)
									.map(item => ({ location: item.location_name.replace(/\\n/g, ', ').trim(), value: parseFloat(item.value) }));

								// 🔹 Combine & sort (descending)
								const sorted = validData.sort((a, b) => b.value - a.value);

								const sortedLabels = sorted.map(item => item.location);
								const sortedValues = sorted.map(item => item.value);

								// 🔹 Declare quartile variables
								let q1, q2, q3;

								if (selectedLayer === 'yield') {
									q1 = 3;
									q2 = 4;
									q3 = 5;
									q4 = 6;
								} else {
									// --- 🔹 Quartile Calculation ---
									const sortedArr = [...sortedValues].sort((a, b) => a - b);

									q1 = quantile(sortedArr, 0.25);
									q2 = quantile(sortedArr, 0.50);
									q3 = quantile(sortedArr, 0.75);

									q1 = dynamicRound(q1, 1);
									q2 = dynamicRound(q2, 1);
									q3 = dynamicRound(q3, 1);
								}							

								// --- 🔹 Assign colors based on quartile (pabaliktad) ---
								function getColor(value) {
									const val = parseFloat(value);
									if (isNaN(val)) return '#D3D3D3'; // fallback for invalid numbers

									if (selectedLayer === 'area_harvested') {
										if (val > q3) return '#377EB8';
										else if (val > q2) return '#66C2A5';
										else if (val > q1) return '#FDD49E';
										else return '#E78A1F';
									} else if (selectedLayer === 'yield') {
										if (val > q4) return '#B07AA1';
										else if (val > q3) return '#3B93E4';
										else if (val > q2) return '#29883E';
										else if (val > q1) return '#FFF883';
										else return '#F1A63C'; 
									} else {
										if (val > q3) return '#1F78B4';
										else if (val > q2) return '#4DAF4A';
										else if (val > q1) return '#FFC107';
										else return '#FF7F00';   
									}
								}

								// Apply colors based on the same logic as map fillKey
								const colors = sorted.map(item => getColor(item.value));

								// --- 🔹 Chart.js ---
								Chart.register(ChartDataLabels);

								window.barChartInstance = new Chart(ctx, {
									type: 'bar',
									data: {
										labels: sortedLabels,
										datasets: [{
										label: 'Palay Production (metric tons)',
										data: sortedValues,
										backgroundColor: colors,
										borderRadius: 2,
										barThickness: 'flex',     // allow flexible sizing
										barPercentage: 0.6,       // 🔹 smaller = thinner bars (default: 0.9)
										categoryPercentage: 0.7,  // 🔹 smaller = more spacing between bars
										}]
									},
									options: {
										indexAxis: 'y',
										responsive: true,
										maintainAspectRatio: false,
										layout: { padding: { right: 75 } },
										scales: {
										x: {
											beginAtZero: true,
											grid: { display: false },
											title: { display: false, text: 'Metric Tons' }
										},
										y: {
											grid: { display: false }
										}
										},
										plugins: {
											legend: { display: false },
											tooltip: { enabled: true },
											datalabels: {
												align: 'right',
												anchor: 'end',
												clip: false,
												color: '#000',
												font: { weight: 'bold' },
												formatter: (value) => {
													const num = parseFloat(value);
													if (isNaN(num)) return '';
													return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
												}
											}
										}
									},
									plugins: [ChartDataLabels]
								});


							},
							error: function (request, status, error) {
								console.log('Error: ', error);
								console.log('Status: ', status);
								console.log('Response: ', request.responseText);
								alert('Error occurred while fetching data.');
							}
						});

						$('#data-source').html('<b>Data Source:</b> Philippine Rice Information System (PRiSM)');


					// FOR REGION LEVEL BY PROVINCE 
					} else {
						$.ajax({
							url: "<?php echo base_url(); ?>fetch/get_data_by_region",
							method: 'POST',
							dataType: "JSON",
							data: {
								year: selectedYear,
								location_id: locCode,
								ecosystem: selectedEcosystem,
								period: selectedPeriod,
								year_type: selectedYearType,
								psgc_code: psgcCode
							},
							success: function(response) {
								// console.log(response);

								var dbProvsMap = [];

								if (selectedLayer === 'production') {
									dbProvsMap = JSON.parse(response['provincial_production_geocoded']);
								} else if (selectedLayer === 'yield') {
									dbProvsMap = JSON.parse(response['provincial_yield_geo']);
								} else if (selectedLayer === 'area_harvested') {
									dbProvsMap = JSON.parse(response['provincial_area_geocoded']);
								}
								
								var locationCoordinates = JSON.parse(response['location_coordinates']);

								if (dbProvsMap && Object.keys(dbProvsMap).length > 0) {
									if (selectedLayer === 'production') {
										// regionProductionMapByProvince(dbProvsMap, locationCoordinates, selectedPeriodText);
										renderProductionMap('province', dbProvsMap, locationCoordinates, selectedPeriodText);
										$('#series-5').remove();
										$('#legend-box-5').remove();
									} else if (selectedLayer === 'area_harvested') {
										// regionAreaHarvestedMapByProvince(dbProvsMap, locationCoordinates, selectedPeriodText);
										renderAreaHarvestedMap('province', dbProvsMap, locationCoordinates, selectedPeriodText);
										$('#series-5').remove();
										$('#legend-box-5').remove();
									} else if (selectedLayer === 'yield') {
										// regionYieldMapByProvince(dbProvsMap, locationCoordinates);
										renderYieldMap('province', dbProvsMap, locationCoordinates, selectedPeriodText);
									}
									
								} else {

									$('#loadingOverlay').hide();
									$('#rightModal').hide();	
									
									Swal.fire({
										title: 'No Available Data',
										text: 'Please select another location.',
										icon: 'info',
										showConfirmButton: false,
										timer: 2000,
										timerProgressBar: true,
										didClose: () => {
											createNoDataLeafletMap(); // ✅ Run after it auto-closes
										}
									});
								}


								// // FOR CARDS VALUES
								const prodData = JSON.parse(response['annual_production']); // parse the JSON string
								const productionValue = parseFloat(prodData.value); // convert string to number

								const areaData = JSON.parse(response['annual_areaharvested']);
								const areaharverstedValue = parseFloat(areaData.value);

								const yieldData = JSON.parse(response['annual_yield']);
								const averageYieldValue = parseFloat(yieldData.value);

								// FOR CARDS TITLE AND VALUES
								$('#palayproduction_label').html('Palay Production (' + selectedYear + ')');
								$('#palayproduction_value').html(numberWithCommas(productionValue.toFixed(2)));
								$('#palayproduction_unit').html('million metric tons');

								$('#areaharvested_label').html('Area Harvested (' + selectedYear + ')');
								$('#areaharvested_value').html(numberWithCommas(areaharverstedValue.toFixed(2)));
								$('#areaharvested_unit').html('million hectares');

								$('#averageyield_label').html('Average Yield (' + selectedYear + ')');
								$('#averageyield_value').html(averageYieldValue.toFixed(2));
								$('#averageyield_unit').html('metric tons per hectare');



								// ###################################### FOR BAR CHART 
								const ctx = document.getElementById('barChart');

								// Extract regions
								const provinces = dbProvsMap.map(item => item.location_name);

								// Extract numeric values
								const values = dbProvsMap.map(item => parseFloat(item.value));

								// Filter out invalid or zero values
								const validData = dbProvsMap
									.filter(item => item.value !== null && item.value !== undefined && parseFloat(item.value) > 0)
									.map(item => ({ location: item.location_name, value: parseFloat(item.value) }));

								// 🔹 Combine & sort (descending)
								const sorted = validData.sort((a, b) => b.value - a.value);

								const sortedLabels = sorted.map(item => item.location);
								const sortedValues = sorted.map(item => item.value);

								// 🔹 Declare quartile variables
								let q1, q2, q3;

								if (selectedLayer === 'yield') {
									q1 = 3;
									q2 = 4;
									q3 = 5;
									q4 = 6;
								} else {
									// --- 🔹 Quartile Calculation ---
									const sortedArr = [...sortedValues].sort((a, b) => a - b);

									q1 = quantile(sortedArr, 0.25);
									q2 = quantile(sortedArr, 0.50);
									q3 = quantile(sortedArr, 0.75);

									q1 = dynamicRound(q1, 1);
									q2 = dynamicRound(q2, 1);
									q3 = dynamicRound(q3, 1);
								}							

								// --- 🔹 Assign colors based on quartile (pabaliktad) ---
								function getColor(value) {
									const val = parseFloat(value);
									if (isNaN(val)) return '#D3D3D3'; // fallback for invalid numbers

									if (selectedLayer === 'area_harvested') {
										if (val > q3) return '#377EB8';
										else if (val > q2) return '#66C2A5';
										else if (val > q1) return '#FDD49E';
										else return '#E78A1F';
									} else if (selectedLayer === 'yield') {
										if (val > q4) return '#B07AA1';
										else if (val > q3) return '#3B93E4';
										else if (val > q2) return '#29883E';
										else if (val > q1) return '#FFF883';
										else return '#F1A63C'; 
									} else {
										if (val > q3) return '#1F78B4';
										else if (val > q2) return '#4DAF4A';
										else if (val > q1) return '#FFC107';
										else return '#FF7F00';   
									}
								}

								// Apply colors based on the same logic as map fillKey
								const colors = sorted.map(item => getColor(item.value));

								// --- 🔹 Chart.js ---
								Chart.register(ChartDataLabels);

								window.barChartInstance = new Chart(ctx, {
									type: 'bar',
									data: {
										labels: sortedLabels,
										datasets: [{
										label: 'Palay Production (metric tons)',
										data: sortedValues,
										backgroundColor: colors,
										borderRadius: 2,
										barThickness: '50',     // allow flexible sizing
										barPercentage: 0.6,       // 🔹 smaller = thinner bars (default: 0.9)
										categoryPercentage: 0.7,  // 🔹 smaller = more spacing between bars
										}]
									},
									options: {
										indexAxis: 'y',
										responsive: true,
										maintainAspectRatio: false,
										layout: { padding: { right: 75 } },
										scales: {
										x: {
											beginAtZero: true,
											grid: { display: false },
											title: { display: false, text: 'Metric Tons' }
										},
										y: {
											grid: { display: false }
										}
										},
										plugins: {
										legend: { display: false },
										tooltip: { enabled: true },
										datalabels: {
											align: 'right',
											anchor: 'end',
											clip: false,
											color: '#000',
											font: { weight: 'bold' },
											formatter: (value) => {
												const num = parseFloat(value);
												if (isNaN(num)) return '';
												return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
											}
										}
										}
									},
									plugins: [ChartDataLabels]
								});

							},
							error: function (request, status, error) {
								console.log('Error: ', error);
								console.log('Status: ', status);
								console.log('Response: ', request.responseText);
								alert('Error occurred while fetching data.');
							}
						});


						$('#data-source').html('<b>Data Source:</b> Philippine Statistic Authority (PSA)');

					}
					
						
				}

			} else {

				// Clear html content of cards container
				$('#cards-container').html('');

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
