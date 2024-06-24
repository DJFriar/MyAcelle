<h2 class="mt-0 mb-3">{{ trans('messages.preheader') }}</h2>

@if ($campaign->preheader)
    <div class="p-3 border bg-light rounded-3">
        <div class="d-flex align-items-top">
            <div class="pe-4">
                {{ $campaign->preheader }}
            </div>
            <div class="ms-auto">
                <a id="PreheaderEditButton" href="javascript:;" class="btn btn-default me-1">
                    <span class="material-symbols-rounded">edit</span>
                    {{ trans('messages.preheader.edit') }}
                </a>
                <a id="PreheaderRemoveButton" href="javascript:;" class="btn btn-danger">
                    <span class="material-symbols-rounded">delete</span>
                    {{ trans('messages.preheader.remove') }}
                </a>
            </div>
        </div>
    </div>
@else
    <p>{{ trans('messages.preheader.intro') }}</p>
        
    <div class="mt-4">
        <a id="PreheaderAddPopupButton" href="javascript:;" class="btn btn-default">
            <span class="material-symbols-rounded">add</span>
            {{ trans('messages.preheader.add') }}
        </a>
    </div>
@endif

<script>
    var CampaignPreheader = class {

        constructor() {
            this.addPopup = new Popup({
                url: '{{ action('CampaignController@preheaderAdd', $campaign->uid) }}'
            });
        }

        remove() {
            new Dialog('confirm', {
                message: '{{ trans('messages.preheader.remove.confirm') }}',
                ok: function() {
                    addMaskLoading();
                    $.ajax({
                        method: "POST",
                        url: '{{ action('CampaignController@preheaderRemove', $campaign->uid) }}',
                        data: {
                            _token: CSRF_TOKEN
                        }
                    })
                    .done(function( response ) {
                        removeMaskLoading();

                        // notify
                        notify(response.status, '{{ trans('messages.notify.success') }}', response.message);

                        campaign_Preheader.box.load();
                    });   
                }
            });
                      
        }
    }

    $(function() {
        campaignPreheader = new CampaignPreheader();

        $('#PreheaderAddPopupButton').on('click', function() {
            campaignPreheader.addPopup.load();
        });

        $('#PreheaderEditButton').on('click', function() {
            campaignPreheader.addPopup.load();
        });
        
        $('#PreheaderRemoveButton').on('click', function() {
            campaignPreheader.remove();
        });
    });
</script>