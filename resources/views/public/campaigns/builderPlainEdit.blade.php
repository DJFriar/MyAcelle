@extends('layouts.core.frontend_dark')

@section('title', trans('messages.edit_template'))

@section('head')
	<script type="text/javascript" src="{{ AppUrl::asset('core/tinymce/tinymce.min.js') }}"></script>        
    <script type="text/javascript" src="{{ AppUrl::asset('core/js/editor.js') }}"></script>

    <script src="{{ AppUrl::asset('core/js/UrlAutoFill.js') }}"></script>
@endsection

@section('menu_title')
    <li class="d-flex align-items-center">
        <div class="d-inline-block d-flex mr-auto align-items-center ml-1 lvl-1">
            <h4 class="my-0 me-2 menu-title text-white">{{ $campaign->name }}</h4>
            <i class="material-symbols-rounded">alarm</i>
        </div>
    </li>
@endsection

@section('menu_right')
    <li class="nav-item d-flex align-items-center">
        <a  href="javascript:;"
            onclick="parent.$('body').removeClass('overflow-hidden');parent.$('.full-iframe-popup').fadeOut()"
            class="nav-link py-3 lvl-1 d-flex align-items-center">
            <i class="material-symbols-rounded me-2">arrow_back</i>
            <span>{{ trans('messages.go_back') }}</span>
        </a>
    </li>
    <li class="nav-item d-flex align-items-center">
        <a href="{{ action('Pub\CampaignController@builderClassic', [
            'uid' => $campaign->uid
        ]) }}"
            class="nav-link py-3 lvl-1">
            <span>{{ trans('messages.campaign.html_editor') }}</span>
        </a>
    </li>
    @if ($campaign->plain != null)
        <li class="d-flex align-items-center px-3">
            <button class="btn btn-primary" onclick="$('#classic-builder-form').submit()">{{ trans('messages.save') }}</button>
        </li>
    @endif
    <li>
        <a href="javascript:;"
            onclick="parent.$('body').removeClass('overflow-hidden');parent.$('.full-iframe-popup').fadeOut()"
            class="nav-link close-button action black-close-button">
            <i class="material-symbols-rounded">close</i>
        </a>
    </li>
@endsection

@section('content')
    <form style="" id="classic-builder-form" action="{{ action('Pub\CampaignController@builderPlainEdit', $campaign->uid) }}" method="POST" class="form-validate-jqueryz">
        {{ csrf_field() }}

        <div class="row mr-0 ml-0 form-groups-bottom-0">
            <div class="col-md-9 pl-0 pb-0 pr-0 form-group-mb-0">
                @include('helpers.form_control', [
                    'type' => 'textarea',
                    'class' => 'campaign-plain-text',
                    'name' => 'plain',
                    'label' => '',
                    'value' => $campaign->plain,
                    'rules' => [],
                    'help_class' => 'campaign',
                    'disabled' => $campaign->plain == null,
                ])          
            </div>
            <div class="col-md-3 pr-0 pb-0 sidebar pr-4 pt-4 pl-4" style="overflow:auto;background:#f5f5f5">
                <div>
                    @if ($campaign->plain == null)
                        <p>
                            {{ trans('messages.campaign.plain.click_to_custom_html') }}
                        </p>
                        <div class="mb-4">
                            <a href="{{ action('Pub\CampaignController@customPlainOn', [
                                'uid' => $campaign->uid,
                            ]) }}" link-method="POST" class="btn btn-primary">
                                <span class="material-symbols-rounded me-1">dashboard_customize</span>
                                    {{ trans('messages.campaign.plain.use_custom_plain_content') }}
                            </a>
                        </div>
                        <hr>
                    @else
                        <p>
                            {{ trans('messages.campaign.plain.automatically_extract') }}
                        </p>
                        <div class="mb-4">
                            <a href="{{ action('Pub\CampaignController@customPlainOff', [
                                'uid' => $campaign->uid,
                            ]) }}" link-method="POST" class="btn btn-primary">
                                <span class="material-symbols-rounded me-1">post_add</span>
                                    {{ trans('messages.campaign.plain.extract_from_html') }}
                            </a>
                        </div>
                        <hr>
                    @endif
                </div>
                @include('elements._tags', ['tags' => Acelle\Model\Template::tags($campaign->defaultMailList)])
            </div>            
        </div>   
    <form>

    <script>
        $(function() {
            // Click to insert tag
            $(document).on("click", ".insert_tag_button", function() {
                var tag = $(this).attr("data-tag-name");
                insertAtCursor($('textarea[name="plain"]')[0], tag);
            });
        });
    </script>

    <script>
    $('#classic-builder-form').submit(function(e) {
            e.preventDefault();

            tinymce.triggerSave();

            var url = $(this).attr('action');
            var data = $(this).serialize();

            if ($(this).valid()) {
                // open builder effects
                addMaskLoading("{{ trans('messages.automation.template.saving') }}", function() {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        statusCode: {
                            // validate error
                            400: function (res) {
                                removeMaskLoading();
                                
                                // notify
                                parent.notify('error', '{{ trans('messages.notify.error') }}', res.responseText);
                            }
                        },
                        success: function (response) {
                            removeMaskLoading();

                            if (typeof(parent.builderSelectPopup) != 'undefined') {
                                parent.builderSelectPopup.hide();
                            }

                            // notify
                            parent.notify({
    type: 'success',
    title: '{!! trans('messages.notify.success') !!}',
    message: response.message
});
                        }
                    });
                });         
            }     
        });

        $('.sidebar').css('height', parent.$('.full-iframe-popup').height()-53);
        $('[name=plain]').css('height', parent.$('.full-iframe-popup').height()-53);
    </script>
@endsection
