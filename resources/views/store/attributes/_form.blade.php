<h4>{{ $title}}</h4>
<div class="border px-4 py-3 rounded shadow-sm bg-white mb-4">

    <div class="mb-4">
        <label for="categories" class="form-label">{{ trans('store.sms_category') }}</label> 
        <select name="category_id" id="category_id" class=" form-control"> 
            @foreach($categories as $key => $category) 
                <option value="{{ $category->id }}" 
                        {{ $attribute->categories_id == $category->id ? 'selected':''}} >
                        {{ $category->name ?? '' }}
                </option>
                @if($category->children)                    
                    @forEach($category->children as $child) 
                    <option value="{{ $category->id }}"
                        {{ $attribute->categories_id == $child->id ? 'selected':''}} >
                        -> {{ $child->name ?? '' }}
                    </option>    
                    @endforeach
                @endif
            @endforeach
        </select>
        <div class="category-sub" id="subcat">
        </div>
    </div>

    <div class="mb-4"> 
        <label class="form-label required" for="name">{{ trans('store.attributes.name') }}</label>
        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ $attribute->name }}" name="name">
        @if ($errors->has('name')) 
            <div class="invalid-feedback"> {{ $errors->first('name') }} </div>s
        @endif
        <div id="emailHelp" class="form-text">  {{ trans('store.attributes.name') }} </div> 
    </div>
 
  
    <div class="mb-4"> 
        <label class="form-label required" for="description">{{ trans('store.attributes.description') }}</label>
        <textarea  id="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
            name="description"
        >{{ $attribute->description }}</textarea>

        @if ($errors->has('description')) 
            <div class="invalid-feedback"> {{ $errors->first('description') }} </div>
        @endif
        <div id="descriptionlHelp" class="form-text">   {{  trans('store.attributes.remaining') }}  </div> 
    </div>

</div>

