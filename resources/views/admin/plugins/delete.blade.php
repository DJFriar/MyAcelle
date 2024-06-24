@extends('layouts.core.backend', [
    'menu' => 'plugin',
])

@section('title', trans('paddle::messages.paddle'))

@section('content')
    <div class="topfix-header mb-4">
        <div class="topfix-container d-flex align-items-center shadow-sm bg-light">
            <div class="d-flex py-4">
                <div class="me-3 pe-2">
                    <img class="rounded-3" src="{{ $plugin->getIconUrl() }}" width="80px" />
                </div>
                <div>
                    <h4 class="mb-2 font-weight-semibold d-flex align-items-center">{{ $plugin->title }}</h4>
                    <p class="mb-1">{{ $plugin->description }}</p>
                    <p class="mb-3"><span class="small d-block">
                        {{ trans('messages.plugin.name') }}: 
                        <span class="font-weight-semibold">{{ $plugin->name }}</span> | 
                        {{ trans('messages.plugin.version') }}: 
                        <span class="font-weight-semibold">{{ $plugin->version }}</span>
                    </span></p>
                    <div class="d-flex">
                        @if (Auth::user()->admin->can('enable', $plugin))
                            <a link-confirm="{{ trans('messages.enable_plugin_confirm') }}"
                                href="{{ action('Admin\PluginController@enable', ["uids" => $plugin->uid]) }}"
                                class="small link-underline"
                            >
                                {{ trans('messages.enable') }}
                            </a> <span class="mx-2">|</span> 
                        @endif

                        @if (Auth::user()->admin->can('disable', $plugin))
                            <a link-confirm="{{ trans('messages.disable_plugin_confirm') }}"
                                href="{{ action('Admin\PluginController@disable', ["uids" => $plugin->uid]) }}"
                                class="small link-underline"
                            >
                                {{ trans('messages.disable') }}
                            </a> <span class="mx-2">|</span> 
                        @endif
                        <a class="small link-underline" href="{{ action('Admin\PluginController@index') }}">
                            {{ trans('messages.plugin.back_to_plugins') }}
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
    
    <form id="deletePluginForm" action="{{ action('Admin\PluginController@delete', ["uid" => $plugin->uid]) }}" method="POST">
        @csrf

        <h4 class="font-weight-semibold">{{ trans('messages.plugin.delete_confirmation') }}</h4>
        <p>
            {!! trans('messages.plugin.delete_confirmation.wording') !!}
        </p>

        <a href="javascript:;" class="btn btn-danger delete-plugin" >
            {{ trans('messages.plugin.yes_delete') }}
        </a>
        <a href="{{ action('Admin\PluginController@index') }}" class="btn btn-link">{{ trans('messages.plugin.back_to_plugins') }}</a>
    </form>

    <script>

        var PluginsDelete = {
            confirm: function() {
                new Dialog('confirm', {
                    message: '{{ trans('messages.delete_plugins_confirm') }}',
                    ok: function() {
                        PluginsDelete.doDelete();
                    }
                });
            },

            deDelete: function() {
                $('#deletePluginForm').submit();
            },
        }

        $(function() {
            $('.delete-plugin').on('click', function(e) {
                e.preventDefault();
                addButtonMask($(this));
                PluginsDelete.deDelete();
            });
        });
        
    </script>
@endsection