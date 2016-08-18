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
	},
	
	Bar2: function() {
		return { 
			title: {
				text: "",
				color: 'white'
			},
			subtitle: {
				text: "",
				color: 'grey'
			},
			anchor: null,
			xAxis: {
				categories: [],
				line: {
					E: 0,
					color: 'red'
				},
				title: {
					text: "",
					color: 'white'
				},
				label: {
					color: 'white'
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: "",
					color: 'white'
				},
				label: {
					color: 'white'
				}
			},
			legend: {
				enabled: false,
				color: 'white'
			},
			series: [],
			settings: function() {
				return  { chart: { type: 'column', backgroundColor: 'rgba(100, 100, 100, 0.35)' },
					title: { text: this.title.text, style: { color: this.title.color } },
					subtitle: { text: this.subtitle.text, style: { color: this.subtitle.color } },
					xAxis: {
						categories: this.xAxis.categories,
						crosshair: true,
						plotLines: [{ color: this.xAxis.line.color, dashStyle: 'solid', value: this.xAxis.line.E, width: 2 }],
						title: {
							text: this.xAxis.title.text,
							style: { color: this.xAxis.title.color }
						},
						labels: {
							style: { color: this.xAxis.label.color }
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: this.yAxis.title.text,
							style: { color: this.yAxis.title.color }
						},
						labels: {
							style: { color: this.yAxis.label.color }
						}
					},
					tooltip: {
						shared: true
					},
					legend: {
						enabled: this.legend.enabled,
						itemStyle: { color: this.legend.color }
					},
					plotOptions: {
						column: { pointPadding: 0.2, borderWidth: 0 }
					},
					series: this.series
				};
			}
			plot: function() {
				this.anchor.addClass('chart');
				this.anchor.highcharts(this.settings());
			}
		}
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

