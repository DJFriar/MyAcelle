<div class="row">
    <div class="col-md-6 campaigns-summary">
		<h5 class="mt-10 text-semibold">{!! trans('messages.send_to', [ 'count' => '<span class="text-bold badge badge-info bg-info-800 badge-big">' . $campaign->readCache("SubscriberCount", 0) . '</span>' ]) !!}</h5>
        <div class="mb-10">
            <span class="text-bold text-muted">{{ trans('messages.from') }}:</span>
            <span>{!! $campaign->displayRecipients() !!}</span>
        </div>
    </div>
    <div class="col-md-6">
		<div class="mb-10">
            <span class="text-bold text-muted">{{ trans('messages.subject') }}:</span>
            <span>{{ $campaign->subject }}</span>
        </div>
        <div class="mb-10">
            <span class="text-bold text-muted">{{ trans('messages.run_at') }}:</span>
            <span>{{ isset($campaign->run_at) ? $campaign->customer->formatDateTime($campaign->run_at, 'datetime_full') : "" }}</span>
        </div>
		<div class="mb-10">
            <span class="text-bold text-muted">{{ trans('messages.delivery_at') }}:</span>
            <span>{{ isset($campaign->delivery_at) ? $campaign->customer->formatDateTime($campaign->delivery_at, 'datetime_full') : "" }}</span>
        </div>
    </div>
</div>

@include("public.campaigns._chart")
<br />
@include("public.campaigns._open_click_rate")

<br />

@include("public.campaigns._24h_chart")

<br />

@include("public.campaigns._most_open_country")
