<label class="checker">
    @if(isset($unchecked_option))
        <input type="hidden" name="{{ $name }}" value="{{ $unchecked_option }}" />
    @endif
    <input
        type="checkbox"
        name="{{ $name }}"
        value="{{ $option }}" class="styled4"
        {{ $value == $option ? 'checked' : '' }}
        {{ isset($disabled) && $disabled == true ? ' disabled="disabled"' : "" }}
    >
    <span class="checker-symbol"></span>
</label>