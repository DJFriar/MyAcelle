@extends('layouts.core.register')

@section('title', trans('messages.create_your_account'))

@section('content')
    
    <form enctype="multipart/form-data" action="{{ action('UserController@register') }}" method="POST" class="form-validate-jqueryz subscription-form">
        {{ csrf_field() }}

        <input type="hidden" name="plan_uid" value="{{ request()->plan_uid }}" />

        <div class="row mc-form">
            <div class="col-md-1"></div>
            <div class="col-md-3 text-center pb-4">
                <a class="main-logo-big" href="{{ action('HomeController@index') }}">
                    <img width="80%" src="{{ getSiteLogoUrl('dark') }}" alt="">
                </a>
            </div>
            <div class="col-md-6">
                
                <h1 class="mb-20">{{ trans('messages.create_your_account') }}</h1>
                <p>{!! trans('messages.register.intro', [
                    'login' => url("/login"),
                ]) !!}</p>
                    
                @include('helpers.form_control', [
                    'type' => 'text',
                    'name' => 'email',
                    'value' => $user->email,
                    'help_class' => 'profile',
                    'rules' => $user->registerRules()
                ])

                @if (get_localization_config('show_last_name_first', config('app.locale')))

                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'last_name',
                        'value' => $user->last_name,
                        'rules' => $user->registerRules()
                    ])
                    
                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'first_name',
                        'value' => $user->first_name,
                        'rules' => $user->registerRules()
                    ])
                    
                    
                @else 
                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'first_name',
                        'value' => $user->first_name,
                        'rules' => $user->registerRules()
                    ])
                    
                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'last_name',
                        'value' => $user->last_name,
                        'rules' => $user->registerRules()
                    ])
                @endif
                
                    
                
                @include('helpers.form_control', [
                    'type' => 'password',
                    'label'=> trans('messages.new_password'),
                    'name' => 'password',
                    'rules' => $user->registerRules(),
                    'eye' => true,
                ])
                
                @if (config('custom.japan'))
                    <input type="hidden" name="timezone" value="Asia/Tokyo" />
                @else
                    @include('helpers.form_control', [
                        'type' => 'select',
                        'name' => 'timezone',
                        'value' => $customer->timezone ?? config('app.timezone'),
                        'options' => Tool::getTimezoneSelectOptions(),
                        'include_blank' => trans('messages.choose'),
                        'rules' => $user->registerRules()
                    ])	
                @endif				
                
                @include('helpers.form_control', [
                    'type' => 'select',
                    'name' => 'language_id',
                    'label' => trans('messages.language'),
                    'value' => $customer->language_id ?? \Acelle\Model\Language::getDefaultLanguage()->id,
                    'options' => Acelle\Model\Language::getSelectOptions(),
                    'include_blank' => trans('messages.choose'),
                    'rules' => $user->registerRules()
                ])
                
                @if (Acelle\Model\Setting::get('registration_recaptcha') == 'yes')
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <!-- hCaptcha -->
                            @if (\Acelle\Model\Setting::getCaptchaProvider() == 'hcaptcha')
                                @php
                                    $hcaptcha = \Acelle\Hcaptcha\Client::initialize();
                                @endphp
                                {!! $hcaptcha->renderFormHtml($errors) !!}
                            @else
                                {!! Acelle\Library\Tool::showReCaptcha($errors) !!}
                            @endif
                        </div>
                    </div>
                @endif
                <hr>
                <div class="row flex align-items">
                    <div class="col-md-4">
                        <button type='submit' class="btn btn-secondary res-button"><i class="icon-check"></i> {{ trans('messages.get_started') }}</button>
                    </div>
                    <div class="col-md-8">
                        {!! trans('messages.register.agreement_intro') !!}
                    </div>
                        
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </form>

    <script>
        @if (isSiteDemo())
            $('.res-button').click(function(e) {
                e.preventDefault();

                notify('notice', '{{ trans('messages.notify.notice') }}', '{{ trans('messages.operation_not_allowed_in_demo') }}');
            });
        @endif
    </script>
    
@endsection
