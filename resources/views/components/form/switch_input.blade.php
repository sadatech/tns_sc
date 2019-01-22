@php
if (!is_array($attributes)) $attributes = [];

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$labelText = $labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');

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
	'class' => "m_switch_check",
], $attributes);

$formattedAttributes = '';
foreach ($configAttributes as $attribute => $attributeValue) {
	$formattedAttributes .= $attribute . '="' . $attributeValue . '" ';
}
@endphp

<div class="form-group {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($useLabel)
	<div class="row">
		<div class="{{ $labelContainerClass }}">
			<label class="col-form-label">
				{!! $labelText !!}
			</label>
		</div>
		<div class="{{ $inputContainerClass }}">
	@endif

			<input type="checkbox" name="{{$name}}" value="{{$value}}" {{ old($name) ? "checked" : '' }} <?= $formattedAttributes ?>>

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		</div>
	</div>
	@endif
</div>

@push('vendor-css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/mswitch/css/jquery.mswitch.css') }}">
@endpush

@push('vendor-js')
<script type="text/javascript" src="{{ asset('assets/vendor/mswitch/js/jquery.mswitch.js') }}"></script>
<script type="text/javascript">
	@if (isset($configAttributes['id']))
		$("#{{ $configAttributes['id'] }}").mSwitch();
	@else
		$(".m_switch_check:checkbox").mSwitch();
	@endif
</script>
@endpush
