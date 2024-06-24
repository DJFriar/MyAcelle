@extends('layouts.popup.small')

@section('content')
    <h4 class="mt-0 mb-4 d-flex align-items-center">
        <i class="material-symbols-rounded me-2">multiline_chart</i>
        <span>{{ trans("messages.used_quota") }}</span>
    </h4>

    <div class="row quota_box">
        <div class="col-md-12 mb-4">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-primary text-semibold">
                    <span class="text-muted">{{ number_with_delimiter($sendingCreditsUsed) }}/{{ ($sendingCreditsLimit == -1) ? '∞' : number_with_delimiter($sendingCreditsLimit) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($sendingCreditsUsedPercentage) }}
                </div>
                <label class="text-semibold">{{ trans('messages.sending_quota') }}</label>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width: {{ $sendingCreditsUsedPercentage*100 }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-4">
            <div class="content-group-sm">
                <div class="pull-right text-primary text-semibold">
                    <span class="text-muted">{{ number_with_delimiter($listsCount) }}/{{ number_with_delimiter($maxLists) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($listsUsed) }}
                </div>
                <label class="text-semibold">{{ trans('messages.list') }}</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $listsUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-4">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-primary text-semibold">
                    <span class="text-muted progress-xxs">{{ number_with_delimiter($campaignsCount) }}/{{ number_with_delimiter($maxCampaigns) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($campaignsUsed) }}
                </div>
                <label class="text-semibold">{{ trans('messages.campaign') }}</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $campaignsUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-4">
            <div class="content-group-sm">
                <div class="pull-right text-primary text-semibold">
                    <span class="text-muted">{{ number_with_delimiter($subscribersCount) }}/{{ ($maxSubscribers == -1) ? '∞' : number_with_delimiter($subscribersCount) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($subscribersUsed) }}
                </div>
                <label class="text-semibold">{{ trans('messages.subscriber') }}</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $subscribersUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-primary text-semibold">
                    <span class="text-muted progress-xxs">{{ number_with_delimiter($automationsCount) }}/{{ ($maxAutomations == -1) ?  '∞' : number_with_delimiter($maxAutomations) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($automationsUsed) }}
                </div>
                <label class="text-semibold">{{ trans('messages.automation') }}</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $automationsUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-primary text-semibold">
                    <span class="text-muted progress-xxs">{{ number_with_delimiter(round($uploadCount,2)) }}/{{ number_with_delimiter($maxUpload) }} (MB)</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($uploadUsed) }}%
                </div>
                <label class="text-semibold">{{ trans('messages.total_upload_size') }}</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $uploadUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        @if (config('app.saas') && Auth::user()->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnSendingServer())
            <div class="col-md-12 mb-4">
                <div class="content-group-sm">
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted">{{ number_with_delimiter(Auth::user()->customer->sendingServersCount()) }}/{{ number_with_delimiter(Auth::user()->customer->maxSendingServers()) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingServersUsage() }}
                    </div>
                    <label class="text-semibold">{{ trans('messages.sending_server') }}</label>
                    <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ Auth::user()->customer->sendingServersUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::user()->customer->can("create", new Acelle\Model\SendingDomain()))
            <div class="col-md-12 mb-4">
                <div class="content-group-sm">
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted">{{ number_with_delimiter($domainsCount) }}/{{ ( $maxDomains == -1 ) ? '∞' : number_with_delimiter($maxDomains) }}</span>
                            &nbsp;&nbsp;&nbsp;{{ number_to_percentage($domainsUsed) }}
                    </div>
                    <label class="text-semibold">{{ trans('messages.sending_domain') }}</label>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $domainsUsed*100 }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (config('app.saas') && Auth::user()->customer->can("create", new Acelle\Model\EmailVerificationServer()))
            <div class="col-md-12 mb-4">
                <div class="content-group-sm">
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted">{{ number_with_delimiter(Auth::user()->customer->emailVerificationServersCount()) }}/{{ number_with_delimiter(Auth::user()->customer->maxEmailVerificationServers()) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayEmailVerificationServersUsage() }}
                    </div>
                    <label class="text-semibold">{{ trans('messages.email_verification_server') }}</label>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ Auth::user()->customer->emailVerificationServersUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection
