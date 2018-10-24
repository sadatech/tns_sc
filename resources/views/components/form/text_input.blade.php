@php
if (!is_array($attributes)) $attributes = [];

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');

$formAlignment = 'vertical';
if (isset($attributes['formAlignment'])) {
	$formAlignment = $attributes['formAlignment'];
	unset($attributes['formAlignment']);
}

$addonsConfig = isset($attributes['addons']) ? $attributes['addons'] : null;
unset($attributes['addons']);


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
	'placeholder' => "Please enter " . implode(' ', explode('_', $name)) . " here"
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
			@if (!empty($addonsConfig))
			<div class="input-group">
				@if ($addonsConfig['position'] === 'left')
				<span class="input-group-addon addon-left-side">{{ $addonsConfig['text'] }}</span>
				@endif
			@endif

				{{ Form::text($name, $value, $configAttributes) }}

			@if (!empty($addonsConfig))
				@if ($addonsConfig['position'] === 'right')
				<span class="input-group-addon addon-right-side">{{ $addonsConfig['text'] }}</span>
				@endif
			</div>
			@endif

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		</div>
	</div>
	@endif
</div>