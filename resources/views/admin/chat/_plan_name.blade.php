@php
    $var_name = str_replace('[]', '', $name);
    $var_name = str_replace('][', '.', $var_name);
    $var_name = str_replace('[', '.', $var_name);
    $var_name = str_replace(']', '', $var_name);
@endphp

<div id="ChatPlanNameControl" class="form-group control-text {{ $errors->has($var_name) ? 'has-error' : '' }}">
    <label>
        {{ trans('messages.plan.name') }} <span class="text-danger">*</span>
    </label>
    <div class="d-flex">
        <div class=""style="width:100%">
            <div class="">

                <input
                    chat-control="input-control"
                    value="{{ $value }}"
                    type="text"
                    name="{{ $name }}"
                    class="form-control required"
                />

                @if ($errors->has($var_name))
                    <span class="help-block">
                        {{ $errors->first($var_name) }}
                    </span>
                @endif
            </div>
        </div>
        <div class="ms-2">
            <button chat-control="actions-button" type="button" class="btn btn-default px-2 rounded chatgpt-button" style="height:35px;background:rgb(16, 163, 127)!important">
                <svg style="width:20px;height:20px;fill:#fff;" xmlns="http://www.w3.org/2000/svg" width="671.194" height="680.2487" viewBox="0 0 671.194 680.2487"><path d="M626.9464,278.4037a169.4492,169.4492,0,0,0-14.5642-139.187A171.3828,171.3828,0,0,0,427.7883,56.9841,169.45,169.45,0,0,0,299.9746.0034,171.3985,171.3985,0,0,0,136.4751,118.6719,169.5077,169.5077,0,0,0,23.1574,200.8775,171.41,171.41,0,0,0,44.2385,401.845,169.4564,169.4564,0,0,0,58.8021,541.0325a171.4,171.4,0,0,0,184.5945,82.2318A169.4474,169.4474,0,0,0,371.21,680.2454,171.4,171.4,0,0,0,534.7642,561.51a169.504,169.504,0,0,0,113.3175-82.2063,171.4116,171.4116,0,0,0-21.1353-200.9ZM371.2647,635.7758a127.1077,127.1077,0,0,1-81.6027-29.5024c1.0323-.5629,2.8435-1.556,4.0237-2.2788L429.13,525.7575a22.0226,22.0226,0,0,0,11.1306-19.27V315.5368l57.25,33.0567a2.0332,2.0332,0,0,1,1.1122,1.568V508.2972A127.64,127.64,0,0,1,371.2647,635.7758ZM97.3705,518.7985a127.0536,127.0536,0,0,1-15.2074-85.4256c1.0057.6037,2.7624,1.6768,4.0231,2.4012L221.63,514.01a22.04,22.04,0,0,0,22.2492,0L409.243,418.5281v66.1134a2.0529,2.0529,0,0,1-.818,1.7568l-136.92,79.0534a127.6145,127.6145,0,0,1-174.134-46.6532ZM61.7391,223.1114a127.0146,127.0146,0,0,1,66.3545-55.8944c0,1.1667-.067,3.2329-.067,4.6665V328.3561a22.0038,22.0038,0,0,0,11.1173,19.2578l165.3629,95.4695-57.2481,33.055a2.0549,2.0549,0,0,1-1.9319.1752l-136.933-79.1215A127.6139,127.6139,0,0,1,61.7391,223.1114ZM532.0959,332.5668,366.7308,237.0854l57.25-33.0431a2.0455,2.0455,0,0,1,1.93-.1735l136.934,79.0535a127.5047,127.5047,0,0,1-19.7,230.055V351.8247a21.9961,21.9961,0,0,0-11.0489-19.2579Zm56.9793-85.7589c-1.0051-.6174-2.7618-1.6769-4.0219-2.4L449.6072,166.1712a22.07,22.07,0,0,0-22.2475,0L261.9963,261.6543V195.5409a2.0529,2.0529,0,0,1,.818-1.7567l136.9205-78.988a127.4923,127.4923,0,0,1,189.34,132.0117ZM230.8716,364.6456,173.6082,331.589a2.0321,2.0321,0,0,1-1.1122-1.57V171.8835A127.4926,127.4926,0,0,1,381.5636,73.9884c-1.0322.5633-2.83,1.5558-4.0236,2.28L242.0957,154.5044a22.0025,22.0025,0,0,0-11.1306,19.2566Zm31.0975-67.0521L335.62,255.0559l73.6488,42.51v85.0481L335.62,425.1266l-73.6506-42.5122Z"/></svg>
            </button>
        </div>
    </div>
    <div chat-control="actions" class="position-relative" style="font-family:'Inconsolata';display:none;">
        <div class="chatgpt-action-list d-inline-block mt-1" style="position:absolute;z-index:1;right:0">
            <div class="list-group bg-white shadow chatgpt-action-dropdown">
                <a chat-control="ask"
                    data-question="{{ trans('chatgpt::messages.chat.plan_name.ask_for_grammatical_correctness') }}"
                    data-show="{{ trans('chatgpt::messages.chat.plan_name.ask_for_grammatical_correctness.show') }}"
                    href="javascript:;" class="list-group-item pe-4"
                >
                    {{ trans('chatgpt::messages.chat.check_grammatical_correctness') }}
                </a>
                <a chat-control="ask"
                    data-question="{{ trans('chatgpt::messages.chat.plan_name.ask_for_more_formal') }}"
                    data-show="{{ trans('chatgpt::messages.chat.plan_name.ask_for_more_formal.show') }}"
                    href="javascript:;" class="list-group-item pe-4"
                >
                    {{ trans('chatgpt::messages.chat.suggest_more_formal') }}
                </a>
                <a chat-control="ask"
                    data-question="{{ trans('chatgpt::messages.chat.plan_name.ask_for_more_informal') }}"
                    data-show="{{ trans('chatgpt::messages.chat.plan_name.ask_for_more_informal.show') }}"
                    href="javascript:;" class="list-group-item pe-4"
                >
                    {{ trans('chatgpt::messages.chat.suggest_more_informal') }}
                </a>
                <a chat-control="custom-ask" href="javascript:;" class="list-group-item pe-4">
                    {{ trans('chatgpt::messages.chat.custom_question') }}
                </a>
            </div>
        </div>
    </div>
