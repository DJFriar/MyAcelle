<div id="PreheaderBox" class="sub-section">   
    
</div>

<script>
    var Email_Preheader = class {
        constructor() {
            this.box = new Box($('#PreheaderBox'), '{{ action('Automation2Controller@emailPreheader', [
                'uid' => $automation->uid,
                'email_uid' => $email->uid,
            ]) }}');
            this.box.load();
        }
    }

    $(function() {
        email_Preheader = new Email_Preheader();
    });
</script>