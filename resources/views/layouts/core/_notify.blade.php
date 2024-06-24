<script>
    @if (null !== Session::get('orig_admin_id') && Auth::user()->admin)
        notify({
            type: 'warning',
            message: `{!! trans('messages.site_is_offline') !!}`,
            timeout: false,
        });
    @endif
</script>