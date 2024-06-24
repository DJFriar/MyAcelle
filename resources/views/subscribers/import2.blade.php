@extends('layouts.core.frontend', [
	'menu' => 'subscriber',
])

@section('title', $list->name . ": " . trans('messages.import'))

@section('page_header')

    @include("lists._header")

@endsection

@section('content')

	@include("lists._menu", [
		'menu' => 'subscriber_import',
	])

    @if ($currentJob)
        @php
            $progress = $list->getProgress($currentJob);
        @endphp

        <div class="row">
            <div class="col-md-6">
                @if ($progress['status'] == 'failed')
                    <h2 class="my-4">
                        <div class="d-flex align-items-center">
                            <span class="material-symbols-rounded me-3 text-danger">
                                error
                            </span>
                            <span>{{ trans('messages.import.is_failed') }}</span>
                        </div>
                    </h2>
                    <p class="">{!! trans('messages.import.something_went_wrong') !!}</p>
                @else
                    <h2 class="my-4">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border text-success me-3 fw-normal fs-6" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>{{ trans('messages.import.in_progress') }}</span>
                        </div>
                    </h2>
                    <p class="">{!! trans('messages.import.in_progress.wording') !!}</p>
                @endif

                <a id="ViewImportProgressButton" href="javascript:;" class="btn btn-mc_primary start-import">
                    {{ trans('messages.import.in_progress.view') }}
                </a>
            </div>
        </div>

        <script>
            $(function() {
                SubscribersImport2.progessUrl = '{{ action('SubscriberController@import2Progress', [
                    'list_uid' => $list->uid,
                    'job_uid' => $currentJob->uid,
                ]) }}';

                $('#ViewImportProgressButton').on('click', function() {
                    SubscribersImport2.openProgressPopup();
                });
            });
        </script>
    @else
        <div class="row">
            <div class="col-md-6">
                <h2 class="my-4">
                    {{ trans('messages.subscribers.import_csv') }}
                </h2>
                <p class="">{!! trans('messages.subscribers.import_csv.intro', [
                    'csv_link' => url('files/csv_import_example.csv')
                ]) !!}</p>

                <a mapping-action="upload" href="javascript:;" class="btn btn-mc_primary">
                    {{ trans('messages.subscriber.import.start') }}
                </a>
            </div>
        </div>
        
        <script>
            $(function() {
                // load upload popup button
                $('[mapping-action="upload"]').on('click', function() {
                    SubscribersImport2.loadUploadPopup();
                });
            });
        </script>
    @endif


    <script>
        var SubscribersImport2 = {
            popup: null,
            uploadUrl: '{{ action('SubscriberController@import2Wizard', $list->uid) }}',
            progessUrl: null,
            
            init: function() {
                this.popup = new Popup();
            },

            getPopup: function() {
                return this.popup;
            },

            loadUploadPopup: function() {
                this.getPopup().load(this.uploadUrl);
            },

            openProgressPopup: function() {
                this.getPopup().load(this.progessUrl);
            }
        }

        $(function() {
            // init
            SubscribersImport2.init();
        });
    </script>
@endsection
