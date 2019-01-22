@php
if (!is_array($attributes)) $attributes = [];
$attributes['orientation'] = $attributes['orientation'] ?? 'horizontal'; 
$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes);
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

			@if (strtolower($config['orientation']) == 'horizontal')
			<div class="m-radio-inline">
					
				@foreach ($options as $key => $option)
				<label class="m-radio">
					{{ Form::radio($name, $key, $key == $value) }} {{ ucwords($option) }}
					<span></span>
				</label>
				@endforeach

			</div>
			@elseif (strtolower($config['orientation']) == 'vertical')
            <div class="m-radio-list">
				@foreach ($options as $key => $option)
                    <label class="m-radio">
						{{ Form::radio($name, $key, $key == $value) }} {{ ucwords($option) }}
						<span></span>
                    </label>
				@endforeach
            </div>
			@endif

            {!! $config['info'] !!}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>