@extends('layouts.core.frontend', [
    'menu' => 'campaign',
])

@section('title', trans('messages.campaigns') . " - " . trans('messages.schedule'))

@section('head')
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/anytime.min.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/pickadate/picker.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/pickadate/picker.date.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ action("CampaignController@index") }}">{{ trans('messages.campaigns') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold"><span class="material-symbols-rounded me-2">forward_to_inbox</span> {{ $campaign->name }}</span>
        </h1>

        @include('campaigns._steps', ['current' => 4])
    </div>

@endsection

@section('content')
    <form id="CampaignScheduleForm" action="{{ action('CampaignController@schedule', $campaign->uid) }}" method="POST" class="form-validate-jqueryz">
        {{ csrf_field() }}

        <input type="hidden" name="send_now" value="no" />

        <div class="form-group control-radio">
            <div class="radio_box" data-popup="tooltip" title="">
                <label class="main-control">
                    <input {{ !$campaign->run_at ? 'checked' : '' }} type="radio" name="plan[options][create_email_verification_servers]" value="no" class="styled"><span class="check-symbol"></span>
                    <rtitle>{{ trans('messages.campaign.send_now.title') }}</rtitle>
                    <div class="desc text-normal mb-10">
                        {{ trans('messages.campaign.send_now.desc') }}
                    </div>
                </label>
                <div class="radio_more_box" style="display: none;">
                    <div class="desc text-normal mb-10 pl-0">
                        <div class="">
                            @if(config('custom.japan') && !Acelle\Model\Setting::get('license'))
                                <button license-required type="button" class="btn btn-secondary">
                                    {{ trans('messages.save_and_next') }}
                                    <span class="material-symbols-rounded">arrow_forward</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary send-now">
                                    {{ trans('messages.save_and_next') }}
                                    <span class="material-symbols-rounded">arrow_forward</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>   
            <hr>
            <div class="radio_box" data-popup="tooltip" title="">
                <label class="main-control">
                    <input {{ $campaign->run_at ? 'checked' : '' }} type="radio" name="plan[options][create_email_verification_servers]" value="yes" class="styled"><span class="check-symbol"></span>
                        <rtitle>{{ trans('messages.campaign.send_later.title') }}</rtitle>
                        <div class="desc text-normal mb-10">
                            {{ trans('messages.campaign.send_later.desc') }}
                        </div>
                </label>
                <div class="radio_more_box">
                    <div class="row">
                        <div class="col-md-3 list_select_box" target-box="segments-select-box" segments-url="{{ action('SegmentController@selectBox') }}">
                            @include('helpers.form_control', ['type' => 'date',
                                'class' => '_from_now',
                                'name' => 'delivery_date',
                                'label' => trans('messages.delivery_date'),
                                'value' => $delivery_date,
                                'rules' => $rules,
                                'help_class' => 'campaign'
                            ])
                        </div>
                        <div class="col-md-3 segments-select-box">
                            @include('helpers.form_control', ['type' => 'time',
                                'name' => 'delivery_time',
                                'label' => trans('messages.delivery_time'),
                                'value' => $delivery_time,
                                'rules' => $rules,
                                'help_class' => 'campaign'
                            ])
                        </div>
                    </div>
                    
                    <div class="">
                        <button @if(config('custom.japan') && !Acelle\Model\Setting::get('license')) license-required @endif class="btn btn-secondary me-1">
                            <span class="material-symbols-rounded me-1">alarm</span>
                            {{ trans('messages.save_and_next') }}
                                <span class="material-symbols-rounded">arrow_forward</span>
                        </button>
                    </div>
                    
                </div>
            </div>              
        </div>
    <form>

    <script>
        var CampaignSchedule = {
            getForm: function() {
                return $('#CampaignScheduleForm');
            },

            schedule: function() {
                this.getForm().find('[name=send_now]').val('no');
                this.getForm().submit();
            },

            scancelNow: function() {
                this.getForm().find('[name=send_now]').val('yes');
                this.getForm().submit();
            }
        }
        
        $(function() {
            $('.send-now').on('click', function() {
                CampaignSchedule.scancelNow();
            });
        });
    </script>
@endsection
