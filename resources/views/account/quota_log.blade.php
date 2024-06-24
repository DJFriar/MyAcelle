@extends('layouts.popup.small')

@section('title')
    <i class="material-symbols-rounded alert-icon mr-2">addchart</i>
    {{ trans("messages.used_quota") }}
@endsection

@section('content')
    <div class="row quota_box mb-4">
        <div class="col-md-12">
            <div class="content-group-sm">
                <div class="d-flex align-items-center">
                    <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.sending_quota') }}</h5>
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted">{{ number_with_delimiter($sendingCreditsUsed) }}/{{ ($sendingCreditsLimit == -1) ? '∞' : number_with_delimiter($sendingCreditsLimit) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($sendingCreditsUsedPercentage) }}
                    </div>
                </div>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ $sendingCreditsUsedPercentage*100  }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-group-sm mt-4">
                <div class="d-flex align-items-center">
                    <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.list') }}</h5>
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted">{{ number_with_delimiter($listsCount) }}/{{ number_with_delimiter($maxLists) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($listsUsed) }}
                    </div>
                </div>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ $listsUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-group-sm mt-4">
                <div class="d-flex align-items-center">
                    <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.campaign') }}</h5>
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted progress-xxs">{{ number_with_delimiter($campaignsCount) }}/{{ number_with_delimiter($maxCampaigns) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($campaignsUsed) }}
                    </div>
                </div>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ $campaignsUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-group-sm mt-4">
                <div class="d-flex align-items-center">
                    <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.subscriber') }}</h5>
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted">{{ number_with_delimiter($subscribersCount) }}/{{ ($maxSubscribers == -1) ? '∞' : number_with_delimiter($maxSubscribers) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($subscribersUsed) }}
                    </div>
                </div>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ $subscribersUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="content-group-sm mt-4">
                <div class="d-flex align-items-center">
                    <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.automation') }}</h5>
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted progress-xxs">{{ number_with_delimiter($automationsCount) }}/{{ ($maxAutomations == -1) ?  '∞' : number_with_delimiter($maxAutomations) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($automationsUsed) }}
                    </div>
                </div>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ $automationsUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="content-group-sm mt-4">
                <div class="d-flex align-items-center">
                    <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.total_upload_size') }}</h5>
                    <div class="pull-right text-primary text-semibold">
                        <span class="text-muted progress-xxs">{{ number_with_delimiter(round($uploadCount,2)) }}/{{ number_with_delimiter($maxUpload) }} (MB)</span>
                        &nbsp;&nbsp;&nbsp;{{ number_to_percentage($uploadUsed) }}
                    </div>
                </div>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ $uploadUsed*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        @if (config('app.saas') && Auth::user()->customer->getCurrentActiveGeneralSubscription()->planGeneral->useOwnSendingServer())
            <div class="col-md-12">
                <div class="content-group-sm mt-4">
                    <div class="d-flex align-items-center">
                        <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.sending_server') }}</h5>
                        <div class="pull-right text-primary text-semibold">
                            <span class="text-muted">{{ number_with_delimiter(Auth::user()->customer->sendingServersCount()) }}/{{ number_with_delimiter(Auth::user()->customer->maxSendingServers()) }}</span>
                            &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingServersUsage() }}
                        </div>
                    </div>
                    <div class="progress progress-xxs">
                        <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->sendingServersUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::user()->customer->can("create", new Acelle\Model\SendingDomain()))
            <div class="col-md-12">
                <div class="content-group-sm mt-4">
                    <div class="d-flex align-items-center">
                        <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.sending_domain') }}</h5>
                        <div class="pull-right text-primary text-semibold">
                            <span class="text-muted">{{ number_with_delimiter($domainsCount) }}/{{ ( $maxDomains == -1 ) ? '∞' : number_with_delimiter($maxDomains) }}</span>
                            &nbsp;&nbsp;&nbsp;{{ number_to_percentage($domainsUsed) }}
                        </div>
                    </div>
                    <div class="progress progress-xxs">
                        <div class="progress-bar bg-warning" style="width: {{ $domainsUsed*100 }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (config('app.saas') && Auth::user()->customer->can("create", new Acelle\Model\EmailVerificationServer()))
            <div class="col-md-12">
                <div class="content-group-sm mt-4">
                    <div class="d-flex align-items-center">
                        <h5 class="text-semibold mb-1 me-auto">{{ trans('messages.email_verification_server') }}</h5>
                        <div class="pull-right text-primary text-semibold">
                            <span class="text-muted">{{ number_with_delimiter(Auth::user()->customer->emailVerificationServersCount()) }}/{{ number_with_delimiter(Auth::user()->customer->maxEmailVerificationServers()) }}</span>
                            &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayEmailVerificationServersUsage() }}
                        </div>
                    </div>
                    <div class="progress progress-xxs">
                        <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->emailVerificationServersUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection