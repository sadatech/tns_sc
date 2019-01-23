@php
if (!is_array($attributes)) $attributes = [];

$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes);

if ($values === null || $values === []) $values = [''];

$isFirst = true;
@endphp

<div class="form-group {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
	<div class="row">
		<div class="col col-md-3">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="col-12 col-md-9">
	@endif
			<div class="multipleInput_container">

				@foreach ($values as $value)
				<div class="input-group" style="margin-bottom: 10px">
					@if (!empty($config['addonsConfig']) && $config['addonsConfig']['position'] === 'left')
					<span class="input-group-addon addon-left-side">{{ $config['addonsConfig']['text'] }}</span>
					@endif

					<input type="{{ $type }}" name="{{$name}}[]" <?= $config['htmlOptions'] ?> value='{{ $value }}'>

					@if (!empty($config['addonsConfig']) && $config['addonsConfig']['position'] === 'right')
					<span class="input-group-addon addon-middle-side">{{ $config['addonsConfig']['text'] }}</span>
					@endif

					@if ($isFirst)
					<button type="button" class="btn btn-primary addon-right-side multipleInput_addRowBtn-{{ $name }}"><span class="fas fa-plus"></span></button>
					@else
					<button type="button" class="btn btn-danger addon-right-side multipleInput_removeRowBtn"><span class="fa fa-times"></span></button>
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
	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>

@push('additional-js')
<script type="text/javascript">
	$('body').on('click', '.multipleInput_removeRowBtn', function(){
		$(this).closest('div.input-group').remove()
	})

	$('.multipleInput_addRowBtn-{{  $name  }}').click(function(){
		$(this).closest('.multipleInput_container').append(
			'<div class="input-group {{$config['addonsConfig']['position']}}" style="margin-bottom: 10px">' +
			@if (!empty($config['addonsConfig']) && $config['addonsConfig']['position'] === 'left')
			'<span class="input-group-addon addon-left-side">{{$config['addonsConfig']['text']}}</span>' + 
			@endif
			'<input type="{{$type}}" name="{{$name}}[]" class="form-control" <?= $config['htmlOptions'] ?>>' + 
			@if (!empty($config['addonsConfig']) && $config['addonsConfig']['position'] === 'right')
			'<span class="input-group-addon addon-middle-side">{{$config['addonsConfig']['text']}}</span>' + 
			@endif
			'<button type="button" class="btn btn-danger addon-right-side multipleInput_removeRowBtn"><i class="fa fa-times"></i></button>' +
			'</div>')
	})
</script>
@endpush