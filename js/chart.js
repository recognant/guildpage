var $Chart = {

	plotArea: function(container, title, subtitle, min, max, tick, yTitle, tooltipSuffix, categories, series) {
		$(container).addClass('chart');
		
		$(container).highcharts({
			chart: {
				type: 'area',
				backgroundColor: 'rgba(100, 100, 100, 0.35)'
			},
			title: {
				text: title,
				style: {
					color: '#fff'
				}
			},
			legend: {
				itemStyle: {
					color: '#fff'
				}
			},
			subtitle: {
				text: subtitle,
				style: {
					color: '#afafaf'
				}
			},
			xAxis: {
				categories: categories,
				tickmarkPlacement: 'off',
				title: {
					enabled: false
				},
				labels: {
					style: {
						color: '#fff'
					}
				}
			},
			yAxis: {
				min: min,
				max: max,
				tickInterval: tick,
				title: {
					text: yTitle,
					style: {
						color: '#fff'
					}
				},
				labels: {
					style: {
						color: '#fff'
					}
				}
			},
			tooltip: {
				shared: true,
				valueSuffix: tooltipSuffix
			},
			plotOptions: {
				area: {
					lineColor: '#ccc',
					lineWidth: 1,
					marker: {
						lineWidth: 1,
						lineColor: '#ccc'
					}
				}
			},
			series: series
		});
	},
	
	plotVBar: function(container, title, subtitle, categories, series) {
		$(container).addClass('chart');
		
		$(container).highcharts({
			chart: {
				type: 'bar',
				backgroundColor: 'transparent'
			},
			title: {
				text: title,
				style: {
					color: '#fff'
				}
			},
			subtitle: {
				text: subtitle
			},
			xAxis: [{
				categories: categories,
				crosshair: true,
				title: {
					style: {
						color: '#fff'
					}
				},
				labels: {
					style: {
						color: '#fff'
					}
				}
			}],
			yAxis: [{ // Primary yAxis
				labels: {
					format: '{value}%',
					style: {
						color: '#fff'
					}
				},
				title: {
					text: 'Rank',
					style: {
						color: '#fff'
					}
				},
				opposite: true,
				min: 0,
				max: 100

			}, { // Secondary yAxis
				title: {
					text: 'Total',
					style: {
						color: '#fff'
					}
				},
				labels: {
					format: '{value}',
					style: {
						color: '#fff'
					}
				},
				opposite: true,
				min: 0

			}, { // Tertiary yAxis
				title: {
					text: 'Item Level',
					style: {
						color: '#fff'
					}
				},
				labels: {
					format: '{value}',
					style: {
						color: '#fff'
					}
				},
				opposite: true,
				min: 500,
				max: 800
			}],
			tooltip: {
				shared: true
			},
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'top',
				floating: true,
				borderWidth: 1,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
				shadow: false
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			credits: {
				enabled: false
			},
			series: series
		});
	},
	
	plotBar: function(container, title, subtitle, yTitle, E, categories, series) {
		$(container).addClass('chart');
		
		$(container).highcharts({
			chart: {
				type: 'column',
				backgroundColor: 'rgba(100, 100, 100, 0.35)'
			},
			title: {
				text: title,
				style: {
					color: '#fff'
				}
			},
			subtitle: {
				text: subtitle
			},
			xAxis: {
				categories: categories,
				crosshair: true,
				plotLines: [{
					color: 'red', // Color value
					dashStyle: 'solid', // Style of the plot line. Default to solid
					value: E, // Value of where the line will appear
					width: 2 // Width of the line    
				}],
				title: {
					style: {
						color: '#fff'
					}
				},
				labels: {
					style: {
						color: '#fff'
					}
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: yTitle,
					style: {
						color: '#fff'
					}
				},
				labels: {
					style: {
						color: '#fff'
					}
				}
			},
			tooltip: {
				shared: true
			},
			legend: {
				enabled: false,
				itemStyle: {
					color: '#fff'
				}
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: series
		});
	}

};

$Chart.plotGuildranks = function(container, categories, series) {
	$Chart.plotArea(container, 'Performance by Player', 'via Raidtools', 0, 100, 10, '%', '%', categories, series);
};

$Chart.plotCharranks = function(container, categories, series) {
	$Chart.plotVBar(container, 'Performance by Player', 'via Raidtools', categories, series);
};

$Chart.plotRank = function(container, E, categories, series) {
	$Chart.plotBar(container, 'Rank for Player', 'via Raidtools', 'Players', E, categories, series);
};

