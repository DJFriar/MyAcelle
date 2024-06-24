@include('chat._script')

<script>
    var ChatBuilderPro = {
        init: function() {
            this.chatBox = new ChatBox('{{ action('ChatController@chat') }}');
            parent.TemplateBuilderEdit.chatUI.afterFrameInit();

            // events
            // insert action
            this.chatBox.getMessagesList().on('click', '[chat-control="insert"]', function() {
                var answer = $(this).attr('data-value');
                parent.TemplateBuilderEdit.chatUI.insertAnswer(answer);
            });
        },

        openChatPopup: function() {
            this.chatBox.getChatPopup().modal('show');
            fixPopupLayers();
        },

        askQuestion: function(question, questionShow) {
            // show popup
            this.openChatPopup();

            // clear chat box
            this.chatBox.clear();
            this.chatBox.sendUserMessage(question, questionShow);
        },

        openChatHello: function() {
            // show popup
            this.openChatPopup();

            // clear chat box
            this.chatBox.clear();

            // send hello message
            var hello = '{{ trans('chatgpt::messages.chat.custom_question.default_message') }}';
            this.chatBox.addChatGPTMessage(hello);
        }
    }

    $(function() {
        ChatBuilderPro.init();
    })
</script>