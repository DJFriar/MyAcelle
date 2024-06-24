<div class="mb-20">
    <input type="hidden" name="options[type]" value="datetime" />
    
    @php
        $customer = Auth::user()->customer;
        $date = $trigger->getOption('date') ? \Carbon\Carbon::createFromFormat(config('custom.date_format'), $trigger->getOption('date'))
            ->timezone(Auth::user()->customer->timezone)
            ->format('Y-m-d') : '';
        if (!$date) {            
            $date = $customer->getCurrentTime()->format('Y-m-d');
        }
        
        $time = $trigger->getOption('at') ? \Carbon\Carbon::createFromFormat(config('custom.time_format'), $trigger->getOption('at'))
            ->timezone(Auth::user()->customer->timezone)
            ->format('H:i') : '';
        if (!$time) {
            $time = $customer->getCurrentTime()->format('H:i');
        }
    @endphp

    @include('helpers.form_control', [
        'type' => 'date2',
        'class' => '',
        'label' => trans('messages.automation.date'),
        'name' => 'options[date]',
        'value' => $date,
        'help_class' => 'trigger',
        'rules' => $rules,
    ])
    
    @include('helpers.form_control', [
        'type' => 'time2',
        'name' => 'options[at]',
        'label' => trans('messages.automation.at'),
        'value' => $time,
        'rules' => $rules,
        'help_class' => 'trigger'
    ])
</div>