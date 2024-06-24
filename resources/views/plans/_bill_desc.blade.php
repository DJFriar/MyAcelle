<div class="mt-2">
    @if (isset($endsAt))
        <div>
            <span>{{ trans('messages.ends_on') }}</span>:
            <strong>{{ Auth::user()->customer->formatDateTime($endsAt, 'datetime_full') }}</strong>
        </div>
        <hr class="my-2">
    @endif

    @if ($plan->type == Acelle\Model\PlanGeneral::TYPE_GENERAL)
        @php
            $plan = Acelle\Model\PlanGeneral::find($plan->id);
        @endphp
        <div class="mb-1">
            <span>{{ $plan->displayTotalQuota() }} {{ trans('messages.sending_total_quota_label') }}</span>
        </div>
        <div>
            <span>{{ $plan->displayMaxSubscriber() }} {{ trans('messages.contacts') }}</span>
        </div>
    @endif
</div>