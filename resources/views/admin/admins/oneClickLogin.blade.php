@extends('layouts.popup.medium')

@section('bar-title')
    {{ trans('messages.admin.one_cick_login') }}
@endsection

@section('footer')
    <div class="text-end">
        <button id="CopyOneClickLoginUrl" class="btn btn-secondary me-1" type="button">
            <span class="material-symbols-rounded">
                content_copy
                </span> {{ trans('messages.admin.one_cick_login.copy') }}
        </button>
        <button class="btn btn-default close" type="button">
            {{ trans('messages.close') }}
        </button>
    </div>
@endsection

@section('content')
    
    <div id="OneClickLoginContainer" class="">
        <div class="border rounded bg-light">
            <div style="overflow:auto;padding: 6px 8px;">
                {{ $url }}
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('#CopyOneClickLoginUrl').on('click', function() {
                copyToClipboard('{{ $url }}', $('#OneClickLoginContainer'));
                notify('success', '{{ trans('messages.notify.success') }}', '{{ trans('messages.admin.one_cick_login.copied') }}');
            });
        })
    </script>
@endsection 