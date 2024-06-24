@extends('layouts.core.frontend', [
	'menu' => 'funnel',
])

@section('title', trans('store.sms_funnel'))

@section('page_header')
	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ action("FunnelController@index") }}">{{ trans('store.sms_funnel') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">sms</span>
                {{ trans('store.sms_funnel.create.createnew') }}
            </span>
		</h1> 
	</div>
@endsection

@section('content')
    <form id="sendingserverCreate" action="{{ action('Store\FunnelController@store') }}" enctype="multipart/form-data" method="POST">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-md-8">
                
                @include('store.funnels._form',[ 'title' => 'Create Functional']) 

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ trans('messages.save') }}</button>
                    <a href="{{ action('Store\FunnelController@index') }}" class="btn btn-light">{{ trans('messages.cancel') }}</a>
                </div>
            </div>
        </div> 
    </form> 
 <script>
    $('select[name="tags"]').change(function() {
        var $option = $(this).find('option:selected');
        var value = $option.val() ;
        var text = $option.text() ;
        var curPos =   document.getElementById("message").selectionStart;
        let x = $("#message").val(); 
        $("#message").val(
            x.slice(0, curPos) + '{' + value + '}' + x.slice(curPos)
        );
    }); 
 </script>
@endsection