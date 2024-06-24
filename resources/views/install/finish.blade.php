@extends('layouts.core.install')

@section('title', trans('messages.finish'))

@section('content')

        <h4 class="text-primary fw-600 mb-3"><span class="material-symbols-rounded me-2">task_alt</span> {{ trans('messages.install.congrats') }}</h4>
        {{ trans('messages.install.finish') }} <a class="text-semibold" href="{{ action('Admin\HomeController@index') }}">{{ action('Admin\HomeController@index') }}</a>

        <div class="clearfix"><!-- --></div>      
<br />

@endsection
