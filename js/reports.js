/**
 * copyright 2012 Stephen Just <stephenjust@users.sf.net>
 *
 * This file is part of stkaddons
 *
 * stkaddons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stkaddons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stkaddons.  If not, see <http://www.gnu.org/licenses/>.
 */

// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawCharts);

// Callback that creates and populates a data table, 
// instantiates the pie chart, passes in the data and
// draws it.
function drawCharts() {
    $('.pie_chart').each(function(index, Element){
	var chartMeta = Element.innerHTML;
	chartMeta = chartMeta.split('\n');
	console.log("Loading chart data: " + chartMeta[1]);
	document.finished = false;
	$.ajax({
	    url: chartMeta[1],
	    dataType:"jsonp",
	    crossDomain: true,
	    async: true,
	    mimeType: "application/json",
	    jsonp: false,
	    jsonpCallback: "data",
	    success: function(data, a, b){ drawPie(data, Element); }
	    });
    });
}

function drawPie(jsonResponse, Element) {
    var chartMeta = Element.innerHTML;
    chartMeta = chartMeta.split('\n');

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonResponse);

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(Element);
    chart.draw(data, {title: chartMeta[0], width: 400, height: 240});
}