@extends('layouts.core.backend', [
	'menu' => 'public_view',
])

@section('title', trans('messages.plans'))

@section('head')
    <script type="text/javascript" src="{{ AppUrl::asset('core/js/group-manager.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title" style="padding-bottom:0">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold">{{ trans('messages.plan.public_view.title') }}</span>
        </h1>
    </div>

@endsection

@section('content')

    @include('admin.plans.publicView.' . $style, [
        'plans' => $plans,
    ])

@endsection
