@extends('dashboard.gtc')
@section('tabs')
<div id="loading" class="text-center pt-50 pb-50">
	<h1 class="text-primary">
		<i class="fa fa-spin fa-spinner"></i><br>
	</h1>
	<p id="respon">Mengambil Data...</p>
</div>
<div class="row" id="content" style="display: none;">
	<div class="col-md-12">
		<div class="block block-bordered block-themed">
			<div class="block-header p-5 pl-10">
				<h3 class="block-title">Attendance per 7 Day</h3>
			</div>
			<div class="block-content">
				<canvas id="attendance" height="90px"></canvas>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="block block-bordered block-themed">
			<div class="block-header p-5 pl-10">
				<h3 class="block-title">Stockist per Week of Month</h3>
			</div>
			<div class="block-content">
				<canvas id="stockist" height="90px"></canvas>
			</div>
		</div>
	</div>
</div>
@endsection
@section('chartjs')
<script type="text/javascript">
	var options = {
		responsive: true,
		tooltips: {
			mode: 'index',
			intersect: false,
		},
	};

	$(document).ready(function(){
		$.ajax({
			url: '{{ route('data.gtc_smd') }}',
			type: 'GET',
			success: function (data) {
				if (data.success){
					$('#loading').hide();
					$('#content').show();
					var attendance = document.getElementById('attendance').getContext('2d');
					var attendanceChart = new Chart(attendance, {
						type: 'line',
						data: {
							labels: data.attendance.label,
							datasets: [{
								label: 'Attendance',
								backgroundColor: 'rgba(54, 162, 235, 0.3)',
								borderColor: 'rgb(54, 162, 235)',
								data: data.attendance.data
							}]
						},
						options: options
					});
					var stockist = document.getElementById('stockist').getContext('2d');
					var stockistChart = new Chart(stockist, {
						type: 'line',
						data: {
							labels: data.stockist.label,
							datasets: [{
								label: 'Stockist',
								backgroundColor: 'rgba(54, 162, 235, 0.3)',
								borderColor: 'rgb(54, 162, 235)',
								data: data.stockist.data
							}]
						},
						options: options
					});
				} else {
					$('#respon').html('Terjadi Kesalahan!');
				}
			}
		});
	});
</script>
@endsection