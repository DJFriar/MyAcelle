@include('automation2._back')

<h4 class="mb-20 mt-3"h5>
    {{ trans('messages.automation.action.send-an-email') }}
</h4>
<p class="mb-10">
    {{ trans('messages.automation.action.send-an-email.intro') }}
</p>

@if ($email)
    <form action="{{ action('Automation2Controller@emailSetup', $automation->uid) }}" method="POST" class="form-validate-jqueryz">
        {{ csrf_field() }}
        
        @include('automation2.email._summary')
        
        <div class="trigger-action mt-4">    
            <span class="btn btn-secondary email-settings-change mr-1"
            >
                {{ trans('messages.automation.email.settings') }}
            </span>
            @if ($email->hasTemplate())
                <a onclick="popupwindow('{{ action('Automation2Controller@templatePreview', [
                                    'uid' => $automation->uid,
                                    'email_uid' => $email->uid,
                                ]) }}', `{{ $automation->name }}`, 800)"
                                href="javascript:;"
                                class="btn btn-default"
                            >
                                {{ trans('messages.automation.template.preview') }}
                            </a>
            @endif
        </div>
        
    <form>

    <div class="mt-4 d-flex py-3">
        <div>
            <h4 class="mb-2">
                {{ trans('messages.automation.dangerous_zone') }}
            </h4>
            <p class="">
                {{ trans('messages.automation.action.delete.wording') }}         
            </p>
            <div class="mt-3">
                <a href="{{ action('Automation2Controller@emailDelete', [
                    'uid' => $automation->uid,
                    'email_uid' => $email->uid,
                ]) }}" data-confirm="{{ trans('messages.automation.action.delete.confirm') }}" class="btn btn-secondary email-action-delete">
                    <span class="material-symbols-rounded">delete</span> {{ trans('messages.automation.remove_this_action') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        // Click on exist action
        $('.email-settings-change').click(function(e) {
            e.preventDefault();
            
            var url = '{{ action('Automation2Controller@emailTemplate', ['uid' => $automation->uid, 'email_uid' => $email->uid]) }}';
            
            popup.load(url);
        });
        
        $('.email-action-delete').click(function(e) {
            e.preventDefault();
            
            var confirm = $(this).attr('data-confirm');
            var url = $(this).attr('href');

            var dialog = new Dialog('confirm', {
                message: confirm,
                ok: function(dialog) {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: CSRF_TOKEN
                        },
                        globalError: false,
                        statusCode: {
                            // validate error
                            400: function (res) {
                                response = JSON.parse(res.responseText);
                                // notify
                                notify('notice', '{{ trans('messages.notify.warning') }}', response.message);
                            }
                        },
                        success: function (response) {
                            // remove current node
                            tree.getSelected().detach();
                            
                            // save tree
                            saveData(function() {                            
                                // notify
                                notify({
        type: 'success',
        title: '{!! trans('messages.notify.success') !!}',
        message: response.message
    });
                                
                                // load default sidebar
                                sidebar.load('{{ action('Automation2Controller@settings', $automation->uid) }}');
                            });
                        }
                    });                        
                },
            });
        });
    </script>
@else
    <div class="mt-4">
        <div class="alert alert-warning">
            <p>{{ trans('messages.automation.email.is_not_setup.click_below') }}</p>
        </div>
        <div class="mt-3">
            <a href="javascript:;" class="btn btn-default email-action-edit">
                {{ trans('messages.setup') }}
            </a>
        </div>
    </div>
    <div class="mt-4 d-flex py-3">
        <div>
            <h4 class="mb-2">
                {{ trans('messages.automation.dangerous_zone') }}
            </h4>
            <p class="">
                {{ trans('messages.automation.action.delete.wording') }}         
            </p>
            <div class="mt-3">
                <a href="javascript:;" data-confirm="{{ trans('messages.automation.action.delete.confirm') }}" class="btn btn-secondary email-action-delete">
                    <span class="material-symbols-rounded">delete</span> {{ trans('messages.automation.remove_this_action') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('.email-action-edit').on('click', function(e) {
                e.preventDefault();

                var url = '{{ action('Automation2Controller@emailSetup', $automation->uid) }}' + '?action_id=' + tree.getSelected().id;
                popup.load(url);
            });

            $('.email-action-delete').on('click', function(e) {
                e.preventDefault();
                
                var confirm = $(this).attr('data-confirm');
                var url = $(this).attr('href');

                var dialog = new Dialog('confirm', {
                    message: confirm,
                    ok: function(dialog) {
                        // remove current node
                        tree.getSelected().detach();
                                
                        // save tree
                        saveData(function() {
                            // notify
                            notify({
                                type: 'success',
                                title: '{!! trans('messages.notify.success') !!}',
                                message: '{!! trans('messages.automation.email.deteled') !!}',
                            });
                            
                            // load default sidebar
                            sidebar.load('{{ action('Automation2Controller@settings', $automation->uid) }}');  
                        });
                    },
                });
            });
        });
        
    </script>

@endif