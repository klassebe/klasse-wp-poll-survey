<style type="text/css">
	body {
		font-family: 'Open Sans', sans-serif;
	}
	.left {
		float: left;
	}
	.clearfix {
		clear: both;
	}
	textarea, input, select, button {
		font-family: inherit;
		font-size: 13px;
		font-weight: inherit;
	}
	#bar-chart,
	#pie-chart,
	#stacked-bar-chart,
	#quiz-respons {
		padding: 5px;
	}
	.selected {
		border: 5px solid lightgrey;
	}
</style>
<h2>Select a chart</h2>
<p>Choose a chart and press the insert into button to add this to the editor</p>
<div id="charts">

	<div id="bar-chart" class="media-item left">
		<label>
			<h4>Bar Chart</h4>
			<input type="radio" name="results" value="bar_chart">
			<img class="thumbnail" src="images/bar_chart.png" alt="bar-chart-per-question" height="128" width="128">
		</label>
	</div>
	<div id="pie-chart" class="media-item left">
		<label>
			<h4>Pie Chart</h4>
			<input type="radio" name="results" value="pie_chart">
			<img class="thumbnail" src="images/pie_chart.png" alt="pie-chart-per-question" height="128" width="160">
		</label>
	</div>

	<div id="stacked-bar-chart" class="media-item left">
		<label>
			<h4>Stacked Bar Chart</h4>
			<input type="radio" name="results" value="stacked_bar_chart">
			<img class="thumbnail" src="images/stacked_bar_chart.png" alt="stacked-bar-chart-per-question" height="128" width="128">
		</label>
	</div>

	<div id="quiz-respons" class="media-item left">
		<label>
			<h4>Quiz Respons</h4>
			<input type="radio" name="results" value="quiz_respons">
			<img class="thumbnail" src="images/stacked_bar_chart.png" alt="quiz-respons" height="128" width="128">
		</label>
	</div>

	<div id="result-profile" class="media-item left">
		<label>
			<h4>Result Profile</h4>
			<input type="radio" name="results" value="result_profile">
			<img class="thumbnail" src="images/stacked_bar_chart.png" alt="result-profile" height="128" width="128">
		</label>
	</div>
</div>
<div class="clearfix">
	<button id="add-result-to-editor" class="button">Add Chart</button>
</div>
<div id="extra-test">
</div>
<script src="../../../../wp-includes/js/jquery/jquery.js"></script>
<script type="text/javascript">

jQuery(function ($) {
	var selectedResult;
	$('input:radio').hide();
	$('input:radio').on('click', function () {
	    $('.selected').removeClass();
	    $(this).next().addClass('selected');
	    selectedResult = $(this).next().attr('alt');
	});
	$('#add-result-to-editor').on('click', function () {
		if (selectedResult) {
			$('iframe', window.parent.document).contents().find('#tinymce').append('[kwps_result result='+ selectedResult + ']');
			self.parent.tb_remove();
		} else {
			alert('Please select a result view to import');
		}
	});
});
</script>