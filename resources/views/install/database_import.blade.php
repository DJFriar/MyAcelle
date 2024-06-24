@extends('layouts.core.install')

@section('title', trans('messages.database'))

@section('content')

<h3 class="text-primary"><span class="material-symbols-rounded">dns</span> {{ trans('messages.database_configuration') }}</h3>

    <p class="">
        {{ trans('messages.install.db.setup', ['db' => $database["database_name"]]) }}
    </p>

    <div class="alert alert-danger">
        {{ trans('messages.install.init_db_warning') }}
    </div>

    <div class="text-end">
        <a href="{{ action('InstallController@database') }}" class="btn btn-secondary me-1"><span class="material-symbols-rounded">undo</span> {!! trans('messages.back') !!}</a>
        <a href="{{ action('InstallController@import') }}" class="btn btn-primary db-setup">{{ trans('messages.install.setup_database') }} <span class="material-symbols-rounded">east</span></a>
		
    </div>

    <script>

        $(function() {
            $('.db-setup').on('click', function() {
                addButtonMask($(this));
            });
        });

    </script>

@endsection
