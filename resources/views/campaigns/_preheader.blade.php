<div id="PreheaderBox" class="sub-section">   
    
</div>

<script>
    var Campaign_Preheader = class {
        constructor() {
            this.box = new Box($('#PreheaderBox'), '{{ action('CampaignController@preheader', $campaign->uid) }}');
            this.box.load();
        }
    }

    $(function() {
        campaign_Preheader = new Campaign_Preheader();
    });
</script>