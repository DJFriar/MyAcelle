<h2 class="mt-0 mb-3">{{ trans('messages.preheader') }}</h2>

@if ($email->preheader)
    <div class="p-3 border bg-light rounded-3">
        <div class="d-flex align-items-top">
            <div class="pe-4">
                {{ $email->preheader }}
            </div>
            <div class="ms-auto">
                <a id="PreheaderEditButton" href="javascript:;" class="btn btn-default me-1">
                    <span class="material-symbols-rounded">edit</span>
                    {{ trans('messages.preheader.edit') }}
                </a>
                <a id="PreheaderRemoveButton" href="javascript:;" class="btn btn-danger">
                    <span class="material-symbols-rounded">delete</span>
                    {{ trans('messages.preheader.remove') }}
                </a>
            </div>
        </div>
    </div>
@else
    <p>{{ trans('messages.preheader.intro') }}</p>
        
    <div class="mt-4">
        <a id="PreheaderAddPopupButton" href="javascript:;" class="btn btn-default">
            <span class="material-symbols-rounded">add</span>
            {{ trans('messages.preheader.add') }}
        </a>
    </div>
@endif

<script>
    var EmailPreheader = class {

        constructor() {
            this.addPopup = new Popup({
                url: '{{ action('Automation2Controller@emailPreheaderAdd', [
                    'uid' => $automation->uid,
                    'email_uid' => $email->uid,
                ]) }}'
            });
        }

        remove() {
            new Dialog('confirm', {
                message: '{{ trans('messages.preheader.remove.confirm') }}',
                ok: function() {
                    addMaskLoading();
                    $.ajax({
                        method: "POST",
                        url: '{{ action('Automation2Controller@emailPreheaderRemove', [
                            'uid' => $automation->uid,
                            'email_uid' => $email->uid,
                        ]) }}',
                        data: {
                            _token: CSRF_TOKEN
                        }
                    })
                    .done(function( response ) {
                        removeMaskLoading();

                        // notify
                        notify(response.status, '{{ trans('messages.notify.success') }}', response.message);

                        email_Preheader.box.load();
                    });   
                }
            });
                      
        }
    }

    $(function() {
        emailPreheader = new EmailPreheader();

        $('#PreheaderAddPopupButton').on('click', function() {
            emailPreheader.addPopup.load();
        });

        $('#PreheaderEditButton').on('click', function() {
            emailPreheader.addPopup.load();
        });
        
        $('#PreheaderRemoveButton').on('click', function() {
            emailPreheader.remove();
        });
    });
</script>