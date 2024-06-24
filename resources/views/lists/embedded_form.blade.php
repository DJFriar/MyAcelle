@extends('layouts.core.frontend', [
    'menu' => 'list',
])

@section('title', $list->name)

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ AppUrl::asset('core/prismjs/prism.css') }}">
    <script type="text/javascript" src="{{ AppUrl::asset('core/prismjs/prism.js') }}"></script>


    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/anytime.min.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/pickadate/picker.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/datetime/pickadate/picker.date.js') }}"></script>

    <script type="text/javascript" src="{{ AppUrl::asset('core/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ AppUrl::asset('core/js/editor.js') }}"></script>

@endsection

@section('page_header')

    @include("lists._header")

@endsection

@section('content')

    @include("lists._menu", [
        'menu' => 'embedded',
    ])
    <h2 class="text-semibold text-primary my-4">{{ trans('messages.Embedded_form') }}</h2>
    
    <div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-semibold">{{ trans('messages.options') }}</h4>
                        <form id="EmbeddedForm" action="{{ action("MailListController@embeddedForm", $list->uid) }}" class="embedded-options-form">
                            {{ csrf_field() }}
                            <div class="" style="width:100%;justify-content: space-between">
                                <div class="">
                                    @include('helpers.form_control', ['type' => 'text',
                                            'name' => 'options[form_title]',
                                            'label' => trans('messages.form_title'),
                                            'value' => $list->getEmbeddedFormOption('form_title'),
                                            'help_class' => 'list'
                                    ])
            
                                    @include('helpers.form_control', ['type' => 'text',
                                            'name' => 'options[redirect_url]',
                                            'label' => trans('messages.list.embedded_form.redirect_url'),
                                            'value' => $list->getEmbeddedFormOption('redirect_url'),
                                            'help_class' => 'list',
                                            'placeholder' => trans('messages.list.redirect_url.placeholder'),
                                    ])
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{!! trans('messages.show_only_required_fields', ["link" => action('FieldController@index', $list->uid)]) !!}</label>
                                        <div class="notoping ps-2">
                                            @include('helpers.form_control', ['type' => 'checkbox',
                                                'name' => 'options[only_required_fields]',
                                                'label' => '',
                                                'value' => $list->getEmbeddedFormOption('only_required_fields'),
                                                'options' => ['no','yes'],
                                                'help_class' => 'list'
                                            ])
                                        </div>
                                    </div>
            
                                    <div class="form-group">
                                        <label>{{ trans('messages.stylesheet_included') }}</label>
                                        <div class="notoping ps-2">
                                            @include('helpers.form_control', ['type' => 'checkbox',
                                                'name' => 'options[stylesheet]',
                                                'label' => '',
                                                'value' => $list->getEmbeddedFormOption('stylesheet'),
                                                'options' => ['no','yes'],
                                                'help_class' => 'list'
                                            ])
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('messages.include_javascript') }}</label>
                                        <div class="notoping ps-2">
                                            @include('helpers.form_control', ['type' => 'checkbox',
                                                'name' => 'options[javascript]',
                                                'label' => '',
                                                'value' => $list->getEmbeddedFormOption('javascript'),
                                                'options' => ['no','yes'],
                                                'help_class' => 'list'
                                            ])
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('messages.embeded_form.show_invisible') }}</label>
                                        <div class="notoping ps-2">
                                            @include('helpers.form_control', ['type' => 'checkbox',
                                                'name' => 'options[show_invisible]',
                                                'label' => '',
                                                'value' => $list->getEmbeddedFormOption('show_invisible'),
                                                'options' => ['no','yes'],
                                                'help_class' => 'list'
                                            ])
                                        </div>
                                    </div>
                                    <div>
                                    </div>
                                </div>
                                @include('helpers.form_control', ['type' => 'textarea',
                                    'name' => 'options[custom_css]',
                                    'class' => 'height-100 text-small',
                                    'label' => trans('messages.custom_css'),
                                    'value' => $list->getEmbeddedFormOption('custom_css'),
                                    'help_class' => 'list'
                                ])

                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-auto">
                                            <div class="d-flex align-items-center">
                                                <div class="notoping pe-3 ps-2">
                                                    @include('helpers.form_control', ['type' => 'checkbox',
                                                        'name' => 'options[enable_term]',
                                                        'label' => '',
                                                        'value' => $list->getEmbeddedFormOption('enable_term'),
                                                        'options' => ['no','yes'],
                                                        'help_class' => 'list',
                                                    ])
                                                </div>
                                                <label>{{ trans('messages.embedded_form.enable_term') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-control="term" style="display:none">
                                        <textarea class="form-control term-editor" style="height:100px" name="options[term]">{!! $list->getEmbeddedFormOption('term') !!}</textarea>
                                        <div class="mt-2 text-end">
                                            <button type="button" data-control="save-term" class="btn btn-secondary">{{ trans('messages.save') }}</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 embedded-form-result">
                

                <div style="position:sticky;top:10px;">
                    <div class="d-flex">
                        <h4 class="text-semibold me-auto">{{ trans('messages.Copy_paste_onto_your_site') }}</h4>
                        <div>
                            <a href="javascript:;" onclick="copyToClipboard(htmlDecode($('.main-code').html()));
                            notify('success', '{{ trans('messages.notify.success') }}', '{{ trans('messages.embedded_form_code.copied') }}');" class="btn btn-primary copy-clipboard">
                                <span class="material-symbols-rounded">content_copy</span> {{ trans('messages.copy') }}</a>
                        </div>
                    </div>

                    
                    <ul class="nav nav-tabs nav-tabs-top nav-underline mb-1" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                                {{ trans('messages.preview') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                                {{ trans('messages.embedded_form.source_code') }}
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                            <h4 class="text-semibold"></h4>
                            <iframe class="embedded_form mb-4" src="{{ action("MailListController@embeddedFormFrame", $list->uid) }}"></iframe>

                            
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            <pre class="language-markup content-group embedded-code"><code></code></pre>
                            <code style="height: 400px" class="form-control main-code hide">@include("lists._embedded_form_content")</code>
                        </div>
                    </div>
                    
                </div>
                    
                
            </div>
        </div>
    </div>

    <hr />
    
    
    

    

    <script>
        var EmbeddedForm = {
            formatCopyCode: function() {
                var bio_text = $(".main-code").html();
                bio_text = bio_text.replace(/\</g, '&lt;');
                bio_text = bio_text.replace(/script_tmp/g, 'script');
                bio_text = bio_text.replace(/\t/g, '');
                bio_text = bio_text.replace(/\n/g, '');
                bio_text = bio_text.replace(/\s+/g, ' ');
                bio_text = bio_text.replace(/\>\s*&lt;/g, "&gt;\n&lt;");
                bio_text = bio_text.replace(/\s+\{\s+/g, "{");
                $("code").html(bio_text);
                
                // Hightlight code
                Prism.highlightAll();
            },

            save: function() {
                var form = $('#EmbeddedForm');
                var url = form.attr('action');
                var data = form.serialize();

                $.ajax({
                    method: "POST",
                    url: url,
                    data: data
                })
                .done(function( msg ) {
                    var html = $("<div>").html(msg).find(".embedded-form-result").html();
                    $(".embedded-form-result").html(html);
                    
                    EmbeddedForm.formatCopyCode();
                });
            },

            toggleTermInput() {
                var checked = $('[name="options[enable_term]"]').is(':checked');

                if (checked) {
                    $('[data-control="term"]').show();
                } else {
                    $('[data-control="term"]').hide();
                }
            }
        };
        
        $(function() {
            EmbeddedForm.formatCopyCode();

            //
            $(document).on("change keyup", ".embedded-options-form :input", function() {
                var url = $(this).parents("form").attr("action");

                EmbeddedForm.save();
            });

            // term click
            EmbeddedForm.toggleTermInput();
            $('[name="options[enable_term]"]').on('change', function() {
                EmbeddedForm.toggleTermInput();
            });

            //
            tinymce.init({
                selector: '.term-editor',
                height: 200,
                convert_urls: false,
                remove_script_host: false,
                forced_root_block: "",
                skin: "oxide",
                branding: false,
                elementpath: false,
                statusbar: false,
                plugins: [
                'fullpage table advlist autolink lists link image charmap print preview anchor directionality',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media paste code'
                ],
                setup: function(editor) {
                    editor.on('input', function(e) {
                        editor.save();
                    });
                },
                toolbar: 'insertfile undo redo | fontselect | fontsizeselect | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | ltr rtl',
                valid_elements : '*[*],meta[*]',
                valid_children: '+p[ol],+p[ul],+h1[div],+h2[div],+h3[div],+h4[div],+h5[div],+h6[div],+a[div],*[*]',
                extended_valid_elements : "meta[*]",
                valid_children : "+body[style],+body[meta],+div[h2|span|meta|object],+object[param|embed]",
                external_filemanager_path:APP_URL.replace('/index.php','')+"/filemanager2/",
                filemanager_title:"Responsive Filemanager" ,
                external_plugins: { "filemanager" : APP_URL.replace('/index.php','')+"/filemanager2/plugin.min.js"}
            });

            // save
            $('[data-control="save-term"]').on('click', function() {
                EmbeddedForm.save();
            });
            
        });
    </script>
@endsection
