@extends('layouts.popup.small')

@section('title')
    <span class="material-symbols-rounded me-1 text-muted2">content_copy</span>
    {!! trans('messages.automation.copy.title', [
        'name' => $automation->name
    ]) !!}
@endsection

@section('content')
    <form id="copyAutomationForm"
        action="{{ action('Automation2Controller@copy', $automation->uid) }}"
        method="POST"
    >
        {{ csrf_field() }}  

        <p class="">{{ trans('messages.automation.copy.wording') }}</p>

        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
            <label>
                {{ trans('messages.automation.name') }}
            </label>
            <div>
                <input value="{{ $newAutomation->name }}"
                    type="text"
                    name="name" class="form-control">
            </div>
            @if ($errors->has('name'))
                <div class="help-block">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        <div class="form-group {{ $errors->has('mail_list_uid') ? 'has-error' : '' }}">
            <label>
                {{ trans('messages.list') }}
            </label>
            <div>
                <select name="mail_list_uid" class="select">
                    @foreach (Auth::user()->customer->readCache('MailListSelectOptions', []) as $option)
                        <option {{ $newAutomation->mailList->uid == $option['value'] ? 'selected' : '' }} value="{{ $option['value'] }}">{{ $option['text'] }}</option>
                    @endforeach
                </select>
            </div>
            @if ($errors->has('mail_list_uid'))
                <div class="help-block">
                    {{ $errors->first('mail_list_uid') }}
                </div>
            @endif
        </div>

        <div class="mt-4 text-center">
            <button id="copyAutomationButton" type="submit" class="btn btn-secondary me-1">{{ trans('messages.save') }}</button>
            <button type="button" class="btn btn-link fw-600" data-bs-dismiss="modal">{{ trans('messages.cancel') }}</button>
        </div>
    </form>


    <script>
        var Automation2Copy = {
            copy: function(url, data) {
                Automation2List.copyPopup.mask();
                addButtonMask($('#copyAutomationButton'));

                // copy
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    globalError: false
                }).done(function(response) {
                    notify({
                        type: 'success',
                        message: response.message,
                    });

                    Automation2List.copyPopup.hide();
                    Automation2Index.getList().load();

                }).fail(function(jqXHR, textStatus, errorThrown){
                    // for debugging
                    Automation2List.copyPopup.loadHtml(jqXHR.responseText);
                }).always(function() {
                    Automation2List.copyPopup.unmask();
                    removeButtonMask($('#copyAutomationButton'));
                });
            }
        }

        $(function() {
            $('#copyAutomationForm').on('submit', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var data = $(this).serialize();

                Automation2Copy.copy(url, data);
            });
        });
    </script>
@endsection