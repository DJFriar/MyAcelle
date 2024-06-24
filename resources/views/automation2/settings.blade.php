@include('automation2._info')
				
@include('automation2._tabs', ['tab' => 'settings'])
    
<p class="mt-3">
    {!! trans('messages.automation.settings.intro') !!}
</p>
    
<form id="automationUpdate" action="{{ action("Automation2Controller@update", $automation->uid) }}" method="POST" class="form-validate-jqueryz">
    {{ csrf_field() }}
    
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="mb-3 {{ $errors->has('name') ? 'has-error' : '' }}">
                <label class="form-label">{{ trans('messages.automation.automation_name') }}</label>
                <input type="text" name="name" value="{{ $automation->name }}" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" />
                @if ($errors->has('name'))
                    <span class="help-block">
                        {{ $errors->first('name') }}
                    </span>
                @endif
            </div>

            <div class="mb-3 {{ $errors->has('mail_list_uid') ? 'has-error' : '' }}">
                <label class="form-label">{{ trans('messages.automation.choose_list') }}</label>
                <select class="form-select select" name="mail_list_uid">
                    @foreach(Auth::user()->customer->readCache('MailListSelectOptions', []) as $option)
                        <option {{ $automation->mailList->uid == $option['value'] ? 'selected' : '' }} value="{{ $option['value'] }}">
                            {{ $option['text'] }}
                        </option> 
                    @endforeach
                </select>
                @if ($errors->has('mail_list_uid'))
                    <span class="help-block">
                        {{ $errors->first('mail_list_uid') }}
                    </span>
                @endif
            </div>

            <div class="automation-segment">

            </div>
            
            @if (config('custom.japan'))
                <input type="hidden" name="timezone" value="Asia/Tokyo" />
            @else
                <div class="mb-3 {{ $errors->has('timezone') ? 'has-error' : '' }}">
                    <label class="form-label">{{ trans('messages.automation.choose_list') }}</label>
                    <select disabled class="form-select select disabled" name="timezone">
                        <option>{{ trans('messages.choose') }}</option>
                        @foreach(Tool::getTimezoneSelectOptions() as $option)
                            <option {{ (\Auth::user()->customer->timezone ?? config('app.timezone')) == $option['value'] ? 'selected' : '' }} value="{{ $option['value'] }}">
                                {{ $option['text'] }}
                            </option> 
                        @endforeach
                    </select>
                    @if ($errors->has('timezone'))
                        <span class="help-block">
                            {{ $errors->first('timezone') }}
                        </span>
                    @endif
                </div>
            @endif
                
        </div>
    </div>
    
    <button class="btn btn-secondary mt-20">{{ trans('messages.automation.settings.save') }}</button>            
</form>

<div class="mt-4 d-flex py-3">
    <div>
        <h4 class="mb-2">
            {{ trans('messages.automation.dangerous_zone') }}
        </h4>
        <p class="">
            {{ trans('messages.automation.delete.wording') }}        
        </p>
        <div class="mt-3">
            <a href="{{ action('Automation2Controller@delete', ['uids' => $automation->uid]) }}"
                data-confirm="{{ trans('messages.automation.delete.confirm') }}"
                class="btn btn-secondary automation-delete"
            >
                <span class="material-symbols-rounded">delete</span> {{ trans('messages.automation.delete_automation') }}
            </a>
        </div>
    </div>
</div>

<script>
    $(function() {
        // List segment select box
        var listSegmentSelectBox = new ListSegmentSelectBox();
    });

    var ListSegmentSelectBox = class {
        constructor() {
            this.url = '{{ action('Automation2Controller@segmentSelect', [
                'uid' => $automation->uid,
            ]) }}';
            this.listUid = '{{ $automation->mailList->uid }}';
            this.box = new Box($('.automation-segment'));

            // first load
            this.load();

            // events
            this.events();
        }

        getListSelecBox() {
            return document.querySelector('[name=mail_list_uid]');
        }

        events() {
            var _this = this;

            $(this.getListSelecBox()).on('change', (e) => {
                _this.listUid = _this.getListSelecBox().value;

                // load
                _this.load();
            });
        }

        load() {
            var _this = this;

            $.ajax({
                url : this.url,
                type: "GET",
                data: {
                    list_uid: this.listUid,
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
            }).done(function(res) {
                _this.box.loadHtml(res);
            });
        }
    }
</script>
    
<script>
    // set automation name
    setAutomationName('{{ $automation->name }}');

    $('#automationUpdate').submit(function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        
        // loading effect
        sidebar.loading();
        
        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            globalError: false,
            statusCode: {
                // validate error
                400: function (res) {
                   sidebar.loadHtml(res.responseText);
                }
             },
             success: function (response) {
                sidebar.load();
                
                notify(response.status, '{{ trans('messages.notify.success') }}', response.message);

                // need to reload to update tree data
                location.reload();
             }
        });
    });

    var $sel = $('[name=mail_list_uid]').on('change', function() {
        if ($sel.data('confirm') == 'false') {
            confirm = `{{ trans('messages.automation.change_list.confirm') }}`;

            var dialog = new Dialog('confirm', {
                message: confirm,
                ok: function(dialog) {
                    // store new value        
                    $sel.trigger('update');     
                },
                cancel: function(dialog) {
                    // reset
                    $sel.trigger('restore');
                },
                close: function(dialog) {
                    // reset
                    $sel.trigger('restore');
                },
            });
        }
    }).on('restore', function() {
        $(this).data('confirm', 'true');
        $(this).val($(this).data('currVal')).change();
        $(this).data('confirm', 'false');
    }).on('update', function() {
        $(this).data('currVal', $(this).val());
        $(this).data('confirm', 'false');
    }).trigger('update');

    $('.automation-delete').click(function(e) {
        e.preventDefault();
        
        var confirm = $(this).attr('data-confirm');
        var url = $(this).attr('href');

        var dialog = new Dialog('confirm', {
            message: confirm,
            ok: function(dialog) {
                //
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: CSRF_TOKEN,
                    },
                    statusCode: {
                        // validate error
                        400: function (res) {
                            console.log('Something went wrong!');
                        }
                    },
                    success: function (response) {
                        addMaskLoading(
                            '{{ trans('messages.automation.redirect_to_index') }}',
                            function() {
                                window.location = '{{ action('Automation2Controller@index') }}';
                            },
                            { wait: 2000 }
                        );

                        // notify
                        notify({
                            type: 'success',
                            title: '{!! trans('messages.notify.success') !!}',
                            message: response.message
                        });
                    }
                });
            },
        });
    });
</script>
