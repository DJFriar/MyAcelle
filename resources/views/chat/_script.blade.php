<link href="https://fonts.googleapis.com/css?family=Inconsolata&display=swap" rel="stylesheet">

<style>
    .lds-facebook {
      display: inline-block;
      position: relative;
      width: 20px;
      height: 20px;
    }
    .lds-facebook div {
      display: inline-block;
      position: absolute;
      left: 0px;
      width: 6px;
      background: #aaa;
      animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
    }
    .lds-facebook div:nth-child(1) {
      left: 0;
      animation-delay: -0.24s;
    }
    .lds-facebook div:nth-child(2) {
      left: 10px;
      animation-delay: -0.12s;
    }
    .lds-facebook div:nth-child(3) {
      left: 20px;
      animation-delay: 0;
    }
    @keyframes lds-facebook {
      0% {
        top: 0px;
        height: 30px;
        opacity: 1;
      }
      50%, 100% {
        top: 14px;
        height: 10px;
        opacity: 0.5;
      }
    }
</style>

<script>
    var ChatBox = class {
        constructor(chatUrl) {
            this.messages = [];
            this.id = Math.random().toString(16).slice(2);

            // 
            this.chatUrl = chatUrl;

            //
            $('#' + this.id + '[chat-control="popup"]').remove();
            $('body').append(`
                <div id="`+this.id+`" chat-control="popup" class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div style="
                                background-image: url('{{ url('/images/ai-bg.svg') }}');
                                background-repeat: no-repeat;
                                background-position: center -105px;
                                background-size: 100%;
                            ">
                                
                            
                                <div class="modal-header border-bottom-0">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <p class="text-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2406 2406" style="width: 60px; height: 60px;"><path d="M1 578.4C1 259.5 259.5 1 578.4 1h1249.1c319 0 577.5 258.5 577.5 577.4V2406H578.4C259.5 2406 1 2147.5 1 1828.6V578.4z" fill="#74aa9c"/><path d="M1107.3 299.1c-198 0-373.9 127.3-435.2 315.3C544.8 640.6 434.9 720.2 370.5 833c-99.3 171.4-76.6 386.9 56.4 533.8-41.1 123.1-27 257.7 38.6 369.2 98.7 172 297.3 260.2 491.6 219.2 86.1 97 209.8 152.3 339.6 151.8 198 0 373.9-127.3 435.3-315.3 127.5-26.3 237.2-105.9 301-218.5 99.9-171.4 77.2-386.9-55.8-533.9v-.6c41.1-123.1 27-257.8-38.6-369.8-98.7-171.4-297.3-259.6-491-218.6-86.6-96.8-210.5-151.8-340.3-151.2zm0 117.5-.6.6c79.7 0 156.3 27.5 217.6 78.4-2.5 1.2-7.4 4.3-11 6.1L952.8 709.3c-18.4 10.4-29.4 30-29.4 51.4V1248l-155.1-89.4V755.8c-.1-187.1 151.6-338.9 339-339.2zm434.2 141.9c121.6-.2 234 64.5 294.7 169.8 39.2 68.6 53.9 148.8 40.4 226.5-2.5-1.8-7.3-4.3-10.4-6.1l-360.4-208.2c-18.4-10.4-41-10.4-59.4 0L1024 984.2V805.4L1372.7 604c51.3-29.7 109.5-45.4 168.8-45.5zM650 743.5v427.9c0 21.4 11 40.4 29.4 51.4l421.7 243-155.7 90L597.2 1355c-162-93.8-217.4-300.9-123.8-462.8C513.1 823.6 575.5 771 650 743.5zm807.9 106 348.8 200.8c162.5 93.7 217.6 300.6 123.8 462.8l.6.6c-39.8 68.6-102.4 121.2-176.5 148.2v-428c0-21.4-11-41-29.4-51.4l-422.3-243.7 155-89.3zM1201.7 997l177.8 102.8v205.1l-177.8 102.8-177.8-102.8v-205.1L1201.7 997zm279.5 161.6 155.1 89.4v402.2c0 187.3-152 339.2-339 339.2v-.6c-79.1 0-156.3-27.6-217-78.4 2.5-1.2 8-4.3 11-6.1l360.4-207.5c18.4-10.4 30-30 29.4-51.4l.1-486.8zM1380 1421.9v178.8l-348.8 200.8c-162.5 93.1-369.6 38-463.4-123.7h.6c-39.8-68-54-148.8-40.5-226.5 2.5 1.8 7.4 4.3 10.4 6.1l360.4 208.2c18.4 10.4 41 10.4 59.4 0l421.9-243.7z" fill="white"/></svg>
                                    </p>

                                    <h3 style="font-family:'Inconsolata';" class="text-center mb-4 mt-0 font-weight-semibold">Chat<span class="fw-bold">GPT</span></h3>

                                    <div class="">
                                        <ul chat-control="messages" class="p-0 pb-3 mb-0 border-top" style="
                                            font-family:'Inconsolata';height: calc(100vh - 330px);
                                            overflow: auto;
                                        ">
                                            
                                        </ul>
                                    </div>

                                    <div class="px-4 py-3 bg-light border-top">
                                        <div class="d-flex">
                                            <div style="width:100%;">
                                                <input chat-control="input" placeholder="{{ trans('chatgpt::messages.chat.enter_your_question_here') }}" type="text" class="border rounded px-3 py-2 form-control"
                                                    style="width:100%;height:41px;" />
                                            </div>
                                            <div class="ms-1">
                                                <button chat-control="send" type="button" class="btn btn-primary" style="height:41px;">
                                                    <span class="material-symbols-rounded">send</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // render
            this.renderChatBox();

            // check send button
            if (!this.hasContent()) {
                // disable send button
                this.disableSendButton();
            } else {
                // enable send button
                this.enableSendButton();
            }

            // events
            this.applyEvents();
        }

        getChatPopup() {
            return $('#'+this.id+'[chat-control="popup"]');
        }

        getMessagesList() {
            return this.getChatPopup().find('[chat-control="messages"]');
        }

        getChatInput() {
            return this.getChatPopup().find('[chat-control="input"]');
        }

        getSendButton() {
            return this.getChatPopup().find('[chat-control="send"]');
        }

        getInputContent() {
            return this.getChatInput().val().trim();
        }

        disableSendButton() {
            this.getSendButton().prop('disabled', true);
            this.getSendButton().addClass('disabled');
        }

        clear() {
            this.messages = [];
            this.renderChatBox();
        }

        enableSendButton() {
            this.getSendButton().prop('disabled', false);
            this.getSendButton().removeClass('disabled');
        }

        hasContent() {
            return this.getInputContent() != '';
        }

        applyEvents() {
            var _this = this;

            // typing on input
            this.getChatInput().on('keyup', function() {
                if (!_this.hasContent()) {
                    // disable send button
                    _this.disableSendButton();
                } else {
                    // enable send button
                    _this.enableSendButton();
                }
            });

            // click send button
            this.getSendButton().on('click', function() {
                var content = _this.getInputContent();

                _this.sendUserMessage(content);
            });

            // prevent Enter
            this.getChatInput().on("keydown", function(event) {
                if (event.key == "Enter") {
                    var content = _this.getInputContent();

                    _this.sendUserMessage(content);

                    return false;
                }
            });
        }

        renderChatBox() {
            var _this = this;
            this.getMessagesList().html('');

            this.messages.forEach(function(message, index) {
                var content;

                // check if message has show value
                if (typeof(message.show) !== 'undefined') {
                    content = message.show;
                } else {
                    content = message.content;
                }

                if (message.role == 'assistant') {
                    _this.appendChatGPTMessage(content);
                }

                else if (message.role == 'user') {
                    _this.appendUserMessage(content);
                }

                else if (message.role == 'error') {
                    _this.appendErrorMessage(content);
                }
            });

            // scroll to bottom
            _this.scrollToBottom();
        }

        chat(callback) {
            var _this = this;

            this.setSending();

            // filter messages for chatGPT before send
            var sendMessages = this.messages.map(function(message) {
                return {
                    role: message.role,
                    content: message.content
                }
            });

            $.ajax({
                url : _this.chatUrl,
                type: "POST",
                globalError: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    messages: sendMessages,
                },
            }).done(function(result, textStatus, jqXHR) {
                _this.addMessage(result);
                _this.renderChatBox();

                //
                _this.setSendDone();

                // 
                if (typeof(callback) !== 'undefined') {
                    callback();
                }
            }).fail(function(res) {
                _this.renderChatBox();

                // show error
                _this.appendErrorMessage(res.responseText);

                // scroll
                _this.scrollToBottom();

                //
                _this.setSendDone();

                // 
                if (typeof(callback) !== 'undefined') {
                    callback();
                }
            });
        }

        appendUserMessage(message) {
            // filter user message
            message = this.filterUserMessage(message);

            // append message to list
            this.getMessagesList().append(`
                <li class="px-4 py-3 border-bottom bg-white" style="list-style:none;">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="d-flex align-items-center border" style="background: #fff; border-radius: 100%;overflow:hidden;">
                                <img style="width:32px;height:32px;" class="" src="{{ Auth::user()->getProfileImageUrl() }}"
                                    style="border-radius:100%"
                                    class="menu-user-avatar" alt="">
                            </div>
                        </div>
                        <div class="">
                            `+ message +`
                        </div>
                    </div>
                </li>
            `)
        }

        filterGPTMessage(message) {
            message = this.addInsertActionLinks(message);

            return message;
        }

        appendChatGPTMessage(message) {
            // filter message; add control
            message = this.filterGPTMessage(message);

            // append message to list
            var element = $(`
                <li class="bg-light px-4 py-3 border-bottom" style="list-style:none;position:relative;">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="d-flex align-items-center" style="padding: 7px; background: rgb(16, 163, 127); border-radius: 100%;">
                                <svg style="fill: #fff;color:#fff;" width="20" height="20" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" class="h-6 w-6"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>
                            </div>
                        </div>
                        <div class="">
                            <div>`+ message +`</div>
                            <div chat-control="message-action"></div>
                        </div>
                    </div>
                </li>
            `);
            
            // append element
            this.getMessagesList().append(element);

            // init js
            initJs(element);
        }

        addInsertActionLinks(message) {
            console.log(message);
            // find answers
            const text = message;
            const regex = /#([^#]+)\#/g;
            const answers = [];
            let match;

            // inside ##
            while (match = regex.exec(text)) {
                answers.push(match[1]);
            }

            // // if empty get all response
            // if (answers.length == 0 && message.split(" ").length < 20) {
            //     answers.push(message.trim());
            // }

            // console.log(answers);

            // replace links
            answers.forEach(function(answer) {
                answer = answer.replace('"', '\"');
                message = message.replace('#'+answer+'#', `
                    <a title="{{ trans('chatgpt::messages.chat.insert_answer') }} `+answer+`" chat-control="insert" data-value="`+answer+`" href="javascript:;"
                        class="xtooltip"
                    >
                        `+answer.trim()+` <span class="material-symbols-rounded me-0">library_add_check</span>
                    </a>
                `);
            });

            return message;
        }

        appendErrorMessage(message) {
            // append message to list
            this.getMessagesList().append(`
                <li class="bg-light px-4 py-3 border-bottom" style="list-style:none;position:relative">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="d-flex align-items-center bg-danger" style="font-size:16px;border-radius: 100%;width:32px;height:32px;justify-content:center;">
                                <span class="material-symbols-rounded text-white">sms_failed</span>
                            </div>
                        </div>
                        <div class="">
                            `+ message +`
                        </div>
                    </div>
                </li>
            `)
        }

        filterUserMessage(message) {
            return message;
        }

        sendUserMessage(content, contentShow) {
            var _this = this;

            this.addUserMessage(content, contentShow);
            this.chat(function() {
                _this.clearInput();
                _this.getChatInput().focus();
            });
        }

        setSending() {
            // 
            this.getMessagesList().append(`
                <li class="bg-light px-4 py-3 border-bottom" style="list-style:none;position:relative;">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="d-flex align-items-center" style="padding: 7px; background: rgb(16, 163, 127); border-radius: 100%;">
                                <svg style="fill: #fff;color:#fff;" width="20" height="20" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" class="h-6 w-6"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>
                            </div>
                        </div>
                        <div class="">
                            <div class="lds-facebook"><div></div><div></div><div></div></div>
                        </div>
                    </div>
                </li>
            `);

            // disable input
            this.getChatInput().prop('disabled', true);

            // disable send button
            this.disableSendButton();

            // scroll to bottom
            this.scrollToBottom();
        }

        setSendDone() {
            // disable input
            this.getChatInput().prop('disabled', false);

            // enable send button
            if (this.hasContent()) {
                // enable send button
                this.enableSendButton();
            }
        }

        addUserMessage(content, contentShow) {
            var message = {
                role: "user",
                content: content,
            };

            if (typeof(contentShow) !== 'undefined') {
                message.show = contentShow;
            }

            this.addMessage(message);

            // render chat box
            this.renderChatBox();
        }

        addChatGPTMessage(content) {
            this.addMessage({
                role: "assistant",
                content: content,
            });

            // render chat box
            this.renderChatBox();
        }

        addMessage(message) {
            this.messages.push(message);
        }

        clearInput() {
            this.getChatInput().val('');

            if (!this.hasContent()) {
                // disable send button
                this.disableSendButton();
            } else {
                // enable send button
                this.enableSendButton();
            }
        }

        scrollToBottom() {
            this.getMessagesList().scrollTop(this.getMessagesList()[0].scrollHeight);
        }
    }
</script>