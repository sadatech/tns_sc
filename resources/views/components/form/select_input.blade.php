@php
if (!is_array($attributes)) $attributes = [];

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');

$formAlignment = 'horizontal';
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
		<div class=" {{ $labelContainerClass }}">
			<label class="col-form-label">
				{!! $labelText !!}
			</label>
		</div>
		<div class="{{ $inputContainerClass }}">
	@endif

			{{ Form::select($name, $options, $value, $configAttributes) }}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		</div>
	</div>
	@endif
</div>