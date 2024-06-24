<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ trans('messages.form.builder') }} - {{ $form->name }} - {{ \Acelle\Model\Setting::get("site_name") }}</title>
	
	@include('layouts.core._head')
	@include('layouts.core._script_vars')

    <link rel="stylesheet" href="{{ AppUrl::asset('core/slider/bootstrap-slider.min.css') }}" />
    <script type="text/javascript" src="{{ AppUrl::asset('core/slider/bootstrap-slider.min.js') }}"></script>

    <script type="text/javascript" src="{{ AppUrl::asset('core/js/group-manager.js') }}"></script>

    <link type="text/css" rel="stylesheet" href="{{ AppUrl::asset('core/css/form-builder.css') }}" />
</head>

<body class="" style="overflow: hidden">

	<div class="acelle-top-menu">
        <nav class="navbar fixed-top navbar-light bg-light shadow-sm px-4 py-0">
            <span class="navbar-brand d-flex align-items-center pl-0">
                <span class="material-symbols-rounded mr-2">dashboard</span>
                <div class="d-flex align-items-center">
                    <div>
                        {{ trans('messages.form.builder') }} -
                        {{ $form->name }}
                    </div>
                    <div class="ms-3">
                        <span class="label label-flat bg-{{ $form->status }}">{{ trans('messages.form.status.' . $form->status) }}</span>
                    </div>
                </div>
                    
            </span>
            <div class="ms-auto">
                <button onscreen-control="save-button" class="me-2 builder-save-button btn btn-secondary" href="{{ action('FormController@index') }}">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-rounded me-2">task_alt</span>
                            <span>{{ trans('messages.form.builder.save') }}</span>
                    </div>
                </button>
            </div>
            <span class="pe-2 text-muted2"> | </span>
            <div class="me-4">
                @if (Auth::user()->customer->can('publish', $form))
                    <a onscreen-control="publish-unpublish-button" href="{{ action('FormController@publish', [
                        'uids' => [$form->uid],
                    ]) }}" class="me-1 builder-publish-button btn btn-default" href="{{ action('FormController@index') }}">
                        <div class="d-flex align-items-center">
                            <span class="material-symbols-rounded me-2">task_alt</span>
                                <span>{{ trans('messages.form.publish') }}</span>
                        </div>
                            
                    </a>                    
                @endif
                @if (Auth::user()->customer->can('unpublish', $form))
                    <a onscreen-control="publish-unpublish-button" href="{{ action('FormController@unpublish', [
                        'uids' => [$form->uid],
                    ]) }}" class="me-1 builder-unpublish-button btn btn-default" href="{{ action('FormController@index') }}">
                        <div class="d-flex align-items-center">
                            <span class="material-symbols-rounded me-2">do_disturb_on</span>
                                <span>{{ trans('messages.form.unpublish') }}</span>
                        </div>
                            
                    </a>
                @endif
                <a href="javascript:;" class="me-2 builder-view-button btn btn-default">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-rounded me-2">featured_video</span>
                            <span>{{ trans('messages.form.open_popup') }}</span>
                    </div>
                </a> 
            </div>
            
            
            
            <div>
                
                <a class="fs-4 builder-exit-button" href="{{ action('FormController@index') }}"><span class="material-symbols-rounded">close</span></a>
            </div>
            <script>
                $(function() {
                    $('.builder-publish-button, .builder-unpublish-button').on('click', function(e) {
                        e.preventDefault();
                        var but = $(this);

                        addButtonMask(but);
                        // save
                        FormsEdit.getFormsBuilder().save(function() {
                            
                            // do publish/unpublish
                            FormsEdit.topAction(but.attr('href'));
                        })                        
                    });

                    // view
                    $('.builder-view-button').on('click', function() {
                        FormsEdit.openPopup();
                    });
                    
                    // save
                    $('.builder-save-button').on('click', function() {
                        var button = $(this);

                        addButtonMask(button);
                        FormsEdit.getFormsBuilder().save(function() {
                            removeButtonMask(button);
                        });
                    });

                    // update overlay
                    FormsEdit.settings = {!! json_encode($form->getMetadata()) !!};
                });
            </script>
        </nav>
    </div>
    <div class="">
        <iframe id="FormsEditIframe" scrolling="no" style="width: 100%;
        height: calc(100vh - 53px);border:none;overflow:hidden" src="{{ action('FormController@builder', [
            'uid' => $form->uid,
        ]) }}"></iframe>
    </div>

    <div class="styles-settings shadow-sm">
        <div class="styles-sections">
            <div onscreen-control="layouts-button" class="styles-section form-layouts">
                <a href="javascript:;" class="styles-menu-button styles-toggle px-2 py-1">
                    <span class="material-symbols-rounded" style="line-height:30px">dashboard</span>
                </a>
                <div class="styles-container shadow rounded overflow-hidden p-4">
                    <div class="d-flex align-items-center styles-container-heading shadow">
                        <a role="button" href="javascript:;" class="styles-back px-2 py-1">
                            <span class="material-symbols-rounded" style="line-height:30px">keyboard_backspace</span>
                        </a>
                        <h6 class="d-inline ml-2 m-0">{{ trans('messages.form.layouts') }}</h6>
                    </div>  
                    <hr>
                    <div class="">
                        @foreach ($templates->take(5) as $template)
                            <a href="{{ action('FormController@changeTemplate', [
                                'uid' => $form->uid,
                                'template_uid' => $template->uid,
                            ]) }}" class="style-item d-block rounded-3 overflow-hidden mb-4 shadow-sm">
                                <img src="{{ $template->getThumbUrl() }}?v={{ rand(0,10) }}" />
                            </a>
                        @endforeach
                    </div>
                </div>  
            </div>
            <div onscreen-control="themes-button" class="styles-section form-theme">
                <a href="javascript:;" class="styles-menu-button styles-toggle px-2 py-1">
                    <span class="material-symbols-rounded" style="line-height:30px">web</span>
                </a>
                <div class="styles-container shadow rounded overflow-hidden p-4">    
                    <div class="d-flex align-items-center styles-container-heading shadow-sm">
                        <a role="button" href="javascript:;" class="styles-back px-2 py-1">
                            <span class="material-symbols-rounded" style="line-height:30px">keyboard_backspace</span>
                        </a>
                        <h6 class="d-inline ml-2 m-0">{{ trans('messages.form.themes') }}</h6>
                    </div>                    
                        
                    <hr>
                    <div class="">
                        @foreach ($templates->skip(5) as $template)
                            <a href="{{ action('FormController@changeTemplate', [
                                'uid' => $form->uid,
                                'template_uid' => $template->uid,
                            ]) }}" class="style-item d-block rounded-3 overflow-hidden mb-4 shadow-sm">
                                <img src="{{ $template->getThumbUrl() }}?v={{ rand(0,10) }}" />
                            </a>
                        @endforeach
                    </div>
                </div>  
            </div>
            <div onscreen-control="settings-button" class="styles-section form-theme">
                <a href="javascript:;" class="styles-menu-button styles-toggle px-2 py-1">
                    <span class="material-symbols-rounded" style="line-height:30px">tune</span>
                </a>
                <div class="styles-container shadow rounded overflow-hidden p-4" style="
                    overflow-x:hidden!important; height:auto;
                    max-height: calc(100vh - 100px);
                ">    
                    <div class="d-flex align-items-center styles-container-heading shadow-sm">
                        <a role="button" href="javascript:;" class="styles-back px-2 py-1">
                            <span class="material-symbols-rounded" style="line-height:30px">keyboard_backspace</span>
                        </a>
                        <h6 class="d-inline ml-2 m-0">{{ trans('messages.form.form_settings') }}</h6>
                    </div>                    
                        
                    <hr>
                    <div class="" style="width:201px">
                        <form id="FormDisplaySetting" action="{{ action('FormController@settings', [
                            'uid' => $form->uid
                        ]) }}" method="POST">
                            {{ csrf_field() }}

                            <div class="mb-4">
                                @include('helpers.form_control', [
                                    'type' => 'text',
                                    'name' => 'name',
                                    'label' => trans('messages.name'),
                                    'value' => $form->name,
                                    'required' => true,
                                ])
                            </div>

                            <div class="mb-4">
                                @include('helpers.form_control', [
                                    'type' => 'select',
                                    'name' => 'mail_list_uid',
                                    'label' => trans('messages.list'),
                                    'value' => $form->mailList->uid,
                                    'options' => Auth::user()->customer->readCache('MailListSelectOptions', []),
                                    'rules' => [],
                                ])
                            </div>

                            <div class="mb-4">
                                <label class="mb-2">{{ trans('messages.form.overlay_opacity') }}</label>
                                <input id="opacityOverlay" name="overlay_opacity"
                                    type="text"
                                    data-slider-min="0"
                                    data-slider-max="100"
                                    data-slider-step="1"
                                    data-slider-value="{{ $form->getMetadata('overlay_opacity') ? $form->getMetadata('overlay_opacity') : '50'}}"
                                />
                                <script>
                                    $(function() {
                                        // With JQuery
                                        $('#opacityOverlay').slider();
                                    });
                                </script>
                            </div>

                            <div class="mb-3 form-group-mb-0">
                                <label class="mb-2">{{ trans('messages.form.display') }}</label>
                                <p class="small text-muted">{{ trans('messages.form.display.desc') }}</p>
                                @include('helpers.form_control', [
                                    'type' => 'select',
                                    'name' => 'display',
                                    'label' => '',
                                    'value' => $form->getMetadata('display'),
                                    'options' => [
                                        ["value" => "immediately", "text" => trans('messages.form.display.immediately')],
                                        ["value" => "first_visit", "text" => trans('messages.form.display.first_visit')],
                                        ["value" => "wait", "text" => trans('messages.form.display.wait')],
                                        ["value" => "click", "text" => trans('messages.form.display.click')],
                                    ],
                                ])
                            </div>
                            
                            <div class="display-select-page-load mb-3">
                                <div class="">
                                    <p class="small text-muted mb-1">{{ trans('messages.form.display.immediately.desc') }}</p>
                                </div>
                            </div>

                            <div class="display-select-first_visit mb-3">
                                <div class="">
                                    <p class="small text-muted mb-1">{{ trans('messages.form.display.first_visit.desc') }}</p>
                                </div>
                            </div>
                            
                            <div class="display-select-wait mb-3">
                                <div class="">
                                    <p class="small text-muted mb-1">{{ trans('messages.form.display.wait_time.desc') }}</p>
                                    <label class="mb-2">{{ trans('messages.form.display.wait_time') }}</label>                                    
                                    @include('helpers.form_control.number', [
                                        'name' => 'wait_time',
                                        'value' => $form->getMetadata('wait_time') ? $form->getMetadata('wait_time') : '5',
                                        'attributes' => [
                                            'class' => 'numeric',
                                            'min' => '1',
                                            'required' => 'required',
                                        ],
                                    ])
                                </div>
                            </div>

                            <div class="display-select-element mb-3">
                                <p class="small text-muted mb-1">{{ trans('messages.form.display.element_id.desc') }}</p>
                                <label class="mb-2">{{ trans('messages.form.display.element_id') }}</label>                                
                                @include('helpers.form_control.text', [
                                    'name' => 'element_id',
                                    'value' => $form->getMetadata('element_id') ? $form->getMetadata('element_id') : '',
                                    'attributes' => [
                                        'required' => 'required',
                                    ],
                                ])
                            </div>

                            {{-- <div class="mb-3">
                                @include('helpers.form_control', [
                                    'type' => 'checkbox2',
                                    'name' => 'use_captcha',
                                    'label' => trans('messages.form.use_captcha'),
                                    'value' => $form->getMetadata('use_captcha'),
                                    'options' => ['no', 'yes'],
                                ])
                            </div> --}}

                            <div class="mt-4">
                                <button type="button" class="btn btn-primary display-settings-save">{{ trans('messages.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>  
            </div>
            <div onscreen-control="preview-button" class="styles-section form-theme">
                <a href="javascript:;" class="styles-menu-button preview-popup px-2 py-1">
                    <span class="material-symbols-rounded xtooltip" title="{{ trans('messages.preview') }}" style="line-height:30px">visibility</span>
                </a> 
            </div>
        </div>
    </div>

    <script>
        @include('forms.frontend.popupJs')
    </script>

	<script>
        var FormsEdit = {
            styleManager: null,
            displayOption: null,
            settings: null,

            // STYLE MANAGER
            getSettingsManager: function() {
                var _this = this;

                if (_this.styleManager == null) {
                    _this.styleManager = new GroupManager();

                    $('.styles-settings .styles-section').each(function() {
                        _this.styleManager.add({
                            box: $(this),
                            button: $(this).find('.styles-toggle'),
                            container: $(this).find('.styles-container'),
                            back: $(this).find('.styles-back')
                        });
                    });

                    _this.styleManager.bind(function(group, others) {
                        // show
                        group.show = function() {
                            // hide others
                            others.forEach(function(other) {
                                other.hide();
                            });

                            group.container.addClass('show');
                            $('.styles-settings').addClass('open');

                            group.container.scrollTop(0);
                        };

                        // hide
                        group.hide = function() {
                            group.container.removeClass('show');
                            $('.styles-settings').removeClass('open');
                        }

                        group.button.on('click', function() {
                            // toggle container
                            group.show();                       
                        });

                        group.back.on('click', function() {
                            // toggle container
                            group.hide();                       
                        });
                    });
                }
                return this.styleManager;
            },

            // STYLE MANAGER
            getDisplayManager: function() {
                var _this = this;

                if (_this.displayManager == null) {
                    _this.displayManager = new GroupManager();

                    _this.displayManager.add({
                        box: $('.display-select-page-load'),
                        value: 'immediately',
                        selectedValue: function() {
                            return $('[name="display"]').val();
                        }
                    });
                    
                    _this.displayManager.add({
                        box: $('.display-select-first_visit'),
                        value: 'first_visit',
                        selectedValue: function() {
                            return $('[name="display"]').val();
                        }
                    });

                    _this.displayManager.add({
                        box: $('.display-select-element'),
                        value: 'click',
                        selectedValue: function() {
                            return $('[name="display"]').val();
                        }
                    });

                    _this.displayManager.add({
                        box: $('.display-select-wait'),
                        value: 'wait',
                        selectedValue: function() {
                            return $('[name="display"]').val();
                        }
                    });

                    _this.displayManager.bind(function(group) {
                        group.check = function() {
                            if (group.selectedValue() == group.value) {
                                group.box.show();
                            } else {
                                group.box.hide();
                            }
                        }

                        $('[name="display"]').on('change', function() {
                            group.check();
                        });

                        group.check();
                    });                        
                }
                return this.styleManager;
            },

            saveSettings: function() {
                var _this = this;
                var form = $('#FormDisplaySetting');
                var url = form.attr('action');
                var data = form.serialize();

                addMaskLoading();

                if (form.valid()) {
                    // copy
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data
                    }).done(function(response) {
                        notify({
                            message: response.message
                        });

                        removeMaskLoading();

                        _this.hideSidebar();

                        _this.refreshAll();
                    });
                }
            },

            getEditor: function() {
                return document.getElementById('FormsEditIframe').contentWindow.editor;
            },

            getFormsBuilder: function() {
                return document.getElementById('FormsEditIframe').contentWindow.FormsBuilder;
            },

            loadUrls: function(urls, callback) {
                var _this = this;

                _this.getEditor().saveUrl = urls.saveUrl;
                _this.getEditor().uploadAssetUrl = urls.uploadAssetUrl;
                _this.getEditor().url = urls.url;

                // save current form container
                _this.getEditor().cleanUpContent();

                _this.getEditor().unselect();
                _this.getEditor().hideControls();

                _this.getEditor().loadUrl(_this.getEditor().url, function() {
                    if (typeof(callback) != 'undefined') {
                        callback();
                    }
                    _this.getEditor().adjustIframeSize();
                    console.log('aaa');
                });
            },

            connectPopup: null,
            getConnectPopup: function() {
                if (this.connectPopup == null) {
                    this.connectPopup = new Popup({
                        url: '{{ action('FormController@connect', [
                            'uid' => $form->uid,
                        ]) }}'
                    });
                }
                return this.connectPopup;
            },

            openPopup: function() {
                popup = new AFormPopup({
                    url: '{{ action('FormController@frontendContent', [
                        'uid' => $form->uid,
                    ]) }}',
                    overlayOpacity: this.settings.overlay_opacity/100
                });

                popup.load();
            },

            preview: function() {
                var _this = this;

                $.ajax({
                    url: "{{ action('FormController@preview', [
                            'uid' => $form->uid,
                    ]) }}",
                    method: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        content: _this.getFormsBuilder().getEditor().getContent()
                    }
                })
                .done(function(res) {
                    popup = new AFormPopup({
                        url: '{{ action('FormController@frontendContent', [
                            'uid' => $form->uid,
                            'preview' => true,
                        ]) }}',
                        overlayOpacity: _this.settings.overlay_opacity/100
                    });

                    popup.load();
                });
                
            },

            refreshNavbar: function(callback) {
                $.ajax({
                    url: "",
                    method: 'GET'
                })
                .done(function(res) {
                    var html = $('<div>').html(res).find('.navbar').html();
                    $('.navbar').html(html);

                    if (typeof(callback) != 'undefined') {
                        callback();
                    }
                })
            },

            refreshAll: function(callback) {
                var _this = this;

                _this.refreshNavbar(function() {
                    _this.getFormsBuilder().refreshAddressBar(callback);
                });
            },

            topAction: function(url) {
                addMaskLoading();

                // publish
                new Link({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                    },
                    before: function() {
                        
                    },
                    done: function(response) {
                        notify({
                            type: 'success',
                            message: response.message,
                        });

                        FormsEdit.refreshAll(function() {
                            removeMaskLoading();
                        });
                    }
                });
            },

            hideSidebar: function() {
                this.getSettingsManager().groups.forEach(function(group) {
                    group.hide();
                });
            },

            canNotViewUnpublishedForm: function() {
                new Dialog('alert', {
                    message: `{{ trans('messages.form.view_in_site_but_is_not_published') }}`
                });
            },

            changeTemplate: function(url) {
                var _this = this;

                // copy
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN
                    }
                }).done(function(response) {
                    _this.loadUrls(response, function() {
                        _this.getFormsBuilder().openFormTab();
                    });
                    _this.hideSidebar();
                });
            }
        }
        
        $(function() {
            FormsEdit.getSettingsManager();
            FormsEdit.getDisplayManager();
            

            // remove loadding effect
            $('.lds-dual-ring').remove();

            $('.style-item').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                FormsEdit.changeTemplate(url);
            });

            // display setting save
            $('.display-settings-save').on('click', function() {
                FormsEdit.saveSettings();
            });


            // preview
            $('.preview-popup').on('click', function() {
                FormsEdit.preview();
            });

            // hide style
            $('.navbar').on('click', function() {
                FormsEdit.hideSidebar();
            });

            // onscreen intro
            @if (!Auth::user()->isOnscreenIntroShowed('form.builder'))
                new OnscreenIntro();

                @php
                    Auth::user()->setOnscreenIntroShowed('form.builder');
                @endphp
            @endif
        })

        var OnscreenIntro = class {
            constructor() {
                var _this = this;

                this.hightlightBox = null;
                this.arrow = null;
                this.textbox = null;
                this.currentStepNumber = 0;

                this.steps = [
                    // Layouts
                    {
                        getControl: function() {
                            return $('[onscreen-control="layouts-button"]');
                        },
                        message: "{{ trans('messages.form.onscreen.layouts.intro') }}",
                    },

                    // Theme
                    {
                        getControl: function() {
                            return $('[onscreen-control="themes-button"]');
                        },
                        message: "{{ trans('messages.form.onscreen.themes.intro') }}",
                    },

                    // Settings
                    {
                        getControl: function() {
                            return $('[onscreen-control="settings-button"]');
                        },
                        message: "{{ trans('messages.form.onscreen.settings.intro') }}",
                    },

                    // Preview
                    {
                        getControl: function() {
                            return $('[onscreen-control="preview-button"]');
                        },
                        message: "{{ trans('messages.form.onscreen.preview.intro') }}",
                    },

                    // Save
                    {
                        getControl: function() {
                            return $('[onscreen-control="save-button"]');
                        },
                        message: "{{ trans('messages.form.onscreen.save.intro') }}",
                        position: 'left',
                    },

                    // Publbish/Ubpublish
                    {
                        getControl: function() {
                            return $('[onscreen-control="publish-unpublish-button"]');
                        },
                        message: "{{ trans('messages.form.onscreen.publish_unpublish.intro') }}",
                        position: 'left',
                    },
                ]

                // init box
                this.initHightlightBox();

                // init arrow
                this.initArrow();

                // init textbox
                this.initTextbox();

                // 
                this.loadStep(this.currentStepNumber);

                // events
                this.hightlightBox.on('click', function() {
                    _this.nextStep();
                });
            }

            stepCount() {
                return this.steps.length;
            }

            getStepByNumber(number) {
                return this.steps[number];
            }

            loadStep(stepNumber) {
                var currentStep = this.getStepByNumber(stepNumber);

                //
                this.hightlight(currentStep.getControl(), currentStep.message, currentStep.position ?? 'right');
            }

            nextStep() {
                this.currentStepNumber += 1;

                if (this.stepCount() <= this.currentStepNumber) {
                    this.exit();

                    return;
                }
                
                this.loadStep(this.currentStepNumber);
            }

            exit() {
                this.hideHightlightBox();
                this.hideArrow();
                this.hideTextbox();

                this.currentStepNumber = 0;
            }

            hightlight(control, text, position) {
                // load box
                this.showHightlightBox();
                this.hightlightBox.find('[onscreen-control="hightlight-box"]').css('top', control.offset().top);
                this.hightlightBox.find('[onscreen-control="hightlight-box"]').css('left', control.offset().left);
                this.hightlightBox.find('[onscreen-control="hightlight-box"]').css('width', control.outerWidth());
                this.hightlightBox.find('[onscreen-control="hightlight-box"]').css('height', control.outerHeight());

                // load arrow
                this.showArrow();
                this.arrow.css('top', control.offset().top + control.height()/2 - 20);
                this.arrow.css('left', control.offset().left + control.outerWidth() + 20);

                // textbox
                this.showTextbox();
                this.textbox.css('top', control.offset().top + this.arrow.height() + 20);
                this.textbox.css('left', control.offset().left + this.arrow.outerWidth() + 20);
                this.textbox.find('[onscreen-control="text"]').html(text);

                // position
                if (position == 'left') {
                    this.arrow.css('transform', 'scaleX(-1)');
                    this.arrow.css('top', control.offset().top + control.height()/2 - 20);
                    this.arrow.css('left', control.offset().left - this.arrow.outerWidth() - 20);

                    this.textbox.css('top', control.offset().top + control.height() + this.arrow.outerHeight());
                    this.textbox.css('left', control.offset().left - this.arrow.outerWidth() - 20 - this.textbox.width()/2);
                }
            }

            initHightlightBox() {
                this.hightlightBox = $(`<div style="display:none;">
                    <div style="
                        opacity: 0;
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 10000;
                        transition: all 0.2s ease-in-out;
                    ">

                    </div>
                    <div onscreen-control="hightlight-box" class="transparent-box" style="
                        opacity: 0.9;
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100px;
                        height: 80px;
                        box-shadow: 0 0 0 1000pc rgba(0, 0, 0, .9);
                        z-index: 10001;
                        border-radius:5px;
                        transition: all 0.2s ease-in-out;
                    ">
                    </div>
                </div>`);

                $('body').append(this.hightlightBox);
            }

            hideHightlightBox() {
                this.hightlightBox.hide();
            }

            showHightlightBox() {
                this.hightlightBox.show();
            }
            
            initArrow() {
                this.arrow = $(`<div class="onscreen-arrow" style="
                    position: fixed;
                    display:none;
                    z-index: 10002;
                    transition: all 0.2s ease-in-out;
                ">
                    <svg style="width: 200px" xmlns="http://www.w3.org/2000/svg" id="Layer_2" viewBox="0 0 252.9 162.53"><g id="Layer_1-2"><path d="m225.45,162.45c-.14.02-.27.08-.41.08-.08,0-.15-.04-.24-.05-.11,0-.21.03-.31.02-.18-.03-.33-.12-.49-.18-.02,0-.04-.01-.05-.02-.59-.22-1.08-.59-1.4-1.1l-24.54-24.54c-1.1-1.1-1.1-2.87,0-3.97s2.87-1.1,3.97,0l20.82,20.82c4.61-43.41-11.75-79.92-45.5-100.73-1.32-.81-1.73-2.55-.92-3.87s2.55-1.73,3.87-.92c31.76,19.57,49.01,52.15,49.01,91.26,0,4.23-.22,8.54-.63,12.91l19.48-19.48c1.1-1.1,2.87-1.1,3.97,0,.55.55.82,1.27.82,1.99s-.28,1.44-.82,1.99l-25.05,25.05c-.37.37-.84.57-1.33.69-.08.03-.16.03-.25.05Z" style="fill:#fff; stroke-width:0px;"/><path d="m90.37,2.34c29.55,6,55.81,23.61,66.91,44.87,2.61,5.04,3.89,11,3.89,17.38,0,10.01-3.15,21.06-9.23,31.15-6.56,10.9-15.69,19.41-25.7,23.96-10.37,4.71-20.47,4.66-28.45-.15-16.11-9.7-18.04-36.17-4.3-58.99,0-.02.03-.03.04-.04,5.89-9.76,14.02-17.77,22.91-22.54,1.37-.73,3.07-.22,3.8,1.15.23.42.33.88.33,1.33,0,1-.54,1.97-1.48,2.48-8.02,4.31-15.4,11.6-20.78,20.54,0,.01-.02.02-.03.03-12.12,20.17-11.05,43.14,2.4,51.25,6.34,3.82,14.59,3.76,23.22-.16,8.98-4.08,17.23-11.8,23.21-21.74,8.97-14.89,11-31.79,5.17-43.04-10.34-19.81-35.08-36.28-63.04-41.96C58.23,1.54,28.17,8.77,4.6,28.18c-1.2.99-2.97.81-3.96-.38-.99-1.2-.82-2.97.38-3.96C25.92,3.33,57.65-4.31,90.37,2.34Z" style="fill:#fff; stroke-width:0px;"/></g></svg>
                </div>`);

                $('body').append(this.arrow);
            }

            hideArrow() {
                this.arrow.hide();
            }

            showArrow() {
                this.arrow.show();
            }

            initTextbox() {
                this.textbox = $(`<div class="onscreen-textbox" style="
                    position: fixed;
                    display:none;
                    z-index: 10002;
                    max-width: 550px;
                    line-height: 30px;
                    transition: all 0.2s ease-in-out;
                ">
                    <i><span onscreen-control="text" class="text-white display-4"></span></ i>
                </div>`);

                $('body').append(this.textbox);
            }

            hideTextbox() {
                this.textbox.hide();
            }

            showTextbox() {
                this.textbox.show();
            }
        }
    </script>
</body>
</html>
