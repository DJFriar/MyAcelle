@extends('layouts.core.frontend', [
	'menu' => 'funnel',
])

@section('title', trans('store.funnel'))

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">format_list_bulleted</span>
                {{ trans('store.sms_funnel') }}
            </span>
		</h1>
	</div>

@endsection

@section('content')

<div class="align-items-sm-center">
    <div class="row">
        <div class="col-md-6 col-12">
            <form id="sendingserverCreate"  method="POST"  enctype="multipart/form-data" action="{{ action('Store\FunnelController@update',[
                        'funnel' => $funnel,
                        'page' => request()->page
                    ]) }}" method="POST">
                {{ csrf_field() }} 
                @method('PUT')        
                
                @include('store.funnels._form',[ 'title' => 'Update Template']) 
                
                <div class="col-12 mt-2 my-3 ">
                    <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-float waves-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-save"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        {{ trans('store.sms_funnel.save') }}
                    </button>
                    <a href="{{ action('Store\FunnelController@index') }}" class="btn btn-link ms-2 ">
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
    });
</script>
@endsection