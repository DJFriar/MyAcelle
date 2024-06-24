@extends('layouts.popup.large')

@section('bar-title')
    {{ trans('messages.subscriber_import') }}
@endsection

@section('content')
    <!-- Dropzone -->
	<script type="text/javascript" src="{{ AppUrl::asset('core/dropzone/dropzone.js') }}"></script>
	<link href="{{ AppUrl::asset('core/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css">

	<div class="popup-wizard">

        @include('subscribers.import2._sidebar', ['step' => 'mapping'])

        <div class="wizard-content">
            <p>{!! trans('messages.subscriber.import.mapping.wording', [
                'link' => url('files/csv_import_example.csv')
            ]) !!}</p>   

            <ul class="list-group mb-4">
                @foreach($headers as $key => $header)
                    <li mapping-container="{{ $header }}" class="list-group-item">
                        <div class="d-flex align-items-center">
                            <label class="checker me-3">
                                <input id="MappingChecker{{ $key }}" type="checkbox" mapping-control="checker" value="yes" class="styled4">
                                <span class="checker-symbol"></span>
                            </label>
                            <label for="MappingChecker{{ $key }}" class="">{{ $header }}</label>
                            <div class="d-flex ms-auto">
                                <div mapping-control="short-desc" class="me-2">
                                    
                                </div>
                                <a mapping-control="edit" href="javascript:;">
                                    <span>
                                        <span class="material-symbols-rounded">edit</span>
                                    </span>
                                </a>
                                <a mapping-control="close" href="javascript:;">
                                    <span>
                                        <span class="material-symbols-rounded">close</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div mapping-control="options" class="border-top bg-light py-3" style="
                            margin-left: -16px;
                            margin-right: -16px;
                            padding-left: 55px;
                            padding-right: 16px;
                            margin-bottom: -8px;
                            margin-top: 8px;
                        ">
                            <div mapping-control="option" option-value="exist">
                                <label class="">
                                    <input mapping-control="option-checker" name="option-checker-{{ $key }}" type="radio" value="exist" class="styled" />
                                    <span class="check-symbol me-1"></span>
                                    <span class="mr-2 text-nowrap">{{ trans('messages.import.associate_with_existing_field') }}</span>
                                </label>
                                <div mapping-control="option-detail" class="ps-4 ms-1 mb-1 mt-2">
                                    <p class="mb-2">{{ trans('messages.subscriber.import.associate_exist.intro') }}</p>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <select class="select form-control" mapping-control="field-select">
                                                <option value="">{{ trans('messages.import.choose_field') }}</option>
                                                @foreach ($list->fields as $field)
                                                    <option value="{{ $field->id }}">{{ $field->tag }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <a href="javascript:;" class="text-nowrap" mapping-control="save" style="text-decoration: underline">
                                            {{ trans('messages.subscriber.import.ok_i_done') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div mapping-control="option" option-value="new" style="display:none;">
                                <label class="">
                                    <input mapping-control="option-checker" name="option-checker-{{ $key }}" type="radio" value="new" class="styled" />
                                    <span class="check-symbol me-1"></span>
                                    <span class="mr-2 text-nowrap font-weight-semibold">
                                        {{ trans('messages.import.create_new_field') }}
                                    </span>
                                </label>
                                <div mapping-control="option-detail" class="ps-4 ms-1 mt-2">
                                    <p class="text-muted mb-2 option-empty">{{ trans('messages.subscriber.import.create_new_field.intro') }}</p>
                                    <p class="text-muted mb-2 option-done" style="display: none">
                                        {!! trans('messages.subscriber.import.create_field.summary') !!}
                                    </p>

                                    <div class="option-update mb-2">
                                        <div class="mb-1">{{ trans('messages.subscriber.import.field_name') }}</div>
                                        <div><input mapping-control="new-field-name" type="text" name="new_field" class="form-control mb-3 data-create" /></div>
                                        <div class="mb-1">{{ trans('messages.subscriber.import.data_type') }}</div>
                                        <div class="d-flex align-items-center">
                                            <select mapping-control="new-field-type" class="select data-type" style="max-width: 100px;">
                                                <option value="text">{{ trans('messages.text') }}</option>
                                                <option value="number">{{ trans('messages.number') }}</option>
                                                <option value="date">{{ trans('messages.date') }}</option>
                                                <option value="datetime">{{ trans('messages.datetime') }}</option>
                                                <option value="textarea">{{ trans('messages.textarea') }}</option>
                                            </select>
                                            <a mapping-control="save" href="javascript:;" class="text-nowrap ml-4 done-button" style="text-decoration: underline">
                                                {{ trans('messages.subscriber.import.ok_i_done') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            

                    </li>
                @endforeach
            </ul>

            @if (config('custom.japan'))
                <div class="my-4">
                    <div class="form-group">
                        <label class="fw-semibold">
                            {{ trans('messages.export.encoding') }}
                        </label>
                        
                        <div>
                            <label class="main-control">
                                <input type="radio"
                                    checked
                                    name="encoding"
                                    value="utf8" class="styled" /> <rtitle class="ms-2">{{ trans('messages.export.encoding.utf8') }}</rtitle>
                            </label>
                        </div>
                        <div>
                            <label class="main-control">
                                <input type="radio"
                                    name="encoding"
                                    value="japanese-iso" class="styled me-2" /> <rtitle class="ms-2">{{ trans('messages.export.encoding.japanese_iso') }}</rtitle>
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            <a mapping-control="run" href="javascript:;"
                class="btn btn-mc_primary bg-teal-800 mt-4"
            >
                {{ trans('messages.subscriber.import.start') }}
            </a>
        </div>
    </div>

    <script>
        var MappingItem = class {
            constructor(manager, header) {
                this.manager = manager;
                this.header = header;
                this.option;
                this.fieldId;
                this.newFieldName;
                this.newFieldType;

                // default option
                this.updateOption('exist');

                // default field id
                this.updateFieldId(null);

                // hide options box
                this.hideOptionsBox();

                // events
                this.applyEvents();

                // hide close link
                this.hideCloseLink();
            }

            getContainer() {
                return $('[mapping-container="' + this.header + '"]');
            }

            getOptionBoxes() {
                return this.getContainer().find('[mapping-control="option"]');
            }

            getOptionBox(option) {
                return this.getContainer().find('[option-value="'+option+'"]');
            }

            getFieldSelect() {
                return this.getContainer().find('[mapping-control="field-select"]');
            }

            getChecker() {
                return this.getContainer().find('[mapping-control="checker"]');
            }

            getExistSaveButton() {
                return this.getOptionBox('exist').find('[mapping-control="save"]');
            }

            getNewSaveButton() {
                return this.getOptionBox('new').find('[mapping-control="save"]');
            }

            getNewFieldName() {
                return this.getOptionBox('new').find('[mapping-control="new-field-name"]');
            }

            getNewFieldType() {
                return this.getOptionBox('new').find('[mapping-control="new-field-type"]');
            }

            getEditLink() {
                return this.getContainer().find('[mapping-control="edit"]');
            }

            getCloseLink() {
                return this.getContainer().find('[mapping-control="close"]');
            }

            getOptionsBox() {
                return this.getContainer().find('[mapping-control="options"]');
            }

            getShortDesc() {
                return this.getContainer().find('[mapping-control="short-desc"]');
            }

            getOptionCheckers() {
                return this.getContainer().find('[mapping-control="option-checker"]');
            }

            isChecked() {
                return this.getChecker().is(':checked');
            }

            markAsError() {
                this.getContainer().addClass('mapping-error');
            }

            unmarkError() {
                this.getContainer().removeClass('mapping-error');
            }

            updateOption(option) {
                this.option = option;

                // check option
                this.getOptionBox(this.option).find('[mapping-control="option-checker"]').prop('checked', true);

                // show option details
                this.getOptionBoxes().find('[mapping-control="option-detail"]').hide();
                this.getOptionBox(this.option).find('[mapping-control="option-detail"]').show();
                this.getOptionBoxes().removeClass('current');
                this.getOptionBox(this.option).addClass('current');
            }

            updateFieldId(id) {
                // set field id
                this.fieldId = id !== null ? id.toString() : null;

                // set option to exist
                this.updateOption('exist');

                // field id
                if (this.fieldId !== null) {
                    // change select field value
                    this.getFieldSelect().val(this.fieldId).trigger('change');

                    // update short desc message
                    this.getShortDesc().html(`
                        <span class="d-flex align-items-center">
                            <span>{{ trans('messages.subscriber.import.associate_with') }}</span>
                            <span mapping-control="selected-field-label" class="border rounded px-1 mx-2 bg-light" style="font-size: 12px;">
                                `+ this.getFieldSelect().find('option[value="'+this.fieldId+'"]').html() +`    
                            </span>
                        </span>
                    `);

                    // show edit link
                    this.showEditLink();
                    
                    // show short desc line
                    this.showShortDesc();

                    // turn on checker
                    this.getChecker().prop('checked', true);
                } else {
                    // change select field value
                    // this.getFieldSelect().val('').trigger('change');

                    // clear short desc
                    this.getShortDesc().html('');

                    // hide edit link
                    this.hideEditLink();

                    // hide short desc line
                    this.hideShortDesc();

                    // turn off checker
                    this.getChecker().prop('checked', false);
                }
            }

            uncheck() {
                // back to exist option
                this.updateOption('exist');

                // update fieldId to null
                this.updateFieldId(null);

                // hide options box
                this.hideOptionsBox();

                // show run button
                this.manager.showRunButton();

                // hide close link
                this.hideCloseLink();

                // run validate to mark error
                this.manager.validate();
            }

            applyEvents() {
                var _this = this;
                
                // check the checkbox
                this.getChecker().on('change', function() {
                    var checked = $(this).is(':checked');

                    // checked
                    if (checked) {
                        // show options box
                        _this.showOptionsBox();

                        // hide run button
                        _this.manager.hideRunButton();

                        // show close link
                        _this.showCloseLink();

                    // uncheck
                    } else {
                        _this.uncheck();
                    }
                });

                // save the field select
                this.getExistSaveButton().on('click', function() {
                    _this.saveExistField();
                });

                // edit details
                this.getEditLink().on('click', function() {
                    // show details box
                    _this.showOptionsBox();

                    // hide run button
                    _this.manager.hideRunButton();

                    // hide edit link
                    _this.hideEditLink();

                    // show close link
                    _this.showCloseLink();
                });

                // change option
                this.getOptionCheckers().on('change', function() {
                    var option = $(this).val();

                    _this.updateOption(option);
                });

                // save the field select
                this.getNewSaveButton().on('click', function() {
                    _this.saveNewField();
                });

                // close
                this.getCloseLink().on('click', function() {
                    _this.close();
                });
            }

            saveExistField() {
                var id = this.getFieldSelect().val();

                if (id == '') {
                    new Dialog('alert', {
                        message: '{{ trans('messages.import.field_not_select') }}',
                    });

                    return;
                }

                // set field id
                this.updateFieldId(id);

                // hide details box after saved
                this.hideOptionsBox();

                // show run button
                this.manager.showRunButton();

                // hide close link
                this.hideCloseLink();
console.log('validate');
                // run validate to mark error
                this.manager.validate();
            }

            saveNewField() {
                var name = this.getNewFieldName().val();
                var type = this.getNewFieldType().val();

                if (name == '') {
                    new Dialog('alert', {
                        message: '{{ trans('messages.import.field_name_empty') }}',
                    });

                    return;
                }

                // set field id
                this.updateNewField(name, type);

                // hide details box after saved
                this.hideOptionsBox();

                // show run button
                this.manager.showRunButton();

                // hide close link
                this.hideCloseLink();

                // run validate to mark error
                this.manager.validate();
            }

            updateNewField(name, type) {
                // set field id
                this.newFieldName = name;
                this.newFieldType = type;

                // set option to exist
                this.updateOption('new');

                // field id
                if (this.newFieldName) {
                    // change select field value
                    this.getNewFieldName().val(this.newFieldName);
                    this.getNewFieldType().val(this.newFieldType).trigger('change');

                    // update short desc message
                    this.getShortDesc().html(`
                        <span class="d-flex align-items-center">
                            <span>{{ trans('messages.subscriber.import.associate_with_new') }}</span>
                            <span mapping-control="selected-field-label" class="border rounded px-1 mx-2 bg-light" style="font-size: 12px;">
                                `+ this.newFieldName +`    
                            </span>
                        </span>
                    `);

                    // show edit link
                    this.showEditLink();
                    
                    // show short desc line
                    this.showShortDesc();

                    // turn on checker
                    this.getChecker().prop('checked', true);
                } else {
                    // change select field value
                    this.getNewFieldName().val('');
                    // this.getNewFieldType().val(this.newFieldType).trigger('change');

                    // clear short desc
                    this.getShortDesc().html('');

                    // hide edit link
                    this.hideEditLink();

                    // hide short desc line
                    this.hideShortDesc();

                    // turn off checker
                    this.getChecker().prop('checked', false);
                }
            }

            hasValue() {
                if (this.option == 'exist') {
                    if (this.fieldId) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (this.option == 'new') {
                    if (this.newFieldName && this.newFieldType) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            close() {
                // close options box
                this.hideOptionsBox();

                // show run button
                this.manager.showRunButton();

                // uncheck checker
                if (!this.hasValue()) {
                    this.getChecker().prop('checked', false);
                }

                // show edit link
                if (this.hasValue()) {
                    // show edit link
                    this.showEditLink();
                }

                // hide close link
                this.hideCloseLink();
            }

            hideOptionsBox() {
                this.getOptionsBox().hide();
            }

            hideShortDesc() {
                this.getShortDesc().hide();
            }

            showShortDesc() {
                this.getShortDesc().show();
            }

            showOptionsBox() {
                this.getOptionsBox().slideDown();
            }

            hideEditLink() {
                this.getEditLink().css('display', 'none');
            }

            showEditLink() {
                this.getEditLink().css('display', 'inline-block');
            }

            hideCloseLink() {
                this.getCloseLink().css('display', 'none');
            }

            showCloseLink() {
                this.getCloseLink().css('display', 'inline-block');
            }

            getData() {
                if (this.option == 'exist') {
                    if (this.fieldId) {
                        return this.fieldId;
                    } else {
                        return null;
                    }
                } else {
                    if (this.newFieldName && this.newFieldType) {
                        return {name: this.newFieldName, type: this.newFieldType};
                    } else {
                        return null;
                    }
                }
            }
        };

        var MappingManager = class {
            constructor(headers, mapping, runUrl, emailFieldId, validateUrl) {
                var _this = this;

                _this.items = [];
                _this.runUrl = runUrl;
                _this.emailFieldId = emailFieldId;
                _this.validateUrl = validateUrl;

                // add items by headers
                headers.forEach(function(header) {
                    _this.items.push(new MappingItem(_this, header));
                });

                if (mapping) {
                    _this.importData(mapping);
                }

                // run button
                _this.getRunButton().on('click', function() {
                    _this.run();
                });
            }
            
            getRunButton() {
                return $('[mapping-control="run"]');
            }

            hideRunButton() {
                this.getRunButton().hide();
            }

            showRunButton() {
                this.getRunButton().show();
            }

            validate() {
                var errors = [];

                var fieldIds = this.items
                    .filter(function(i) {return i.option == 'exist' && i.fieldId})
                    .map(function(i) {return i.fieldId});

                var fieldNames = this.items
                    .filter(function(i) {return i.option == 'new' && i.newFieldName})
                    .map(function(i) {return i.newFieldName});

                // unmark all error
                this.items.map(function(item) {item.unmarkError();});

                // find dup items
                let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) != index)

                // at least one email field
                if (!fieldIds.includes(this.emailFieldId)) {
                    errors.push('{{ trans('messages.subscriber.import.no_email_field') }}');
                }

                // more than one field
                var idDups = findDuplicates(fieldIds);
                var nameDups = findDuplicates(fieldNames);

                // exist field dup
                if (idDups.length) {
                    errors.push('{{ trans('messages.subscriber.import.multiple_field_error') }}');

                    // mark error
                    this.items.map(function(item) {
                        if(idDups.includes(item.fieldId)) {
                            item.markAsError();
                        }
                    });
                }

                // new field dup
                if (nameDups.length) {
                    errors.push('{{ trans('messages.subscriber.import.multiple_field_error') }}');
                }
                
                return errors;
            }

            remoteValidate(success, failed) {
                addMaskLoading();

                $.ajax({
                    url: this.validateUrl,
                    type: 'POST',
                    dataType: "json",
                    globalError: false,
                    data: {
                        _token: CSRF_TOKEN,
                        mapping: this.getData(),
                    }
                }).done(function(response) {
                    success();
                }).fail(function(response) {
                    failed(JSON.parse(response.responseText).message);
                }).always(function() {
                    removeMaskLoading();
                });
            }

            run() {
                var _this = this;

                var errors = this.validate();
                console.log(errors);
                
                if (errors.length) {
                    var msg = errors.join('<br>');

                    new Dialog('alert', {
                        message: msg,
                    });

                    return;
                }

                this.remoteValidate(
                    // success
                    function() {
                        addMaskLoading();
                        
                        $.ajax({
                            url: _this.runUrl,
                            type: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                                filepath: '{{ $filepath }}',
                                mapping: _this.getData(),
                                @if (config('custom.japan'))
                                    // encoding
                                    encoding: $('[name=encoding]:checked').val(),
                                @endif
                            }
                        }).done(function(response) {
                            SubscribersImport2.getPopup().load(response.progressUrl);
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                        }).always(function() {
                            removeMaskLoading();
                        });
                    },
                    //failed
                    function(error) {
                        new Dialog('alert', {
                            message: error,
                        });
                    }
                );

                    
            }

            getData() {
                var data = {};
                this.items.forEach(function(item) {
                    if (item.hasValue()) {
                        data[item.header] = item.getData();
                    }
                });

                return data;
            }

            importData(mapping) {
                this.items.forEach(function(item) {
                    if (mapping[item.header]) {
                        item.updateFieldId(mapping[item.header]);
                    }
                }); 

                // run validate to mark error
                this.validate();
            }
        };

        var SubscribersImport2Mapping = {
            mappingManager: null,
        }

        $(function() {
            SubscribersImport2Mapping.mappingManager = new MappingManager(
                {!! json_encode($headers) !!},
                {!! json_encode($list->generateAutoMapping($headers)) !!},
                '{!! action('SubscriberController@import2Run', [
                    'list_uid' => $list->uid,
                ]) !!}',
                '{{ $list->getEmailField()->id }}',
                '{!! action('SubscriberController@import2Validate', [
                    'list_uid' => $list->uid,
                ]) !!}'
            );
        });
    </script>
@endsection