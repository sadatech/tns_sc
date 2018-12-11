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
				<h3 class="block-title">Distribution per 7 Day</h3>
			</div>
			<div class="block-content">
				<canvas id="distribution" height="90px"></canvas>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="block block-bordered block-themed">
			<div class="block-header p-5 pl-10">
				<h3 class="block-title">Sales per Week of Month</h3>
			</div>
			<div class="block-content">
				<canvas id="sales" height="90px"></canvas>
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
			url: '{{ route('data.gtc_motorik') }}',
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
					var sales = document.getElementById('sales').getContext('2d');
					var salesChart = new Chart(sales, {
						type: 'line',
						data: {
							labels: data.sales.label,
							datasets: [{
								label: 'Sales',
								backgroundColor: 'rgba(54, 162, 235, 0.3)',
								borderColor: 'rgb(54, 162, 235)',
								data: data.sales.data
							}]
						},
						options: options
					});
					var distribution = document.getElementById('distribution').getContext('2d');
					var distributionChart = new Chart(distribution, {
						type: 'line',
						data: {
							labels: data.distribution.label,
							datasets: [{
								label: 'Distribution',
								backgroundColor: 'rgba(54, 162, 235, 0.3)',
								borderColor: 'rgb(54, 162, 235)',
								data: data.distribution.data
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