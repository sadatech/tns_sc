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

$formattedAttributes = '';
foreach ($configAttributes as $attribute => $value) {
	$formattedAttributes .= $attribute . '="' . $value . '" ';
}

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

				@foreach ($values as $value)
				<div class="input-group" style="margin-bottom: 10px">
					@if (!empty($addonsConfig) && $addonsConfig['position'] === 'left')
					<span class="input-group-addon addon-left-side">{{ $addonsConfig['text'] }}</span>
					@endif

					<input type="{{ $type }}" name="{{$name}}[]" <?= $formattedAttributes ?> value='{{ $value }}'>

					@if (!empty($addonsConfig) && $addonsConfig['position'] === 'right')
					<span class="input-group-addon addon-middle-side">{{ $addonsConfig['text'] }}</span>
					@endif

					@if ($isFirst)
					<button type="button" class="btn btn-primary addon-right-side multipleInput_addRowBtn-{{ $name }}"><span class="fas fa-plus"></span></button>
					@else
					<button type="button" class="btn btn-danger addon-right-side multipleInput_removeRowBtn"><span class="fas fa-close"></span></button>
					@endif
					@php
						$isFirst = false;
					@endphp
				</div>
				@endforeach

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
	$('body').on('click', '.multipleInput_removeRowBtn', function(){
		console.log('remove-clicked');
		$(this).closest('div.input-group').remove()
	})

	$('.multipleInput_addRowBtn-{{  $name  }}').click(function(){
		$(this).closest('.multipleInput_container').append(
			'<div class="input-group {{$addonsConfig['position']}}" style="margin-bottom: 10px">' +
			@if (!empty($addonsConfig) && $addonsConfig['position'] === 'left')
			'<span class="input-group-addon addon-left-side">{{$addonsConfig['text']}}</span>' + 
			@endif
			'<input type="{{$type}}" name="{{$name}}[]" class="form-control" <?= $formattedAttributes ?>>' + 
			@if (!empty($addonsConfig) && $addonsConfig['position'] === 'right')
			'<span class="input-group-addon addon-middle-side">{{$addonsConfig['text']}}</span>' + 
			@endif
			'<button type="button" class="btn btn-danger addon-right-side multipleInput_removeRowBtn"><span class="fas fa-close"></span></button>' +
			'</div>')
	})
</script>
@endpush