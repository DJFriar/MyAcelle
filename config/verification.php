<?php

return [
    'services' => [
        [
            'id' => 'emailable.com',
            'name' => 'Emailable (recommended)',
            'uri' => 'https://api.emailable.com/v1/verify?email={EMAIL}&api_key={API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.state',
            'result_map' => [ 'deliverable' => 'deliverable', 'undeliverable' => 'undeliverable', 'risky' => 'risky', 'unknown' => 'unknown' ]
        ],[
            'id' => 'kickbox.io',
            'name' => 'Kickbox IO',
            'uri' => 'https://api.kickbox.io/v2/verify?email={EMAIL}&apikey={API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.result',
            'result_map' => [ 'deliverable' => 'deliverable', 'undeliverable' => 'undeliverable', 'risky' => 'risky', 'unknown' => 'unknown' ]
        ], [
            'id' => 'zerobounce.net',
            'name' => 'Zero Bounce',
            'uri' => 'https://api.zerobounce.net/v2/validate?api_key={API_KEY}&email={EMAIL}&ip_address=',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.status',
            'result_map' => [
                'valid' => 'deliverable',
                'Valid' => 'deliverable',
                'invalid' => 'undeliverable',
                'Invalid' => 'undeliverable',
                'unknown' => 'unknown',
                'Unknown' => 'unknown',
                'abuse' => 'undeliverable',
                'Abuse' => 'undeliverable',
                'spamtrap' => 'undeliverable',
                'Spamtrap' => 'undeliverable',
                'catch-all' => 'deliverable',
                'Catch-all' => 'deliverable',
                'Catch-All' => 'deliverable',
                'do_not_mail' => 'undeliverable',
                'Do_not_mail' => 'undeliverable',
                'Do_Not_Mail' => 'undeliverable',
            ]
        ],
        [
            'id' => 'verify-email.org',
            'name' => 'VerifyEmail ORG',
            'uri' => 'https://app.verify-email.org/api/v1/{API_KEY}/verify/{EMAIL}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.status',
            'result_map' => [ '1' => 'deliverable', '0' => 'undeliverable', '-1' => 'unknown' ]
        ], [
            'id' => 'localmail.io',
            'name' => 'Localmail IO',
            'uri' => 'https://api.localmail.io/v1/mail/verify?key={API_KEY}&email={EMAIL}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.result',
            'result_map' => [ 'deliverable' => 'deliverable', 'unknown' => 'unknown', 'risky' => 'risky', 'undeliverable' => 'undeliverable' ]
        ], [
            'id' => 'debounce.io',
            'name' => 'Debounce IO',
            'uri' => 'https://api.debounce.io/v1/?api={API_KEY}&email={EMAIL}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.debounce.result',
            'result_map' => [ 'Safe to Send' => 'deliverable', 'Unknown' => 'unknown', 'Risky' => 'risky', 'Invalid' => 'undeliverable' ]
        ], [
            'id' => 'emailchecker.com',
            'name' => 'EmailChecker',
            'uri' => 'https://api.emailverifyapi.com/v3/lookups/json?email={EMAIL}&key={API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.deliverable',
            'result_map' => [ 'true' => 'deliverable', 'false' => 'undeliverable' ]
        ],[
            'id' => 'cloudvision.io',
            'name' => 'Cloud Vision',
            'uri' => 'https://dev-marketing.cloudvision.io/api/v1/verify?email={EMAIL}&api_token={API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.result',
            'result_map' => [ 'deliverable' => 'deliverable', 'undeliverable' => 'undeliverable' ]
        ],[
            'id' => 'cloudmersive.com',
            'name' => 'Cloudmersive',
            'uri' => 'https://api.cloudmersive.com/validate/email/address/full',
            'request_type' => 'POST',
            'post_data' => '{EMAIL}',
            'post_headers' => [ 'Content-Type' => 'application/json', "Apikey" => "{API_KEY}" ],
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.ValidAddress',
            'result_map' => [ 'true' => 'deliverable', 'false' => 'undeliverable' ]
        ],[
            'id' => 'emaillistvalidation.com',
            'name' => 'Emaillist Validation',
            'uri' => 'https://app.emaillistvalidation.com/api/verifEmail?secret={API_KEY}&email={EMAIL}',
            'request_type' => 'GET',
            'response_type' => 'plain',
            'fields' => [ 'api_key' ],
            'result_map' => [
                'ok' => 'deliverable',
                'ok_for_all' => 'deliverable',
                'ok_for_all | ok_for_all' => 'deliverable',
                'ok_for_all|ok_for_all' => 'deliverable',
                'email_disabled' => 'undeliverable',
                'risky' => 'risky',
                'unknown' => 'unknown'
            ]
        ],[
            'id' => 'bounceless.io',
            'name' => 'Bounceless.io',
            'uri' => 'https://apps.bounceless.io/api/singlemaildetails?secret={API_KEY}&email={EMAIL}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.result',
            'result_map' => [
                'valid' => 'deliverable',
                'unknown' => 'unknown',
                'invalid' => 'undeliverable',
                'risky' => 'risky',
            ]
        ], [
            'id' => 'bouncify.io',
            'name' => 'Bouncify',
            'uri' => 'https://api.bouncify.io/v1/verify?apikey={API_KEY}&email={EMAIL}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.result',
            'result_map' => [
                'deliverable' => 'deliverable',
                'unknown' => 'unknown',
                'accept-all' => 'unknown', // Acelle-All means the domain's email addresses cannot be verified.
                'accept all' => 'unknown', // Acelle-All means the domain's email addresses cannot be verified.
                'undeliverable' => 'undeliverable',
            ]
        ], [
            'id' => 'myemailverifier.com',
            'name' => 'myEmailVerifier',
            'uri' => 'https://client.myemailverifier.com/verifier/validate_single/{EMAIL}/{API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.Status',
            'result_map' => [
                'Valid' => 'deliverable',
                'Unknown' => 'unknown',
                'Invalid' => 'undeliverable',
            ]
        ]

    ]
];
