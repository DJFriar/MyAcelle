<div class="mc_section boxing">
    <div class="row">
        <div class="col-md-6">
            <h3 class="mt-0">{{ trans('messages.sending_servers.sending_identity') }}</h3>
            <p>
                {!! trans('messages.sending_servers.sending_identity.intro', ['link' => 'https://us-west-2.console.aws.amazon.com/ses/home#verified-senders-domain']) !!}
            </p>
        </div>
        <div class="col-md-8">
            @if (is_null($identities))
                @include('elements._notification', [
                    'level' => 'warning',
                    'title' => 'Error fetching identities list',
                    'message' => 'Please check your connection to AWS',
                ])
            @else
                <table class="table table-box table-box-head field-list">
                    <thead>
                        <tr>
                            <td>{{ trans('messages.domain') }}</td>
                            <td>{{ trans('messages.status') }}</td>
                            <td align="center" class="xtooltip" title="{{ trans('messages.sending_server.identity.available_for_all.desc') }}">{{ trans('messages.sending_server.identity.available_for_all') }}</td>
                            <td>{{ trans('messages.sending_server.identity.added_by') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allIdentities as $domain => $attributes)
                            <tr class="odd">
                                <td>
                                    {{ $domain }}
                                </td>
                                <td>
                                    @if ($attributes['VerificationStatus'] == 'Success')
                                        <span class="label label-flat bg-active">{{ trans('messages.sending_domain_status_active') }}</span>
                                    @else
                                        <span class="label label-flat bg-inactive">{{ trans('messages.sending_domain_status_inactive') }}</span>
                                    @endif
                                    
                                </td>

                                @if (!is_null($attributes['UserId']))
                                    <td align="center"><span class="xtooltip" title="This domain is private and is available for the owner user only">Private</span></td>
                                @elseif ($attributes['VerificationStatus'] == 'Success')
                                    <td align="center">
                                        @if (checkEmail($domain))
                                            <label class="checker">
                                                <input {{ $attributes['Selected'] ? " checked" : "" }} type="checkbox" name="options[emails][]" value="{{ $domain }}" class="styled4">
                                                <span class="checker-symbol"></span>
                                            </label>
                                        @else
                                            <label class="checker">
                                                <input {{ $attributes['Selected'] ? " checked" : "" }} type="checkbox" name="options[domains][]" value="{{ $domain }}" class="styled4">
                                                <span class="checker-symbol"></span>
                                            </label>
                                        @endif
                                    </td>
                                @else
                                    <td align="center"></td>
                                @endif
                                <td>
                                    <a href="{{ action('Admin\CustomerController@index') }}" target="_blank">{{ $attributes['UserName'] }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="col-md-6">
            <br>
            <a target="_blank" confirm="{{ trans('messages.sending_server.aws.add_domain_redirect_confirm') }}" href="https://us-west-2.console.aws.amazon.com/ses/home?region={{ $server->type }}" class="btn btn-secondary me-2">
                {{ trans('messages.sending_serbers.add_domain') }}
            </a>
            <a target="_blank" href="https://console.aws.amazon.com/ses/home?region={{ $server->aws_region }}" role="button"
              class="btn btn-primary">
                {{ trans('messages.sending_serbers.go_to_amazon_dashboard') }}
            </a>
            <p class="mt-5">
                {{ trans('messages.sending_serbers.aws.allow_verify.intro') }}
            </p>

            <div>
                <span class="form-label d-flex align-items-center">
                    <label class="checkbox-label readonly">
                        <input type="hidden" name="options[allow_verify_domain_remotely]" value="no" />
                        <input                           
                            type="checkbox"
                            class="styled me-2"
                            name="options[allow_verify_domain_remotely]"
                            {{ $server->getOption('allow_verify_domain_remotely') == 'yes' ? 'checked' : '' }}
                            value="yes"
                            readonly
                        >
                        <span class="check-symbol"></span>
                        <span class="ms-1">
                            {{ trans('messages.allow_verify_domain_against_aws') }}
                        </span>
                    </label>
                </span>
            </div>

            @include('helpers.form_control', [
                'type' => 'checkbox2',
                'label' => trans('messages.allow_verify_email_against_aws'),
                'name' => 'options[allow_verify_email_remotely]',
                'value' => $server->getOption('allow_verify_email_remotely'),
                'help_class' => 'sending_server',
                'options' => ['no', 'yes'],
            ])

            <hr>
            <div class="mt-20">
                <button class="btn btn-secondary me-2">{{ trans('messages.save') }}</button>
                <a href="{{ action('Admin\SendingServerController@index') }}" role="button" class="btn btn-link">
                    {{ trans('messages.cancel') }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('[name="options[allow_verify_email_remotely]"]').change(function() {
            var value = $(this).is(':checked');
            if(value) {
                $('.use_custom_verification_email').show();
            } else {
                $('.use_custom_verification_email').hide();
            }
        });
        $('[name="options[allow_verify_email_remotely]"]').change();
    });
</script>
