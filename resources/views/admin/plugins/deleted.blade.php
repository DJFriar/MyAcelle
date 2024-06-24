@extends('layouts.core.backend', [
    'menu' => 'plugin',
])

@section('title', trans('paddle::messages.paddle'))

@section('content')    
    <h4 class="mt-5 pt-2 font-weight-semibold">{{ trans('messages.plugin.deleted', [
        'plugin' => $pluginName,
    ]) }}</h4>
    <p>
        {!! trans('messages.plugin.deleted.wording') !!}
    </p>

    <a href="{{ action('Admin\PluginController@index') }}" class="link-underline">{{ trans('messages.plugin.back_to_plugins') }}
    </a>
@endsection