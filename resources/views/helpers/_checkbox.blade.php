<!-- value="{{ false }}" will result in value="", so it is safe to set it to 0 in case of false -->
<input type="hidden" name="{{ $name }}" value="{{ ($options[0] == false) ? 0 : $options[0] }}" />
<div class="d-flex">
	
		@if (!empty($label)) <div style="width:100%"><label class="me-5">
			
				{!! $label !!}
			

				@if (isset($help_class) && Lang::has('messages.' . $help_class . '.' . $name . '.help'))
					<span class="checkbox-description mt-1">
						{!! trans('messages.' . $help_class . '.' . $name . '.help') !!}
					</span>
				@endif

			</label></div>
		@endif
	
	<div class="d-flex align-items-top">
		<label class="checker">
			<input{{ $value == $options[1] ? " checked" : "" }}
			{{ isset($disabled) && $disabled == true ? ' disabled="disabled"' : "" }}
			type="checkbox" name="{{ $name }}" value="{{ $options[1] }}"
			class="styled4 {{ $classes }} {{ isset($class) ? $class : "" }}" data-on-text="On" data-off-text="Off" data-on-color="success" data-off-color="default">
			<span class="checker-symbol"></span>
		</label>
	</div>
</div>
	


