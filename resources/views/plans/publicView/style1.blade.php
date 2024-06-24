<div>
    <div class="new-price-box" style="margin-right: -30px">
        <div class="d-flex">

            @foreach ($plans as $key => $plan)
                <div
                    class="new-price-item mb-3 d-inline-block plan-item"
                    style="width: calc(33% - 20px)">
                    <div style="height: 100px">
                        <div class="price">
                            {!! format_price($plan->price, $plan->currency->format, true) !!}
                            <span class="p-currency-code">{{ $plan->currency->code }}</span>
                        </div>
                        <p><span class="material-symbols-rounded text-muted2">restore</span> {{ $plan->displayFrequencyTime() }}</p>
                    </div>
                    <hr class="mb-2" style="width: 40px">
                    <div style="height: 40px">
                        <label class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                    </div>

                    <div style="height: 150px">
                        <p class="mt-4">{{ $plan->description }}</p>
                    </div>

                    <span class="time-box d-block text-center small py-2 fw-600 mb-4">
                        <div class="mb-1">
                            <span>{{ $plan->displayTotalQuota() }} {{ trans('messages.sending_total_quota_label') }}</span>
                        </div>
                        <div>
                            <span>{{ $plan->displayMaxSubscriber() }} {{ trans('messages.contacts') }}</span>
                        </div>
                    </span>

                    <div>
                        <div style="vertical-align:bottom">
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
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-light text-secondary me-auto">{{ trans('messages.sending_quota_label') }}</span>
                            <span>{!! $plan->displayTotalQuota() !!}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-light text-secondary me-auto">{{ trans('messages.max_lists_label') }}</span>
                            <span>{!! $plan->displayMaxList() !!}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-light text-secondary me-auto">{{ trans('messages.max_subscribers_label') }}</span>
                            <span>{!! $plan->displayTotalQuota() !!}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-light text-secondary me-auto">{{ trans('messages.max_campaigns_label') }}</span>
                            <span>{!! $plan->displayMaxCampaign() !!}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-light text-secondary me-auto">{{ trans('messages.max_size_upload_total_label') }}</span>
                            <span>{!! $plan->displayMaxSizeUploadTotal() !!}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-light text-secondary me-auto">{{ trans('messages.max_file_size_upload_label') }}</span>
                            <span>{!! $plan->displayFileSizeUpload() !!}</span>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
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

