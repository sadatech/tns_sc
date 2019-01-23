@php
if (!is_array($attributes)) $attributes = [];

// SET DEFAULT CLASS
$attributes['elOptions']['class'] = 'select2 form-control';

// SET DEFAULT ID
$attributes['elOptions']['id'] = $attributes['elOptions']['id'] ?? 'select2-' . $name;

// CALLING SETUP DEFAULT CONFIG
$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes, true);
$config['pluginOptions'] = $attributes['pluginOptions'] ?? [];
@endphp

<div class="form-group {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
	<div class="row">
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif

			<div class="row">
				<div class="col-md-12">
					<div class="input-group" style="margin-bottom: 10px">
						<div style="width:90%;">
							{{ Form::select2Input($name, $value, $options, array_merge($config, ['useLabel' => false])) }}
						</div>
						<div style="width:10%">
							<span class="input-group-append">
								<button class="btn btn-primary btn-block" type="button" onclick="addSelectedVal_{{$name}}()" style="border-radius: 0 4px 4px 0 !important;">
									Add
								</button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<table class="table {{'table-select2-' . $name}}">
						<thead>
							<tr>
								<th>#</th>
								<th>{{$name}} Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="3">{{$name}} is Empty.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>

@push('vendor-css')
<style type="text/css">
	.select2-container--default .select2-selection--multiple{
		border-top-right-radius: 0 !important; 
	}
</style>
@endpush

@push('additional-js')
<script type="text/javascript">
	var select2val_{{$name}} = {!! json_encode($value) !!};

	function addSelectedVal_{{$name}}(){
		$.each($('#{{$config['elOptions']["id"]}}').select2('data'), function(k, v){
			if (isSelectedDataExist_{{$name}}(v.id)) {
				toastr.options = {
				  "closeButton": true,
				  "debug": false,
				  "newestOnTop": false,
				  "progressBar": true,
				  "positionClass": "toast-bottom-right",
				  "preventDuplicates": false,
				  "onclick": null,
				  "showDuration": "300",
				  "hideDuration": "1000",
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "slideDown",
				  "hideMethod": "slideUp"
				};

				toastr.warning('{{$name}} '+ v.text +' has been selected.', "Warning!");
			} else {
				select2val_{{$name}}.push({
					id: v.id,
					name: v.text
				})
				generateTable_{{$name}}()
			    $('#{{$config['elOptions']["id"]}}').val({}).trigger('change');
			}
		})
	}

	function generateTable_{{$name}}() {
		resetTable_{{$name}}()

		if (select2val_{{$name}}.length > 0) {
			$.each(select2val_{{$name}}, function(k, v) {
				var number = k+1;
	            $(".{{'table-select2-' . $name}} tbody").append(
	            	'<tr>' +
	            		'<td>' + number + '</td>' +
	            		'<td>' + v.name + '</td>' +
	            		'<td>' + 
	            		 	'<button class="btn btn-danger btn-sm removeSelectedDataBtn_{{$name}}" type="button" data-id="'+ v.id +'" title="Remove this {{$name}}" data-toggle="tooltip"><i class="fa fa-times"></i></button>' +  
	            		 '</td>' +
	            		 '<td style="display:none">' +
	            			'<input type="hidden" value="'+ v.id +'" name="{{$name}}[]">' +
	            		 '</td>' +
	            	'</tr>' 
	            )
	        });
		} else {
			$(".{{'table-select2-' . $name}} tbody").append(
	        	'<tr>' +
	        		'<td colspan="3">{{$name}} is Empty.</td>' +
	        	'</tr>' 
	        )
		}
	}

	// Remove Selected Data
	$(".{{'table-select2-' . $name}} tbody").on('click', '.removeSelectedDataBtn_{{$name}}', function(){
		getSelectedVal_{{$name}}($(this).attr('data-id'), true)
		$(this).closest('tr').remove()
		generateTable_{{$name}}()
	})

	function getSelectedVal_{{$name}}(id, update = false) {
		var foundVal = $.grep(select2val_{{$name}}, function(v) {
			if(update){
				return v.id !== id
			}
		    return v.id === id;
		});

		if(update) {
			select2val_{{$name}} = foundVal
			return;
		}

		return foundVal;
	}

	function isSelectedDataExist_{{$name}}(id) {
		return getSelectedVal_{{$name}}(id).length > 0;
	}

	function resetTable_{{$name}}() {
		$(".{{'table-select2-' . $name}} tbody").html('')
	}

	$(document).ready(function(){
		generateTable_{{$name}}()
	})
</script>
@endpush