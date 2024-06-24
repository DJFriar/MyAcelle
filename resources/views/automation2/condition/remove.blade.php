@extends('layouts.popup.small')

@section('content')
    <div class="">
       <p>{{ trans('messages.automation.condition.remove.choose_which_keep') }}</p>

       <div class="mt-3">
            <a id="moveYesBranchUp" href="javascript:;" class="btn btn-light mb-2 me-1">{{ trans('messages.automation.condition.remove.keep_yes') }}</a>
            <a id="moveNoBranchUp" href="javascript:;" class="btn btn-light mb-2 me-1">{{ trans('messages.automation.condition.remove.keep_no') }}</a>
            <a id="deleteAllChildren" href="javascript:;" class="btn btn-danger mb-2">{{ trans('messages.automation.condition.remove.delete_all_children') }}</a>
       </div>
    </div>

    <script>
        var AutomationConditionRemove = {
            moveYesBranchUp: function() {
                var dialog = new Dialog('confirm', {
                    message: '{{ trans('messages.automation.condition.remove.keep_yes.confirm') }}',
                    ok: function(dialog) {
                        // remove current node
                        tree.getSelected().detachAndPromoteYesBranch();

                        // hide popup
                        AutomationActionEdit.deleteConditionPopup.hide();
                        
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

            moveNoBranchUp: function() {
                var dialog = new Dialog('confirm', {
                    message: '{{ trans('messages.automation.condition.remove.keep_no.confirm') }}',
                    ok: function(dialog) {
                        // remove current node
                        tree.getSelected().detachAndPromoteNoBranch();

                        // hide popup
                        AutomationActionEdit.deleteConditionPopup.hide();
                        
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

            deleteAllChildren: function() {
                var dialog = new Dialog('confirm', {
                    message: '{{ trans('messages.automation.condition.remove.delete_all_children.confirm') }}',
                    ok: function(dialog) {
                        // remove current node
                        tree.getSelected().remove();

                        // hide popup
                        AutomationActionEdit.deleteConditionPopup.hide();
                        
                        // save tree
                        saveData(function() {
                            // notify
                            notify('success', '{{ trans('messages.notify.success') }}', '{{ trans('messages.automation.action.deteled') }}');
                            
                            // load default sidebar
                            sidebar.load('{{ action('Automation2Controller@settings', $automation->uid) }}');
                        });
                    },
                });
            }
        }

        $(function() {
            $('#moveYesBranchUp').on('click', function() {
                AutomationConditionRemove.moveYesBranchUp();
            });

            $('#moveNoBranchUp').on('click', function() {
                AutomationConditionRemove.moveNoBranchUp();
            });

            $('#deleteAllChildren').on('click', function() {
                AutomationConditionRemove.deleteAllChildren();
            });
        });
    </script>

@endsection
