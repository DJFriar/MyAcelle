<div class="page-title">
	<ul class="breadcrumb breadcrumb-caret position-right">
		<li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		<li class="breadcrumb-item"><a href="{{ action("CampaignController@index") }}">{{ trans('messages.campaigns') }}</a></li>
	</ul>
    <div class="d-flex mt-4">
        <div>
            <h1 class="d-flex align-items-center">
                <span class="text-semibold mr-3">{{ $campaign->name }}</span>
                <span class="d-flex" title='{{ $campaign->status == Acelle\Model\Campaign::STATUS_ERROR ? $campaign->last_error : '' }}' data-popup='tooltip'>
                    <span class="label label-flat bg-{{ $campaign->status }}">{{ trans('messages.campaign_status_' . $campaign->status) }}</span>
                </span>
            </h1>
        </div>
        <div class="ms-auto">
            <a target="_blank" href="{{ action('Pub\CampaignController@overview', $campaign->uid) }}" class="btn btn-default">
                <span class="material-symbols-rounded me-1">public</span>
                {{ trans('messages.campaign.public_report') }}
            </a>
        </div>
    </div>
        
</div>
