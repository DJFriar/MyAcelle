@extends('layouts.core.page')

@section('title', trans('messages.error'))


@section('content')
  <div class="row">
    <div class="col-md-12 tex-center">
        <div style="margin: 100px auto; width: 400px;">
            <a href="javascript:;" onclick="window.history.back()" class="btn btn-primary">{{ trans('messages.try_again') }}</a>
        </div>
    </div>
  </div>

@endsection