<h4>{{ $title}}</h4>
<div class="border px-4 py-3 rounded shadow-sm bg-white mb-4">
    <div class="mb-4"> 
        <label class="form-label required" for="name">{{ trans('store.sms_funnel.name') }}</label>
        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ $funnel->name }}" name="name">
        @if ($errors->has('name')) 
            <div class="invalid-feedback"> {{ $errors->first('name') }} </div>
        @endif
        <div id="emailHelp" class="form-text">  {{ trans('store.sms_funnel.name') }} </div> 
    </div> 
  
    <div class="mb-4"> 
        <label class="form-label required" for="message">{{ trans('store.sms_funnel.message') }}</label>
        <textarea  id="message" class="form-control {{ $errors->has('message') ? 'is-invalid' : '' }}"
            name="message"
        >{{ $funnel->message }}</textarea>

        @if ($errors->has('message'))
            <div class="invalid-feedback"> {{ $errors->first('message') }} </div>
        @endif
        <div id="messagelHelp" class="form-text">   {{  trans('store.sms_funnel.remaining') }}  </div> 
    </div>

</div>
@if($funnel->file !='')  
 
<div class="border px-4 py-3 rounded shadow-sm bg-white mb-4"> 
    <div class="mb-3">
        <label for="picture" class="form-label">Image</label> 
    </div> 
    <div class="mb-3">
        <img src="{{ asset('storage/funnels/'.$funnel->file)  }}" alt="" title="" width="100%">
    </div>  
    <div class="mb-3">
        <label for="picture" class="form-label">Change Image</label>
        <input class="form-control" type="file" id="picture" name="picture">
    </div> 
</div>

@else
<div class="border px-4 py-3 rounded shadow-sm bg-white mb-4"> 
    <div class="mb-3">
        <label for="picture" class="form-label">Image</label>
        <input class="form-control" type="file" id="picture" name="picture">
    </div> 
</div>


@endif