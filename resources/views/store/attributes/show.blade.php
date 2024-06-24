@extends('layouts.core.frontend', [
	'menu' => 'sms_tracking_log',
])

@section('title', trans('store.sms_sending_servers'))

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">format_list_bulleted</span>
                {{ trans('store.sms_sending_servers') }}
            </span>
		</h1>
	</div>

@endsection

@section('content')
<div class="card"> 
    <div class="card-body  align-items-sm-center ">
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8"> 
                   <h2 class="text-semibold">Server Infomation</h2>
    
                    <div class="sub-section">         
                        <p>
                            Thank you for use my service, the sms content has changsed is:
                       </p>    
                        <ul class="dotted-list topborder section">                                
                            <li>
                                <div class="unit size1of2"> <strong>Tranfer  ID</strong> </div>
                                <div class="lastUnit size1of2">  <strong>{{ $sendingServer->id }}</strong></div>
                            </li>
                            <li>
                                <div class="unit size1of2"> <strong>name</strong> </div>
                                <div class="lastUnit size1of2">  <strong>{{ $sendingServer->name }}</strong></div>
                            </li>
                            <li class="selfclear">
                                <div class="unit size1of2"><strong>Type</strong></div>
                                <div class="lastUnit size1of2"><strong>{{ $sendingServer->type }}</strong></div>
                            </li>
                            <li class="selfclear">
                                <div class="unit size1of2"><strong>quota</strong></div>
                                <div class="lastUnit size1of2">{{ $sendingServer->quota }}</div>
                            </li> 
                        </ul> 
                        <div class="sub-section"> 
                          
                                <a  href="{{ action('Sms\SmsTemplateController@index', [
                                        'page' => request()->page,
                                    ]) }}" class="btn btn-secondary me-1">
                                        Return Back
                                </a> 

                                <form action="{{ action('Sms\SmsTemplateController@destroy', [
                                    'sms_sending_server' => $sendingServer,
                                ]) }}" method="POST" class="d-inline-block">
                                    @method('DELETE ') 
                                    @csrf 
                                    
                                    <button type="submit" class="btn btn btn-danger me-2">                         
                                        <span class="material-symbols-rounded">delete_outline</span>  delete
                                    </button> 
                                </form>

                        </div>

                    </div>
                 </div> 
            </div>
        </div> 
    </div> 
</div> 
@endsection