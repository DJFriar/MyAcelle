<h4>{{ $title}}</h4>
<div class="border px-4 py-3 rounded shadow-sm bg-white mb-4">
    <div class="mb-4"> 
        <label for="parent" class="form-label">{{ trans('store.categories.parent') }}</label> 
        <select name="parent" id="parent" class="form-control"> 
            @foreach($parents as $key => $parent) 
                @if( $category->id == $parent->id )
                    <option value={{ $parent->id }}" selected>{{ $parent->name }}</option>
                @else
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endif
            @endforeach
        </select> 
    </div>
    <div class="mb-4"> 
        <label class="form-label required" for="name">{{ trans('store.categories.name') }}</label>
        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ $category->name }}" name="name">
        @if ($errors->has('name')) 
            <div class="invalid-feedback"> {{ $errors->first('name') }} </div>
        @endif
        <div id="emailHelp" class="form-text">  {{ trans('store.categories.name') }} </div> 
    </div>  
    <div class="mb-4"> 
        <label class="form-label required" for="description">{{ trans('store.categories.description') }}</label>
        <textarea  id="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
            name="description"
        >{{ $category->description }}</textarea>
        @if ($errors->has('description'))
            <div class="invalid-feedback"> {{ $errors->first('description') }} </div>
        @endif
        <div id="messagelHelp" class="form-text">   {{  trans('store.categories.remaining') }}  </div> 
    </div>
</div> 