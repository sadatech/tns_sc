@php
if (!is_array($attributes)) $attributes = [];

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$labelText = $labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');;

$addonsConfig = isset($attributes['addons']) ? $attributes['addons'] : null;
unset($attributes['addons']);

$configAttributes = array_merge([
	'class' => 'form-control',
], $attributes);

if ($values === null || $values === []) $values = [''];

$isFirst = true;
@endphp

<div class="form-group {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($useLabel)
	<div class="row">
		<div class="col col-md-3">
			<label class="form-control-label">
				{!! $labelText !!}
			</label>
		</div>
		<div class="col-12 col-md-9">
	@endif
			<div class="multipleInput_container">

				<div class="table-responsive">
					<table class="table" id="table-multiple_column-{{ $name }}">
						<thead>
							<tr>
								@foreach ($columns as $column)
									<th>{{ ucwords(implode(' ', explode('_', $column['name']))) }}</th>
								@endforeach
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr class="multipleColumnRow">
								@foreach ($columns as $key => $column)
									<td {{ $column['options'] ?? ''}}>
										<input type="{{ $column['type'] }}" name="{{ $name .'[1]['. $column['name'] .']' }}" class="form-control" {{ $column['fieldOptions'] ?? '' }}>
									</td>
								@endforeach
								<td>
									<button type="button" class="btn btn-primary multipleColumnInput_addRowBtn-{{  $name  }}"><span class="fas fa-plus"></span></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				@if($errors->has($name))
				<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
				@endif

			</div>
	@if ($useLabel)
		</div>
	</div>
	@endif
</div>

@push('vendor-js')
<script type="text/javascript">
	$('body').on('click', '.multipleInput_removeRowBtn-{{  $name  }}', function(){
		console.log('remove-clicked');
		$(this).closest('tr').remove()
	})

	var lastRow_{{ $name }} = 0;

	function getColumn_{{ $name }}(key){
		var multipleColumn_columns_{{ $name }} = ''
		lastRow_{{ $name }} = lastRow_{{$name}} == 0 ? key : ++lastRow_{{$name}};

		@foreach ($columns as $column)
			multipleColumn_columns_{{ $name }} += '<td {{ $column["options"] ?? "" }}>' + 
				'<input type="{{ $column['type'] ?? 'text' }}" class="form-control" name="{{ $name }}[' + lastRow_{{ $name }} + '][{{ $column['name'] }}]" {{ $column['fieldOptions'] ?? '' }}>' + 
			'</td>' 		
		@endforeach
		return multipleColumn_columns_{{ $name }}
	}

	$('body').on('click', '.multipleColumnInput_addRowBtn-{{  $name  }}', function(){
		$('#table-multiple_column-{{ $name }}').append(
			'<tr class="multipleColumnRow">' +
				getColumn_{{ $name }}(++$('.multipleColumnRow').length) +
				'<td>' +
					'<button type="button" class="btn btn-danger multipleInput_removeRowBtn-{{  $name  }}"><span class="fas fa-close"></span></button>' +
				'</td>' +
			'</tr>'
		)
	})
</script>
@endpush