@extends('layouts.core.frontend', [
	'menu' => 'attributes',
])

@section('title', trans('store.attributes'))

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">format_list_bulleted</span>
                {{ trans('store.attributes') }} 
            </span>
		</h1>
	</div>

@endsection

@section('content')

<div class="align-items-sm-center">
    <div class="row">
        <div class="col-md-6 col-12">
            <form id="sendingserverCreate"  method="POST"  action="{{ action('Store\AttributeController@update',[
                        'attribute' => $attribute,
                        'page' => request()->page
                    ]) }}" method="POST">
                {{ csrf_field() }} 
                @method('PUT')        
                
                @include('store.attributes._form',[ 'title' => 'Update Attribute']) 
                
                <div class="col-12 mt-2 my-3 ">
                    <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-float waves-light">
                        {{ trans('store.attributes.save') }}
                    </button>
                    <a href="{{ action('Store\AttributeController@index') }}" class="btn btn-link ms-2 ">
                        {{ trans('messages.cancel') }}
                    </a>
                </div>  

            </form>                        
        </div> 
    </div>
       
</div>  

 
<script>
$(document).ready(function () {
    $('select[name="tags"]').change(function() {
        var $option = $(this).find('option:selected');
        var value = $option.val() ;
        var text = $option.text() ;
        var curPos =   document.getElementById("messange").selectionStart;
        let x = $("#messange").val(); 
        $("#messange").val(
            x.slice(0, curPos) + '{' + value + '}' + x.slice(curPos)
        );
    });
    /*
        load leel 2 category
    */ 
   $('.select').change( function(){ 
        var _this = this;
        var option = $(this).find('option:selected'); 
        var text = option.text(); 
        $.ajax({
            url: '{{ action("CategoryController@collection") }}',
            type: 'GET',
            data: {
                id: option.val(), 
                level: 2,
            }
        }).done(function(response){
            $( document.getElementById('subcat') ).html(response); 
            
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log('Eror load');
        });
   });
   /*
        marrt the correct category in lisst
   */
   

});
</script>
@endsection