@extends('layouts.core.frontend', [
	'menu' => 'products',
])

@section('title', trans('store.product'))

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ AppUrl::asset('core/css/laza.css') }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
@endsection
 
@section('page_header') 
    <div class="page-home-content">     
        <div class="row">
            <div class="col-lg-12"> 
                <div class="page-title py-0"> 
                    <nav aria-label="Breadcrumb" class="Breadcrumbnew">
                        <ul class="breadcrumb breadcrumb-caret position-right">
                            <li class="breadcrumb-item"><a class="text-muted2" href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
                            <li class="breadcrumb-item"><a class="text-muted2" href="{{ action("Store\ProductController@index") }}">{{  trans('store.product') }}</a></li>
                            <li class="breadcrumb-item">{{  trans('store.product.create.createnew') }}</li>
                        </ul>
                    </nav> 
                    <div class="title-area d-flex justify-content-between align-items-center"> 
                        <h3 class="title-head py-0" style="font-size: 24px;color: rgba(0,0,0,.65);font-weight: 600;">
                            Thêm sản phẩm
                        </h3>  
                    </div> 
                </div>
            </div> 
        </div>
    </div>
@endsection

@section('content')
  
<div class="align-items-sm-center"> 
    <form id="ProductForm" action="{{ action('Store\ProductController@store') }}"  enctype="multipart/form-data" method="POST">
        {{ csrf_field() }}

        @include('store.products._form')

    </form>
</div>  

<script>
    $(function() {
        // Editor for product content
        ClassicEditor
            .create( document.querySelector( '#content' ) )
            .catch( error => {
                console.error( error );
            } );
    })
</script>
@endsection