<div class="row">
    <div class="col-md-6 operator-col">
        <div class="form-group">
            <select class="select" name="conditions[{{ $index }}][operator]">
                @foreach (Acelle\Model\Segment::dateOperators() as $option)
                    <option {{ isset($operator) && $operator == $option['value'] ? 'selected' : '' }} value="{{ $option['value'] }}">{{ $option['text'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 value-col">
        <div class="form-group {{ $errors->has('conditions.'.$index.'.value') ? 'has-error' : '' }}">
            <div class="input-icon-right position-relative">											
                <input type="text" name="conditions[{{ $index }}][value]" value="{{ isset($value) ? $value : '' }}" class="control-with-mask pickadate-control form-control pickadate">
                <span class="mask-control date-mask-control"></span>
                <span class="date-input-icon"><span class="material-symbols-rounded">event</span></span>
            </div>
        </div>
    </div>
</div>