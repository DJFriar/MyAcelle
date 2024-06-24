<div class="wizard-sidebar">
    <ul class="wizard-steps">
        <li>
            <span class="import-tab {{ $step == 'upload' ? 'current' : '' }}">
                <span class="material-symbols-rounded">
                    upload_file
                    </span>
                <span>
                    <label>{{ trans('messages.subscriber.import.upload_csv_file') }}</label>
                    <p>{{ trans('messages.subscriber.import.upload_csv_file.intro') }}</p>
                </span>
            </span>
        </li>
        <li>
            <span class="import-tab {{ $step == 'mapping' ? 'current' : '' }}">
                <span class="material-symbols-rounded">
                    key_visualizer
                    </span>
                    
                <span>
                    <label>{{ trans('messages.subscriber.import.mapping') }}</label>
                    <p>{{ trans('messages.subscriber.import.mapping.intro') }}</p>
                </span>
            </span>
        </li>
        <li>
            <span class="import-tab {{ $step == 'review' ? 'current' : '' }}">
                <span class="material-symbols-rounded">
                    checklist
                    </span>
                <span>
                    <label>{{ trans('messages.subscriber.import.review_go') }}</label>
                    <p>{{ trans('messages.subscriber.import.review_go.intro') }}</p>
                </span>
            </span>
        </li>
    </ul>
</div>