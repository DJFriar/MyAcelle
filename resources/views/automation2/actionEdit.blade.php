@include('automation2._back')

<form id="ActionEditForm" class="action-edit" action="{{ action("Automation2Controller@actionEdit", ['uid' => $automation->uid, 'key' => $key]) }}" method="POST" class="form-validate-jqueryz">
    {{ csrf_field() }}
    
    <input type="hidden" name="key" value="{{ $key }}" />
    
    @if(View::exists('automation2.action.' . $key))
        @include('automation2.action.' . $key)
    @endif
    
    <div class="trigger-action mt-2">    
        <button class="btn btn-secondary action-save-change mr-1 mt-2"
            data-url="{{ action('Automation2Controller@triggerSelect', ['uid' => $automation->uid, 'key' => $key]) }}"
        >
                {{ trans('messages.automation.action.save_change') }}
        </button>
    </div>
</form>

<div class="mt-4 d-flex py-3">
    <div>
        <h4 class="mb-2">
            {{ trans('messages.automation.dangerous_zone') }}
        </h4>
        <p class="">
            {{ trans('messages.automation.action.delete.wording') }}                
        </p>
        <div class="mt-3">
            @if ($element->get('type') == 'ElementCondition')
                <a href="javascript:;" class="btn btn-secondary condition-delete">
                    <span class="material-symbols-rounded">delete</span> {{ trans('messages.automation.remove_this_action') }}
                </a>
            @else
                <a href="javascript:;" class="btn btn-secondary action-delete">
                    <span class="material-symbols-rounded">delete</span> {{ trans('messages.automation.remove_this_action') }}
                </a>
            @endif
        </div>
    </div>
</div>
    
<script>
    var AutomationActionEdit = {
        submit: function() {
            var form = $('#ActionEditForm');
            var data = form.serialize();
            var url = form.attr('action');

            sidebar.loading();
        
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
            }).always(function(response) {
                // set node title
                tree.getSelected().setTitle(response.title);
                // merge options with reponse options
                tree.getSelected().setOptions($.extend(tree.getSelected().getOptions(), response.options));
                tree.getSelected().setOptions($.extend(tree.getSelected().getOptions(), {init: true}));
                tree.getSelected().validate();
                // save tree
                saveData(function() {
                    // notify
                    notify({
                        type: 'success',
                        title: '{!! trans('messages.notify.success') !!}',
                        message: response.message
                    });

                    // reload sidebar
                    sidebar.load();
                });
            });
        },

        deleteAction: function() {
            var confirm = '{{ trans('messages.automation.action.delete.confirm') }}';

            var dialog = new Dialog('confirm', {
                message: confirm,
                ok: function(dialog) {
                    // remove current node
                    tree.getSelected().detach();
                    
                    // save tree
                    saveData(function() {
                        // notify
                        notify('success', '{{ trans('messages.notify.success') }}', '{{ trans('messages.automation.action.deteled') }}');
                        
                        // load default sidebar
                        sidebar.load('{{ action('Automation2Controller@settings', $automation->uid) }}');
                    });
                },
            });
        },

        deleteCondition: function() {
            this.deleteConditionPopup = new Popup({
                url: '{{ action('Automation2Controller@conditionRemove', $automation->uid) }}',
            });
            this.deleteConditionPopup.load();
        }
    }

    $(function() {
        $('.action-edit').submit(function(e) {
            e.preventDefault();
            
            AutomationActionEdit.submit();
        });

        $('.action-delete').click(function(e) {
            e.preventDefault();
            
            AutomationActionEdit.deleteAction();
        });

        $('.condition-delete').click(function(e) {
            e.preventDefault();
            
            AutomationActionEdit.deleteCondition();
        });
    });
        
        
        
</script>
