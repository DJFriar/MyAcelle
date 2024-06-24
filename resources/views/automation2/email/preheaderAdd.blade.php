@extends('layouts.popup.small')

@section('title')
    {{ trans('messages.preheader.add') }}
@endsection

@section('content')
    <form id="PreheaderForm" action="{{ action('Automation2Controller@emailPreheaderAdd', [
        'uid' => $automation->uid,
        'email_uid' => $email->uid,
    ]) }}" method="POST">
        {{ csrf_field() }}
        
        <p>{{ trans('messages.preheader.intro') }}</p>

        <div class="form-group control-textarea {{ $errors->has('preheader') ? ' has-error' : '' }}">
            @if (Acelle\Model\Plugin::isInstalled('acelle/chatgpt') && Acelle\Model\Plugin::getByName('acelle/chatgpt')->isActive())
                @include('chat._preheader', [
                    'name' => 'preheader',
                    'value' => $email->preheader,
                ])
            @else
                <textarea type="text" name="preheader" rows="5" class="form-control required">{{ $email->preheader }}</textarea>
            @endif
            

        </div>

        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-secondary me-1">{{ trans('messages.preheader.save') }}</button>
            <button type="button" class="btn btn-default close">{{ trans('messages.preheader.close') }}</button>
        </div>
    </form>

    <script>
        $(function() {
            $('#PreheaderForm').on('submit', function(e) {
                e.preventDefault();

                var url = $(this).attr('action');
                var data = $(this).serialize();

                addMaskLoading();

                // 
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    globalError: false,
                    statusCode: {
                        // validate error
                        400: function (res) {
                            emailPreheader.addPopup.loadHtml(res.responseText);

                            // remove masking
                            removeMaskLoading();
                        }
                    },
                    success: function (response) {
                        removeMaskLoading();

                        // notify
                        notify(response.status, '{{ trans('messages.notify.success') }}', response.message);

                        emailPreheader.addPopup.hide();

                        email_Preheader.box.load();
                    }
                });
            });
        });
    </script>
@endsection