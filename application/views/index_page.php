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
		width: 800px;
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
		padding: 15px;
	}

	.left-controls {
		position: fixed;
		top: 150px;     /* adjust starting height */
		left: 25px;     /* position from left edge */
		display: flex;
		flex-direction: column;  /* stack vertically */
		gap: 8px;       /* spacing between buttons */
		z-index: 2000;
	}

	.left-modal {
		width: 250px;
		background: white;
		border-radius: 10px;
		box-shadow: 0 4px 10px rgba(0,0,0,0.2);
		margin-top: 5px;
	}

	.right-modal {
		right: 20px;
		width: 600px;
		top: 20px;
		cursor: move;
	}

	.pay-card{
		/* background-color: #2138b7;  */
		background: linear-gradient(to top, #7a8ff5, #4a5fd3, #2138b7);
		border: none !important;
		padding: 2px;
	}


  </style>
</head>
<body>

	<!-- Map container -->
	<div id="leafletmapmain"></div>

	<!-- Location Filter -->
	<div class="filter-modal rounded-pill px-4">
		<div class="form-group mb-1 mt-1 d-flex align-items-center gap-2">
			<!-- Dropdown -->
			<div class="custom-select-wrapper flex-grow-1 mx-2 position-relative">
				<select class="form-control form-select custom-select-arrow" id="selectLocation" style="height: 45px; width: 100%;">
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
	<div class="left-controls">
		<!-- Button 1 -->
		<button class="btn btn-success rounded-pill mb-2" 
			type="button" 
			data-bs-toggle="collapse" 
			data-bs-target="#riceMapsCollapse" 
			aria-expanded="false" 
			aria-controls="riceMapsCollapse">
			Rice Maps
		</button>

		<div class="collapse left-modal" id="riceMapsCollapse">
			<div class="card card-body">
				<h5>Rice Maps</h5>
				<p>Layer or control options here.</p>
				<button class="btn btn-primary btn-sm">Run</button>
			</div>
		</div>

		<!-- Button 2 -->
		<button class="btn btn-warning rounded-pill mb-2"
			type="button" 
			data-bs-toggle="collapse" 
			data-bs-target="#yieldCollapse" 
			aria-expanded="false" 
			aria-controls="yieldCollapse">
			Yield Stats
		</button>

		<div class="collapse left-modal" id="yieldCollapse">
			<div class="card card-body">
				<h5>Yield Statistics</h5>
				<p>Display yield layers or data filters.</p>
				<button class="btn btn-primary btn-sm">Analyze</button>
			</div>
		</div>

		<!-- Button 3 -->
		<button class="btn btn-info rounded-pill"
			type="button" 
			data-bs-toggle="collapse" 
			data-bs-target="#weatherCollapse" 
			aria-expanded="false" 
			aria-controls="weatherCollapse">
			Weather Data
		</button>

		<div class="collapse left-modal" id="weatherCollapse">
			<div class="card card-body">
				<h5>Weather Layers</h5>
				<p>Temperature, rainfall, etc.</p>
				<button class="btn btn-primary btn-sm">View</button>
			</div>
		</div>
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



  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <script>

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

    // Modal button actions
    document.getElementById('leftBtn').addEventListener('click', () => {
      alert('Left modal button clicked!');
    });

    document.getElementById('rightBtn').addEventListener('click', () => {
      alert('Right modal button clicked!');
    });
  </script>

</body>
</html>