</div>

@include('chat._script')

<script>
    

    var ChatPlanName = {
        ChatUI: class {
            constructor(container, chatBox) {
                this.container = container;
                this.chatBox = chatBox;

                // close action list
                if (this.isActionListOpen()) {
                    this.closeActionList();
                }

                // events
                this.events();
            }

            getActionList() {
                return this.container.find('[chat-control="actions"]');
            }

            getActionButton() {
                return this.container.find('[chat-control="actions-button"]');
            }

            getAskActions() {
                return this.container.find('[chat-control="ask"]');
            }

            getCustomAskActions() {
                return this.container.find('[chat-control="custom-ask"]');
            }

            getInput() {
                return this.container.find('[chat-control="input-control"]');
            }

            getInputValue() {
                return this.getInput().val().trim();
            }

            setInputValue(text) {
                text = text.trim();

                this.getInput().val(text).change();
            }

            events() {
                var _this = this;

                // click action button
                this.getActionButton().on('click', function() {
                    if (!_this.isActionListOpen()) {
                        _this.openActionList();
                    }
                });

                // click outside to hide action list
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('[chat-control="actions"], [chat-control="actions-button"]').length) {
                        // Clicked outside the box; hide it
                        _this.closeActionList();
                    }
                });

                // insert action
                this.chatBox.getMessagesList().on('click', '[chat-control="insert"]', function() {
                    var answer = $(this).attr('data-value');
                    _this.closeChatGPTPopup();
                    _this.setInputValue(answer);
                    notify('success', '{{ trans('messages.notify.success') }}', '{{ trans('chatgpt::messages.chat.plan_name.inserted') }}' + ': "' + answer + '"');
                });

                // ask action
                this.getAskActions().on('click', function() {
                    var question = $(this).attr('data-question');
                    var questionShow = $(this).attr('data-show');

                    if (_this.getInputValue() == '') {
                        new Dialog('alert', {
                            message: '{{ trans('chatgpt::messages.chat.plan_name.empty') }}',
                        });
                        return;
                    }

                    // clear chat box
                    _this.chatBox.clear();

                    _this.openChatGPTPopup();
                    _this.askQuestionForSubject(question, questionShow);

                    // hide dropdown
                    _this.closeActionList();
                });

                // ask custom action
                this.getCustomAskActions().on('click', function() {
                    // clear chat box
                    _this.chatBox.clear();

                    _this.openChatGPTPopup();
                    if (!_this.chatBox.messages.length) {
                        var hello = '{{ trans('chatgpt::messages.chat.custom_question.default_message') }}';

                        // add hello message
                        _this.chatBox.addChatGPTMessage(hello);

                        // focus
                        _this.chatBox.getChatInput().focus();

                        // hide dropdown
                        _this.closeActionList();
                    }
                });
            }

            closeActionList() {
                this.getActionList().hide();
            }

            openActionList() {
                this.getActionList().show();
            }

            isActionListOpen() {
                this.getActionList().is(':visible');
            }

            openChatGPTPopup() {
                this.chatBox.getChatPopup().modal('show');

                fixPopupLayers();
            }

            closeChatGPTPopup() {
                this.chatBox.getChatPopup().modal('hide');
            }

            askQuestionForSubject(question, questionShow) {
                question = question.replace(':name', this.getInputValue());
                questionShow = questionShow.replace(':name', this.getInputValue());

                this.chatBox.sendUserMessage(question, questionShow);
            }
        },

        init: function() {
            this.chatUI = new this.ChatUI($('#ChatPlanNameControl'), new ChatBox('{{ action('Admin\ChatController@chat') }}'));
        }

    }

    $(function() {
        ChatPlanName.init();
    });

</script>