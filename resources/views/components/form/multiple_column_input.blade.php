@php
if (!is_array($attributes)) $attributes = [];

$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes);

if (empty($values)) $values = [''];

$isFirst = true;
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
							@foreach ($values as $v)
							<tr class="multipleColumnRow">
								@foreach ($columns as $key => $column)

									<?php 
										$column['htmlOptions'] = App\Components\FormBuilderHelper::arrayToHtmlAttribute(array_merge([
											'class' => 'form-control'
										], $column['elOptions'])) 
									?>

									<td {{ $column['options'] ?? ''}}>
										<input type="{{ $column['type'] }}" value="{{ $v[$column['name']] ?? '' }}" name="{{ $name .'[1]['. $column['name'] .']' }}" {!! $column['htmlOptions'] !!}>
									</td>
								@endforeach
								<td>
									<button type="button" class="btn btn-primary multipleColumnInput_addRowBtn-{{  $name  }}"><span class="fas fa-plus"></span></button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				@if($errors->has($name))
				<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
				@endif

			</div>
	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>

@push('additional-js')
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
		
			<?php 
				$column['htmlOptions'] = App\Components\FormBuilderHelper::arrayToHtmlAttribute(array_merge([
					'class' => 'form-control'
				], $column['elOptions'])) 
			?>

			multipleColumn_columns_{{ $name }} += '<td {{ $column["options"] ?? "" }}>' + 
				'<input type="{{ $column['type'] ?? 'text' }}" value="" name="{{ $name }}[' + lastRow_{{ $name }} + '][{{ $column['name'] }}]" {!! $column['htmlOptions'] !!}>' + 
			'</td>' 		
		@endforeach
		return multipleColumn_columns_{{ $name }}
	}

	function generateRow_{{ $name }}() {
		$('#table-multiple_column-{{ $name }}').append(
			'<tr class="multipleColumnRow">' +
				getColumn_{{ $name }}(++$('.multipleColumnRow').length) +
				'<td>' +
					'<button type="button" class="btn btn-danger multipleInput_removeRowBtn-{{  $name  }}"><span class="fa fa-times"></span></button>' +
				'</td>' +
			'</tr>'
		)
	}

	$('body').on('click', '.multipleColumnInput_addRowBtn-{{  $name  }}', function(){
		generateRow_{{ $name }}();
	})
</script>
@endpush