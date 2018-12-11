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
				<h3 class="block-title">Plan per 1 Month</h3>
			</div>
			<div class="block-content">
				<canvas id="plan" height="90px"></canvas>
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
	<div class="col-md-12">
		<div class="block block-bordered block-themed">
			<div class="block-header p-5 pl-10">
				<h3 class="block-title">Sampling per Week of Month</h3>
			</div>
			<div class="block-content">
				<canvas id="sampling" height="90px"></canvas>
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
			url: '{{ route('data.gtc_dc') }}',
			type: 'GET',
			success: function (data) {
				if (data.success){
					$('#loading').hide();
					$('#content').show();
					var plan = document.getElementById('plan').getContext('2d');
					var planChart = new Chart(plan, {
						type: 'line',
						data: {
							labels: data.plan.label,
							datasets: [{
								label: 'Plan',
								backgroundColor: 'rgba(54, 162, 235, 0.3)',
								borderColor: 'rgb(54, 162, 235)',
								data: data.plan.data
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
					var sampling = document.getElementById('sampling').getContext('2d');
					var samplingChart = new Chart(sampling, {
						type: 'line',
						data: {
							labels: data.sampling.label,
							datasets: [{
								label: 'Sampling',
								backgroundColor: 'rgba(54, 162, 235, 0.3)',
								borderColor: 'rgb(54, 162, 235)',
								data: data.sampling.data
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