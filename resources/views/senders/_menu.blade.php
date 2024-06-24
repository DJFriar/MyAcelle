@php $menu = $menu ?? false @endphp

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-tabs-top nav-underline">
            <!-- always hide
            <li class="nav-item {{ in_array($menu, ['sender']) ? 'active' : '' }}">
                <a href="{{ action('SenderController@index') }}" class="nav-link">
                <span class="material-symbols-rounded">mail_outline</span> {{ trans('messages.email_addresses') }}
                </a>
            </li>
            -->

            <!-- always show sending domains -->
            <li class="nav-item {{ in_array($menu, ['sending_domain']) ? 'active' : '' }}">
                <a href="{{ action('SendingDomainController@index') }}" class="nav-link">
                    <span class="material-symbols-rounded">alternate_email</span> {{ trans('messages.domains') }}
                </a>
            </li>
        </ul>
    </div>
</div>
