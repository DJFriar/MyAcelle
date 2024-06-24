@extends('layouts.core.frontend', [
	'menu' => 'subscriber',
])

@section('title', trans('messages.list.no_list'))

@section('content')
    <div class="pt-5 mt-4 text-center">
        <h1>{{ trans('messages.list.you_have_no_list') }}</h1>
        <p class="fs-6">{{ trans('messages.subscriber.create_list_to_see') }}</p>
        <a href="{{ action('MailListController@index') }}" class="btn btn-primary rounded-pill shadow py-2 px-4">{{ trans('messages.list.go_to_mail_lists') }}</a>

        <div class="mt-5"><img width="300" src="{{ url('images/icons/SVG/no-list.svg') }}" />
    </div>
@endsection
