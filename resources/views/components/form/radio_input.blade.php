@php
if (!is_array($attributes)) $attributes = [];

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$orientation = 'horizontal';
if (isset($attributes['orientation'])) {
	$orientation = $attributes['orientation'];
	unset($attributes['orientation']);
}

$labelText = $labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');;

$formAlignment = 'vertical';
if (isset($attributes['formAlignment'])) {
	$formAlignment = $attributes['formAlignment'];
	unset($attributes['formAlignment']);
}


$labelContainerClass = $formAlignment === 'vertical' ? 'col-md-12' : 'col-md-3';
$inputContainerClass = $formAlignment === 'vertical' ? 'col-md-12' : 'col-md-9';
if ($formAlignment === 'horizontal') {
	if (isset($attributes['labelContainerClass'])) {
		$labelContainerClass = $attributes['labelContainerClass'];
		unset($attributes['labelContainerClass']);
	}
	if (isset($attributes['inputContainerClass'])) {
		$inputContainerClass = $attributes['inputContainerClass'];
		unset($attributes['inputContainerClass']);
	}
}


$configAttributes = array_merge([
	'class' => 'form-control',
], $attributes);

@endphp

<div class="form-group {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($useLabel)
	<div class="row">
		<div class="{{ $labelContainerClass }}">
			<label class="form-control-label">
				{!! $labelText !!}
			</label>
		</div>
		<div class="{{ $inputContainerClass }}">
	@endif

			@if (strtolower($orientation) == 'horizontal')
				@foreach ($options as $key => $option)
				{{ Form::radio($name, $key, $key == $value ? true : false) }} {{ ucwords($option) }}
				@endforeach

			@elseif (strtolower($orientation) == 'vertical')
				@foreach ($options as $key => $option)
                    <div class="radio">
                        <label for="radio{{$key}}" class="form-check-label ">
							{{ Form::radio($name, $key, $key == $value ? true : false) }} {{ ucwords($option) }}
                        </label>
                    </div>
				@endforeach
			@endif

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		</div>
	</div>
	@endif
</div>