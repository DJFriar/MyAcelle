<div class="row">
    @foreach ($attributes as $attribute)
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">{{ $attribute->name }}</label>
                <input type="text" name="product_attributes[{{ $attribute->uid }}]"
                    value="{{ $product->getValueByAttribute($attribute) }}" class="form-control">
            </div>
        </div>
    @endforeach
</div>