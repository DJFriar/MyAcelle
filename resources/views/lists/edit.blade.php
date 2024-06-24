@extends('layouts.core.frontend', [
	'menu' => 'list',
])

@section('title', $list->name)

@section('head')
    <link href="{{ AppUrl::asset('core/emojionearea/emojionearea.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ AppUrl::asset('core/emojionearea/emojionearea.min.js') }}"></script>
@endsection

@section('page_header')

    @include("lists._header")

@endsection

@section('content')

    @include("lists._menu", [
		'menu' => 'edit',
	])

    <form action="{{ action('MailListController@update', $list->uid) }}" method="POST" class="form-validate-jqueryz">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PATCH">

        @include("lists._form")
        <hr>
        <div class="text-left">
            <button class="btn btn-secondary me-2"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
            <a href="{{ action('MailListController@index') }}" class="btn btn-link"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
        </div>
    </form>
@endsection
