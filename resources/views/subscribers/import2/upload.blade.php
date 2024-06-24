@extends('layouts.popup.large')

@section('bar-title')
    {{ trans('messages.subscriber_import') }}
@endsection

@section('content')
    
    <!-- Dropzone -->
    <script type="text/javascript" src="{{ AppUrl::asset('core/dropzone/dropzone.js') }}"></script>
    <link href="{{ AppUrl::asset('core/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css">

    @include('helpers._dropzone_lang')

    <div class="popup-wizard">

        @include('subscribers.import2._sidebar', ['step' => 'upload'])
        
        <div class="wizard-content">
            <p>{!! trans('messages.subscriber.import.upload.wording', [
                'link' => url('files/csv_import_example.csv')
            ]) !!}</p>   

            <link rel="stylesheet" type="text/css" href="{{ AppUrl::asset('core/dropzone/dropzone.css') }}" />
            @php
                $formId = uniqid();
            @endphp
            <form action="/file-upload" id="form_{{ $formId }}" class="dropzone mb-4">
                {{ csrf_field() }}
                <div class="fallback">
                    <input name="file" type="file" multiple />
                </div>
            </form>

            <a href="javascript:;" mapping-action="mapping"
                class="btn btn-mc_primary bg-teal-800 mt-4" style="display:none"
            >
                {{ trans('messages.subscriber.import.next_mapping') }}
            </a>
        </div>
    </div>

    <script>
        var SubscribersImport2Upload = {
            mappingUrl: null,
            dropZone: null,

            loadMappingPopup: function() {
                // SubscribersImport2.getPopup().load(this.mappingUrl);
                $.ajax({
                    url: this.mappingUrl,
                    method: 'GET'
                }).done(function(response) {
                    SubscribersImport2.getPopup().loadHtml(response);
                }).fail(function(res){
                    notify('error', '{{ trans('messages.notify.error') }}', res.responseJSON.message); 
                    SubscribersImport2.loadUploadPopup();
                }).always(function() {
                });
            }
        };

        $(function() {
            SubscribersImport2Upload.dropZone = new Dropzone("#form_{{ $formId }}", {
                url: "{{ action('SubscriberController@import2Upload', $list->uid) }}",
                maxFiles: 1,
                success: function(file, res) {
                    $('[mapping-action="mapping"]').show();

                    // disable dropzone
                    SubscribersImport2Upload.dropZone.disable();

                    // update mapping url after uploaded
                    SubscribersImport2Upload.mappingUrl = res.mappingUrl;

                    // notify
                    notify(res.status, '{{ trans('messages.notify.success') }}', res.message); 
                },
                error: function(file, res) {
                    // remove error files
                    SubscribersImport2Upload.dropZone.removeAllFiles();

                    // notify
                    notify(res.status, '{{ trans('messages.notify.error') }}', res.message); 
                },
            });

            $('[mapping-action="mapping"]').click(function(e){
                e.preventDefault();

                // load mapping popup
                SubscribersImport2Upload.loadMappingPopup();
            });
        });

        
    </script>
@endsection