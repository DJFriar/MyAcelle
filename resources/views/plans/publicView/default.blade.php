<div>
    <table class="table table-bordered box-shadow-sm rounded bg-white price-list-col-{{ count($plans) }}">
        <tr style="border-bottom:none!important;">
            <th width="{{ round(50/(count($plans)+1)) }}%" class="bg-light text-center" style="vertical-align:middle;align-items:center">
                {{ trans('messages.plan_name') }}
            </th>

            @foreach($plans as $plan)
                <td width="{{ round(100/(count($plans)+1)) }}%">
                    <h3 class="mb-1 font-weight-semibold">{{ $plan->name }}</h3>
                    <p class="mb-2">{{ $plan->description }}</p>
                </td>
            @endforeach
        </tr>
        <tr style="border-bottom:none!important;">
            <th width="{{ round(50/(count($plans)+1)) }}%" class="bg-light text-center" style="vertical-align:middle;align-items:center">
                {{ trans('messages.price') }}
            </th>

            @foreach($plans as $plan)
                <td width="{{ round(100/(count($plans)+1)) }}%">
                    <p class="mb-1 mt-2 font-weight-semibold fs-6 text-center">
                        <span class="fs-3">{{ \Acelle\Library\Tool::format_price($plan->price, $plan->currency->format) }}</span>
                        / {!! $plan->displayFrequencyTime() !!}
                    </p>
                    <p class="mb-0 py-3">
                        <a 
                            {!! Auth::check() ? 'data-action="login-warning"' : '' !!}
                            href="{{ action('UserController@register', [
                                'plan_uid' => $plan->uid,
                            ]) }}"
                            class="btn fw-600 btn-primary rounded-3 d-block py-2 shadow-sm">
                                @if ($plan->isFree() || $plan->hasTrial())
                                    {{ trans('messages.plan.select') }}
                                @else
                                    {{ trans('messages.plan.buy') }}
                                @endif
                        </a>
                        @if ($plan->hasTrial())
                            <p
                                href="{{ action('UserController@register', [
                                    'plan_uid' => $plan->uid,
                                ]) }}"
                                class="mt-3 fw-600 mb-0 text-center">
                                    {{ trans('messages.plan.has_trial', [
                                        'time' => $plan->getTrialPeriodTimePhrase(),
                                    ]) }}
                            </p>
                        @endif
                    </p>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.sending_quota_label') }}
            </th>
            @foreach($plans as $plan)
                <td>
                    <span>{!! $plan->displayTotalQuota() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.max_lists_label') }}
            </th>
            @foreach($plans as $plan)
                <td>
                    <span>{!! $plan->displayMaxList() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.max_subscribers_label') }}
            </th>
            @foreach($plans as $plan)
                <td>
                    <span>{!! $plan->displayMaxSubscriber() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.max_campaigns_label') }}
            </th>
            @foreach($plans as $plan)
                <td>
                    <span>{!! $plan->displayMaxCampaign() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.max_size_upload_total_label') }} (MB)
            </th>
            @foreach($plans as $plan)
                <td>
                    <span>{!! $plan->displayMaxSizeUploadTotal() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.max_file_size_upload_label') }} (MB)
            </th>
            @foreach($plans as $plan)
                <td>
                    <span>{!! $plan->displayFileSizeUpload() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.allow_create_sending_servers_label') }}
            </th>
            @foreach($plans as $plan)
                <td class="{{ ($plan->getOption('sending_server_option') == \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_OWN ? '' : 'bg-light') }}">
                    <span>{!! $plan->displayAllowCreateSendingServer() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.allow_create_sending_domains_label') }}
            </th>
            @foreach($plans as $plan)
                <td class="{{ $plan->getOption('create_sending_domains') == 'no' ? 'bg-light' : '' }}">
                    <span>{!! $plan->displayAllowCreateSendingDomain() !!}</span>
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="fw-normal bg-light text-center">
                {{ trans('messages.allow_create_email_verification_servers_label') }}
            </th>
            @foreach($plans as $plan)
                <td class="{{ $plan->getOption('create_email_verification_servers') == 'no' ? 'bg-light' : '' }}">
                    <span>{!! $plan->displayAllowCreateEmailVerificationServer() !!}</span>
                </td>
            @endforeach
        </tr>
    </table>
</div>  

@if (Auth::check())
    <div class="modal" id="loginWatningModal" tabindex="-1">
        <div class="modal-dialog shadow modal-default">
            <div class="modal-content">
                <div class="modal-body @yield('class')">
                    <div class="">
                        <span>
                            {{ trans('messages.select_plan.logged_in.logout_current_account', [
                                'username' => Auth::user()->email,
                            ]) }}
                        </span>
                        <div class="mt-2">
                            <button data-action="logout"  class="btn btn-secondary me-1">
                                <i class="icon-switch2"></i> {{ trans('messages.logout') }}
                            </button>

                            <button type="button"  class="btn btn-default" onclick="$('#loginWatningModal').modal('hide')">
                                <i class="icon-switch2"></i> {{ trans('messages.close') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script>
        $(function() {
            $('[data-action="logout"]').on('click', function(e) {
                e.preventDefault();

                addMaskLoading();

                $.ajax({
                    url: '{{ url("/logout") }}',
                    method: 'GET',
                    globalError: false
                }).always(function() {
                    window.location.reload();
                });
            });
        });
    </script>

    <script>
        $(function() {
            $('[data-action="login-warning"]').on('click', function(e) {
                e.preventDefault();

                $('#loginWatningModal').modal('show');
            });
        });
    </script>
@endif

