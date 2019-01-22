@php
if (!is_array($attributes)) $attributes = [];
$isDataRequestByAjax = is_array($options) ? false : true;
$url = $options;

// SET DEFAULT CLASS
$attributes['elOptions']['class'] = 'select2 form-control';

// SET DEFAULT ID
$attributes['elOptions']['id'] = $attributes['elOptions']['id'] ?? 'select2-' . $name;

// SET DEFAULT FOR FORMATTED SELECT2 DATA FORMAT
$attributes['text'] = $attributes['text'] ?? 'obj.name';
$attributes['key'] = $attributes['key'] ?? 'obj.id';

// CALLING SETUP DEFAULT CONFIG
$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes, true);
$config['pluginOptions'] = $attributes['pluginOptions'] ?? [];

// FORMATTING TEXT BY TEMPLATE 
// if (is_array($config['text'])) {
// 	$text = null;
// 	foreach ($config['text']['field'] as $field) {
// 		$text = str_replace("<<$field>>", "'+ obj.$field +'", $text ?? $config['text']['template']);
// 	}
// str_replace_array('<<field>>', $config['text']['field'], $config['text']['template']); // Laravel str helper method 
// 	$config['text'] = "'" . $text . "'";
// }
@endphp

<div class="form-group {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
	<div class="row">
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="{{ $config['labelClass'] }}">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif

			<select name="{{ isset($config['pluginOptions']['multiple']) && $config['pluginOptions']['multiple'] ? $name . '[]' : $name }}" <?= $config['htmlOptions'] ?>>
				@if (!$isDataRequestByAjax)
					<option></option>
					@foreach ($options as $key => $option)
		                <option value="{{ $key }}">{{ $option }}</option>
					@endforeach
				@endif
            </select>

            {!! @$config['info'] !!}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>

@push('additional-js')
<script type="text/javascript">
	$(document).ready(function() {
		var select2Options_{{$name}} = Object.assign({
				placeholder: "{{ $config['elOptions']['placeholder'] }}",
		    	allowClear: true,//
			}, {!! json_encode($config['pluginOptions']) !!}),
			select2val_{{$name}} = {!! !is_array($value) ? json_encode([$value]) : json_encode($value) !!}

		// IF THE SELECT2 IS REQUEST DATA BY AJAX
		@if ($isDataRequestByAjax)
		select2Options_{{$name}}.ajax = {
			url: "{{ $url }}",
			processResults: function (data) {
				var result = {},
					isPaginate = data.hasOwnProperty('data'),
					isSimplePaginate = !data.hasOwnProperty('last_page');

	                result.results = $.map(isPaginate ? data.data : data, function (obj) {                                
	                    return {id: {!! $config['key'] !!}, text: {!! $config['text'] !!} }
	                })

	                if (isPaginate) {
	                	result.pagination = {
		                	more: isSimplePaginate ? data.next_page_url !== null : data.current_page < data.last_page
		                }
	                }

				return result;
			}
		}
		@endif

		// FOR SELECT2 DROPDOWNPARENT
		@if (isset($config['pluginOptions']['dropdownParent']))
		select2Options_{{$name}}.dropdownParent = $('<?= $config['pluginOptions']["dropdownParent"] ?>')
		@endif

		$('#{{ $config['elOptions']['id'] }}').select2(select2Options_{{$name}})
		@if (!empty($value))
		$('#{{ $config['elOptions']['id'] }}').select2("trigger", "select", {
			data: { id: "{{ $value[0]}}", text: "{{ $value[1] }}" }
		});

        // scroll top
        setTimeout(function() {
	        window.scrollTo(0, 0);
	        $('html, body').scrollTop();
        }, 200);
	    @endif
	})
</script>
@endpush