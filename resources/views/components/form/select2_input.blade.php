@php
if (!is_array($attributes)) $attributes = [];

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$pluginOptions = null;
if (isset($attributes['pluginOptions'])) {
	$pluginOptions = $attributes['pluginOptions'];
	unset($attributes['pluginOptions']);
}

$labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');
unset($attributes['labelText']);

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

$configAttributes['id'] = $configAttributes['id'] ?? 'select2-' . $name;

$formattedAttributes = '';
foreach ($configAttributes as $attribute => $attributeValue) {
	$formattedAttributes .= $attribute . '="' . $attributeValue . '" ';
}

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

			<select class="select2 form-control" name="{{ isset($configAttributes['multiple']) ? $name . '[]' : $name }}" <?= $formattedAttributes ?>>
				<option></option>
				@foreach ($options as $key => $option)
	                <option value="{{ $key }}">{{ $option }}</option>
				@endforeach
            </select>

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		</div>
	</div>
	@endif
</div>

@push('vendor-css')
<link href="{{asset('assets/vendor/select2/select2.min.css')}}" rel="stylesheet" media="all">
@endpush

@push('vendor-js')
<script type="text/javascript" src="{{ asset('assets/vendor/select2/select2.min.js')}}"></script>
<script type="text/javascript">
	var select2Options_{{$name}} = {
			placeholder: 'Select...',
	    	allowClear: true,
		}
	var select2val_{{$name}} = {{ !is_array($value) ? json_encode([$value]) : json_encode($value) }}

	@if (isset($pluginOptions['dropdownParent']))
	    select2Options_{{$name}}.dropdownParent = $('#<?= $pluginOptions["dropdownParent"] ?>')
	@endif
	@if(isset($configAttributes['multiple']))
		select2Options_{{$name}}.multiple = true
	@endif

    $('#{{$configAttributes["id"]}}').select2(select2Options_{{$name}}).val(select2val_{{$name}}).trigger('change');
</script>
@endpush