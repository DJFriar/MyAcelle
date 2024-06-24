@if (Auth::user()->admin->getPermission("setting_general") == 'yes')
    <div class="tab-pane active" id="top-general">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-semibold">{{ trans('messages.admin.settings.edit_app_settings') }}</h2>
                <h3 class="text-semibold">{{ trans('messages.general') }}</h3>
            </div>
        </div>
        <div class="row">
            <?php $count = 0; ?>
            @foreach ($settings as $name => $setting)
                @if (isset($setting['cat']) && $setting['cat'] == 'general')
                    <div class="col-md-6">

                        @if (in_array($name, ['site_logo_light','site_logo_dark','site_favicon']))
                            @include('admin.settings.general.' . $name)
                        @else
                            @if ($setting['type'] == 'checkbox')
                                <div class="form-group">
                                    <div class="d-flex" style="width:100%">
                                        <div class="me-5">
                                            <label class="text-semibold">
                                                {{ trans('messages.' . $name) }}
                                            </label>
                                            <p class="checkbox-description mt-1 mb-0">
                                                {{ trans('messages.setting.' . $name . '.help') }}
                                            </p>
                                        </div>
                                            
                                        <div class="d-flex align-items-top">
                                            @include('helpers.switch', [
                                                'name' => $name,
                                                'option' => $setting['options'][1],
                                                'unchecked_option' => (($setting['options'][0] == false) ? 0 : $setting['options'][0]),
                                                'value' => $setting['value'],
                                            ])
                                        </div>
                                    </div>
                                </div>
                            @else
                                @include('helpers.form_control', [
                                    'type' => $setting['type'],
                                    'class' => (isset($setting['class']) ? $setting['class'] : "" ),
                                    'name' => $name,
                                    'value' => ($setting['type'] == 'image' ? (!empty($setting['value']) ? action('SettingController@file', $setting['value']) : '') : $setting['value']),
                                    'label' => trans('messages.' . $name),
                                    'help_class' => 'setting',
                                    'options' => (isset($setting['options']) ? $setting['options'] : "" ),
                                    'rules' => Acelle\Model\Setting::rules(),
                                ])
                            @endif
                        @endif
                    </div>
                    @if ($count%2 == 1)
        </div><div class="row">
                    @endif
                    <?php ++$count; ?>
                @endif
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-12"><p align="right"><a href="{{ action('Admin\SettingController@advanced') }}">{{ trans('messages.configuration.settings') }}</a></p></div>
        </div>
        <div class="text-left">
            <button class="btn btn-secondary"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
        </div>
        
    </div>
@endif
