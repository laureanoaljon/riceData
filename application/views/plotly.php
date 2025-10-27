<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Plotly Horizontal Bar Chart</title>
  <script src="https://cdn.plot.ly/plotly-2.32.0.min.js"></script>
</head>
<body>

  <div id="barChart" style="width:100%; max-height:400px;"></div>

<script src="https://cdn.plot.ly/plotly-2.32.0.min.js"></script>
<script>
	// =========================================
	// 🔹 Simulate your dbRegsMap data
	// =========================================
	const dbRegsMap = [
		{ location_name: 'Region I', value: 12.5 },
		{ location_name: 'Region II', value: 19.8 },
		{ location_name: 'Region III', value: 8.7 },
		{ location_name: 'Region IV-A', value: 15.2 },
		{ location_name: 'Region V', value: 10.6 }
	];

	// Simulate your variables
	const selectedLayer = 'production';
	const selectedPeriodText = 'Annual';
	const psgcCode = 'PHL';

	// =========================================
	// 🔹 Data Preparation
	// =========================================
	const validData = dbRegsMap
		.filter(item => item.value !== null && item.value !== undefined && parseFloat(item.value) > 0)
		.map(item => ({ region: item.location_name, value: parseFloat(item.value) }));

	// Sort descending
	const sorted = validData.sort((a, b) => b.value - a.value);
	const sortedLabels = sorted.map(item => item.region);
	const sortedValues = sorted.map(item => item.value);

	// =========================================
	// 🔹 Quartile Calculation
	// =========================================
	function quantile(arr, q) {
		const sorted = [...arr].sort((a, b) => a - b);
		const pos = (sorted.length - 1) * q;
		const base = Math.floor(pos);
		const rest = pos - base;
		if (sorted[base + 1] !== undefined) {
			return sorted[base] + rest * (sorted[base + 1] - sorted[base]);
		} else {
			return sorted[base];
		}
	}

	function dynamicRound(num, decimals = 1) {
		return parseFloat(num.toFixed(decimals));
	}

	let q1, q2, q3, q4;

	if (psgcCode === 'PHL' && selectedPeriodText === "Annual" && selectedLayer === 'production') {
		q1 = 500000; q2 = 1000000; q3 = 2000000;
	} else if (selectedLayer === 'yield') {
		q1 = 3; q2 = 4; q3 = 5; q4 = 6;
	} else {
		const sortedArr = [...sortedValues].sort((a, b) => a - b);
		q1 = dynamicRound(quantile(sortedArr, 0.25), 1);
		q2 = dynamicRound(quantile(sortedArr, 0.50), 1);
		q3 = dynamicRound(quantile(sortedArr, 0.75), 1);
	}

	// =========================================
	// 🔹 Color Assignment Logic
	// =========================================
	function getColor(value) {
		const val = parseFloat(value);
		if (isNaN(val)) return '#D3D3D3';

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

	const colors = sortedValues.map(v => getColor(v));

	let labelUnit;
	if (selectedLayer === 'area_harvested') {
		labelUnit = 'Area Harvested (hectares)';
	} else if (selectedLayer === 'yield') {
		labelUnit = 'Average Yield (metric tons per hectare)';
	} else {
		labelUnit = 'Palay Production (metric tons)';
	}

	// =========================================
	// 🔹 Plotly Bar Chart
	// =========================================
	const trace = {
		type: 'bar',
		x: sortedValues,
		y: sortedLabels,
		orientation: 'h',
		text: sortedValues.map(v => v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })),
		textposition: 'auto',
		marker: {
			color: colors,
			line: { width: 1, color: '#fff' }
		},
		hovertemplate: '%{y}<br>%{x:.2f} tons<extra></extra>'
	};

	const layout = {
		title: labelUnit,
		margin: { l: 100, r: 30, t: 50, b: 40 },
		xaxis: { title: 'Metric Tons', showgrid: false },
		yaxis: { automargin: true, showgrid: false },
		height: 400,
		barmode: 'group'
	};

	const config = { responsive: true, displayModeBar: true, scrollZoom: true };

	Plotly.newPlot('barChart', [trace], layout, config);
</script>


</body>
</html>
