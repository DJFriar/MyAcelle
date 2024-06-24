@php $menu = $menu ?? false @endphp

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-tabs-top nav-underline mb-1">
            <li class="nav-item {{ in_array($menu, ['overview']) ? 'active' : '' }}">
                <a href="{{ action('MailListController@overview', $list->uid) }}" class="nav-link">
                <span class="material-symbols-rounded">auto_graph</span> {{ trans('messages.overview') }}
                </a>
            </li>
            <li class="nav-item {{ in_array($menu, ['edit']) ? 'active' : '' }}">
                <a class="nav-link level-1" href="{{ action('MailListController@edit', $list->uid) }}">
                <span class="material-symbols-rounded">settings</span> {{ trans('messages.list.title.setting') }}
                </a>
            </li>
            <li class="nav-item {{ in_array($menu, ['subscriber','subscriber_add','subscriber_import','subscriber_export']) ? 'active' : '' }}">
                <a href="{{ action("AccountController@contact") }}" class="nav-link dropdown-toggle level-1" data-bs-toggle="dropdown">
                <span class="material-symbols-rounded">people</span> {{ trans('messages.subscribers') }}
                <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="nav-item {{ in_array($menu, ['subscriber']) ? 'active' : '' }}">
                        <a class="dropdown-item" href="{{ action('SubscriberController@index', $list->uid) }}">
                        <span class="material-symbols-rounded">format_list_bulleted</span> {{ trans('messages.view_all') }}
                        </a>
                    </li>
                    <li class="nav-item {{ in_array($menu, ['subscriber_add']) ? 'active' : '' }}">
                        <a class="dropdown-item" href="{{ action('SubscriberController@create', $list->uid) }}">
                        <span class="material-symbols-rounded">add</span> {{ trans('messages.add') }}
                        </a>
                    </li>
                    <li class="divider"></li>

                    @if (\Auth::user()->can('import', $list))
                    <li class="nav-item {{ in_array($menu, ['subscriber_import']) ? 'active' : '' }}">
                        <a class="dropdown-item" class="dropdown-item" href="{{ action('SubscriberController@import2', $list->uid) }}">
                        <span class="material-symbols-rounded">file_upload</span> {{ trans('messages.import') }}
                        </a>
                    </li>
                    @endif

                    @if (\Auth::user()->can('export', $list))
                    <li class="nav-item {{ in_array($menu, ['subscriber_export']) ? 'active' : '' }}">
                        <a class="dropdown-item" href="{{ action('SubscriberController@export', $list->uid) }}">
                        <span class="material-symbols-rounded">file_download</span> {{ trans('messages.export') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @if (Auth::user()->customer->can("list", new Acelle\Model\Segment()))
                <li class="nav-item {{ in_array($menu, ['segment','segment_add']) ? 'active' : '' }}">
                    <a href="{{ action("AccountController@contact") }}" class="nav-link level-1 dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="material-symbols-rounded">splitscreen</span> {{ trans('messages.segments') }}
                    <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="nav-item {{ in_array($menu, ['segment']) ? 'active' : '' }}">
                        <a class="dropdown-item" href="{{ action('SegmentController@index', $list->uid) }}">
                        <span class="material-symbols-rounded">format_list_bulleted</span> {{ trans('messages.view_all') }}
                        </a>
                        </li>
                        <li class="nav-item {{ in_array($menu, ['segment_add']) ? 'active' : '' }}">
                        <a class="dropdown-item" href="{{ action('SegmentController@create', $list->uid) }}">
                        <span class="material-symbols-rounded">add</span> {{ trans('messages.add') }}
                        </a>
                        </li>
                    </ul>
                </li>
            @endif
            <li class="nav-item {{ in_array($menu, ['embedded','page']) ? 'active' : '' }}">
                <a href="#menu" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <span class="material-symbols-rounded">web</span> {{ trans('messages.custom_forms_and_emails') }}
                <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end has-head">
                <li>
                    <a class="dropdown-item {{ in_array($menu, ['embedded']) ? 'active' : '' }}" href="{{ action('MailListController@embeddedForm', $list->uid) }}">
                    <span class="material-symbols-rounded">dashboard</span> {{ trans('messages.Embedded_form') }}
                    </a>
                </li>
                <li class="head">
                    <span class="material-symbols-rounded me-1">add_task</span> {{ trans('messages.subscribe') }}
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['sign_up_form']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'sign_up_form']) }}">
                    {{ trans('messages.sign_up_form') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['sign_up_thankyou_page']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'sign_up_thankyou_page']) }}">
                    {{ trans('messages.sign_up_thankyou_page') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['sign_up_confirmation_email']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'sign_up_confirmation_email']) }}">
                    {{ trans('messages.sign_up_confirmation_email') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['sign_up_confirmation_thankyou']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'sign_up_confirmation_thankyou']) }}">
                    {{ trans('messages.sign_up_confirmation_thankyou') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['sign_up_welcome_email']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'sign_up_welcome_email']) }}">
                    {{ trans('messages.sign_up_welcome_email') }}
                    </a>
                </li>
                <li class="head">
                    <span class="material-symbols-rounded me-1">logout</span> {{ trans('messages.unsubscribe') }}
                    </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['unsubscribe_form']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'unsubscribe_form']) }}">
                    {{ trans('messages.unsubscribe_form') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['unsubscribe_success_page']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'unsubscribe_success_page']) }}">
                    {{ trans('messages.unsubscribe_success_page') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['unsubscribe_goodbye_email']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'unsubscribe_goodbye_email']) }}">
                    {{ trans('messages.unsubscribe_goodbye_email') }}
                    </a>
                </li>
                    <li class="head">
                        <span class="material-symbols-rounded me-1">person_outline</span> {{ trans('messages.update_profile') }}
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['profile_update_email_sent']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'profile_update_email_sent']) }}">
                    {{ trans('messages.profile_update_email_sent') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['profile_update_email']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'profile_update_email']) }}">
                    {{ trans('messages.profile_update_email') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['profile_update_form']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'profile_update_form']) }}">
                    {{ trans('messages.profile_update_form') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ in_array(request()->alias, ['profile_update_success_page']) ? 'active' : '' }}" href="{{ action('PageController@update', ['list_uid' => $list->uid, 'alias' => 'profile_update_success_page']) }}">
                    {{ trans('messages.profile_update_success_page') }}
                    </a>
                </li>
                </ul>
            </li>
            <li class="nav-item {{ in_array($menu, ['field']) ? 'active' : '' }}">
                <a class="nav-link" href="{{ action('FieldController@index', $list->uid) }}">
                <span class="material-symbols-rounded">fact_check</span> {{ trans('messages.manage_list_fields') }}
                </a>
            </li>
            <li class="nav-item {{ in_array($menu, ['email_verification']) ? 'active' : '' }}">
                <a class="nav-link" href="{{ action('MailListController@verification', $list->uid) }}">
                <span class="material-symbols-rounded">mark_email_read</span> {{ trans('messages.email_verification') }}
                </a>
            </li>
        </ul>
    </div>
</div>
