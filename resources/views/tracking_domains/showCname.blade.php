@extends('layouts.core.frontend')

@section('title', $domain->name)

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ action("TrackingDomainController@index") }}">{{ trans('messages.tracking_domains') }}</a></li>
        </ul>
        <h2>
            <span class="text-semibold"><span class="material-icons-outlined">public</span> {{ $domain->getUrl() }} </span>
            <span class="label label-primary bg-{{ $domain->status }}">
                {{ trans('messages.tracking_domain.status.' . $domain->status) }}
            </span>
        </h2>       
    </div>

@endsection

@section('content')
    
    <div class="row sub_section">
        <div class="col-sm-12 col-md-8">
            @if (!$domain->isVerified())
                <form action="{{ action('TrackingDomainController@verifyCname', [ 'uid' => $domain->uid ]) }}" method="GET">
                    <div data-type="admin-notification" class="alert alert-warning" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                        <div style="display: flex; flex-direction: row; align-items: center;">
                            <div style="padding-right: 40px">
                                <h4>{{ trans('messages.tracking_domain.cname.pending_title') }}</h4>
                            <p>{{ trans('messages.tracking_domain.cname.pending_note') }}</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary text-nowrap">{{ trans('messages.tracking_domain.cname.refresh') }}</button>
                    </div>
                </form>
                <h2>{{ trans('messages.tracking_domain.cname.dns_setup.title') }}</h2>
                <p>{{ trans('messages.tracking_domain.cname.dns_setup.note1') }}</p>

                <p>{{ trans('messages.tracking_domain.cname.dns_setup.note2') }}</p>
                <ul class="dotted-list topborder section section-flex">
                    <li style="font-size:16px">
                        <div class="size1of3">
                            <strong>{{ $domain->getFQDN() }}</strong><br><span style="font-size:14px;">{{ trans('messages.tracking_domain.hostname') }}</span>
                        </div>
                        <div class="unit size1of3">
                            <strong>CNAME</strong><br><span style="font-size:14px;">{{ trans('messages.tracking_domain.record_type') }}</span>
                        </div>
                        <div class="size1of3">
                            <div class="d-flex align-items-center">
                                <div>
                                    <strong>{{ $hostname }}</strong><br><span style="font-size:14px;">{{ trans('messages.tracking_domain.value') }}</span>
                                </div>

                                @if (!$domain->isVerified())
                                    <a href="javascript:;" class="btn btn-secondary ml-auto tracking-domain-test">{{ trans('messages.tracking_domain.test') }}</a>
                                    <div class="tracking-domain-test-message" style="display:none">
                                        <div class="modal-dialog shadow modal-default">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <a href="javascript:;" class="material-icons-round back">keyboard_backspace</a>
                                                    <h5 class="modal-title text-center" style="width:100%">
                                                        {{ $domain->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {!! trans('messages.tracking_domain.cname.test.wording', [
                                                        'domain' => $domain->name,
                                                    ]) !!}

                                                    <div class="mt-4 pt-3 text-center">
                                                        <a href="http://{{ $domain->name }}" class="btn btn-secondary mr-2" target="_blank">{{ trans('messages.tracking_domain.test.proceed') }}</a>
                                                        <button class="btn btn-link tracking-domain-test-close" target="_blank">{{ trans('messages.close') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $('.tracking-domain-test').on('click', function(e) {
                                            e.preventDefault();

                                            var html = $('.tracking-domain-test-message').html();
                                            var testPopup = new Popup();
                                            testPopup.loadHtml(html);
                                            testPopup.show();

                                            $('.tracking-domain-test-close').on('click', function(e) {
                                                e.preventDefault();

                                                testPopup.hide();
                                            });
                                        });
                                        
                                    </script>
                                @endif
                                    
                            </div>
                        </div>
                    </li>
                </ul>
                <p style="margin-top: 20px"><i>{{ trans('messages.tracking_domain.cname.dns_setup.note3') }}</i></p>
                <a role="button" style="padding-left:0" class="btn btn-link" href="{{ action('TrackingDomainController@index') }} ">{{ trans('messages.go_back') }}</a>
            @else
                <div data-type="admin-notification" class="alert alert-success" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                    <div style="display: flex; flex-direction: row; align-items: center;">
                        <div style="padding-right: 40px">
                            <h4>{{ trans('messages.tracking_domain.show.verified_title') }}</h4>
                            <p>{{ trans('messages.tracking_domain.show.verified_note') }}</p>
                        </div>
                    </div>
                </div>

                <a role="button" style="padding-left:0" class="btn btn-link" href="{{ action('TrackingDomainController@index') }} ">{{ trans('messages.go_back') }}</a>
            @endif
        </div>
    </div>

@endsection
