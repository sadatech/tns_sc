@php
if (!is_array($attributes)) $attributes = [];
$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes);
$id     = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '',bcrypt($name) );
@endphp

<div class="form-group custom-file m-bot-15 {{ $config['groupClass'] ? $config['groupClass'] : '' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
	<div class="row">
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif
			@if (!empty($config['addonsConfig']))
			<div class="input-group">
				@if ($config['addonsConfig']['position'] === 'left')
				<span class="input-group-addon addon-left-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			@endif
				{{ 
					Form::file( $name, array_merge( 
							[
								'id' => $id, 
							],
							$config['elOptions'],
							[
								'data-toggle' =>'custom-file-input',
								'multiple'    =>'multiple',
								'class'       =>'custom-file-input'. (isset($config['elOptions']['class']) ? ' '.$config['elOptions']['class'] : '')
							]
						) 
					) 
				}}
				<label class="custom-file-label" for="{{ $id }}example-file-multiple-input-custom">Choose files</label>
			@if (!empty($config['addonsConfig']))
				@if ($config['addonsConfig']['position'] === 'right')
				<span class="input-group-addon addon-right-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			</div>
			@endif

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>