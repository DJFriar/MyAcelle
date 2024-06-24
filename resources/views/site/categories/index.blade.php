@extends('layouts.core.frontend', [
    'menu' => false,
])

@section('title', trans('messages.brand.shop_info'))

@section('head')
	<script type="text/javascript" src="{{ AppUrl::asset('core/tinymce/tinymce.min.js') }}"></script>        
    <script type="text/javascript" src="{{ AppUrl::asset('core/js/editor.js') }}"></script>

    <script src="{{ AppUrl::asset('core/js/UrlAutoFill.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ AppUrl::asset('core/css/vbrand.css') }}">
@endsection
	
@section('page_header')
	
    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ action('Site\CategoryController@index') }}">{{ trans('messages.brand.categories') }}</a></li>
        </ul>
    </div>

@endsection

@section('content')
    <iframe id="wpFrame" src="{{ config('wordpress.url') }}/wp-admin/edit-tags.php?taxonomy=product_cat&post_type=product"></iframe>
@endsection
