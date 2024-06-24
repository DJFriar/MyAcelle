<script>
    @if (null !== Session::get('orig_admin_id') && Auth::user()->admin)
        notify({
            type: 'warning',
            message: `{!! trans('messages.current_login_as', ["name" => Auth::user()->admin->displayName()]) !!}<br>{!! trans('messages.click_to_return_to_origin_user', ["link" => action("Admin\AdminController@loginBack")]) !!}`,
            timeout: false,
        });
    @endif
</script>