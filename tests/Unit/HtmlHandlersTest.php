<?php

namespace Tests\Unit;

use Tests\TestCase;
use League\Pipeline\PipelineBuilder;
use Acelle\Library\HtmlHandler\ParseRss;
use Acelle\Library\HtmlHandler\ReplaceBareLineFeed;
use Acelle\Library\HtmlHandler\AppendHtml;
use Acelle\Library\HtmlHandler\TransformTag;
use Acelle\Library\HtmlHandler\InjectTrackingPixel;
use Acelle\Library\HtmlHandler\MakeInlineCss;
use Acelle\Library\HtmlHandler\TransformUrl;
use Acelle\Library\HtmlHandler\AddDoctype;
use Acelle\Library\HtmlHandler\RemoveTitleTag;
use Acelle\Library\HtmlHandler\AddPreheader;
use Acelle\Library\HtmlHandler\GenerateSpintax;
use Acelle\Library\HtmlHandler\InjectMessageIdToBody;
use Acelle\Model\Subscriber;
use Acelle\Model\Campaign;
use Acelle\Model\MailList;
use Acelle\Model\Template;
use Acelle\Model\TrackingDomain;
use Acelle\Model\Customer;
use Mockery;
use Exception;
use DOMDocument;
use Acelle\Library\StringHelper;

class HtmlHandlersTest extends TestCase
{
    public function test_parse_rss()
    {
        $pipeline = new PipelineBuilder();
        $pipeline->add(new ParseRss());
        $html = "{% set rss = rss('http://rss.cnn.com/rss/edition.rss', 5) %}
            {% for post in rss.item %}
                <h3>{{ post.title }}</h3>
                <p>{{ post.description | raw }}</p>
                <hr>
            {% endfor %}";

        $out = $pipeline->build()->process($html);

        $this->assertTrue(strpos($out, '{% for post in rss.item %}') === false);
        $this->assertTrue(strpos($out, '<h3>{{ post.title }}</h3>') === false);
    }

    public function test_replace_bare_line_feed()
    {
        $pipeline = new PipelineBuilder();
        $pipeline->add(new ReplaceBareLineFeed());
        $html = "Hello\nWorld\nFrom\r\nLaravel\n";
        $out = $pipeline->build()->process($html);

        // Notice that message should end with an "\r\n" character as suggested by RFC2822
        $this->assertEquals($out, "Hello\r\nWorld\r\nFrom\r\nLaravel\r\n");
    }

    public function test_append_html()
    {
        $pipeline = new PipelineBuilder();
        $pipeline->add(new AppendHtml('<a>click me</a>'));
        $pipeline->add(new AppendHtml('<p>read me</p>'));

        $html = "<html><body><div>Content main</div></body></html>";
        $out = $pipeline->build()->process($html);

        $this->assertEquals($out, '<!DOCTYPE html><html><body><div>Content main</div><a>click me</a><p>read me</p></body></html>');
    }

    public function test_add_preheader()
    {
        $preheader = 'Hello world';
        $pipeline = new PipelineBuilder();
        $pipeline->add(new AddPreheader('Hello world'));

        $html = "<html><body><div>Content main</div></body></html>";
        $out = $pipeline->build()->process($html);

        $this->assertEquals($out, "<!DOCTYPE html><html><body><div style=\"display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden\">{$preheader}</div><div>Content main</div></body></html>");
    }

    public function test_transform_tag()
    {
        // List
        $list = Mockery::mock(new MailList());

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;
        $campaign->shouldReceive('isStdClassSubscriber')->andReturn(true);

        // Subscriber
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->uid = '000000';

        // Pipe
        $pipeline = new PipelineBuilder();
        $pipeline->add(new TransformTag($campaign, $subscriber, $msgId = 'TEST'));
        $pipeline->add(new AppendHtml('<a>click me</a>'));
        $pipeline->add(new AppendHtml('<p>read me</p>'));

        $html = "<html><body><div>Campaign: '{CAMPAIGN_NAME}' Subscriber: '{SUBSCRIBER_UID}'</div></body></html>";
        $out = $pipeline->build()->process($html);

        $this->assertEquals($out, "<!DOCTYPE html><html><body><div>Campaign: 'Campaign' Subscriber: '%UID%'</div><a>click me</a><p>read me</p></body></html>");
    }

    public function test_inject_tracking_pixel()
    {
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));

        $pipeline = new PipelineBuilder();
        $pipeline->add(new InjectTrackingPixel($campaign, 'MSGID'));

        $html = "<html><body><div>Content main</div></body></html>";
        $out = $pipeline->build()->process($html);

        // Something like: https://localhost/p/TVNHSUQ/open
        // With StringHelper::base64UrlEncode('MSGID') ==> "TVNHSUQ"
        $openTrackingUrl = route('openTrackingUrl', ['message_id' => StringHelper::base64UrlEncode('MSGID')], true);

        $this->assertEquals($out, '<!DOCTYPE html><html><body><div>Content main</div><img alt="This is a tracking pixel" src="'.$openTrackingUrl.'" width="0" height="0" style="visibility:hidden"></body></html>');
    }

    public function test_make_inline_css()
    {
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));

        $pipeline = new PipelineBuilder();
        $pipeline->add(new MakeInlineCss([storage_path('tests/sample.css')]));

        $html = "<html><body><div class='main'><a>Content main</a><span class='big blue '>Test<span></div></body></html>";
        $out = $pipeline->build()->process($html);

        $this->assertEquals($out, '<!DOCTYPE html><html><body><div class="main"><a style="color:green">Content main</a><span class="big blue " style="font-weight:600;color:blue">Test<span></span></span></div></body></html>');
    }

    public function test_test_campaign_html_content()
    {
        // Template
        $template = Mockery::mock(new Template(['name' => 'Template']));
        $template->generateUid();
        $msgId = 'TEST';

        // List
        $list = Mockery::mock(new MailList());

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;
        $campaign->shouldReceive('isStdClassSubscriber')->andReturn(true);

        // Subscriber
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->uid = '000000';

        $pipeline = new PipelineBuilder();
        $pipeline->add(new AddDoctype());
        $pipeline->add(new RemoveTitleTag());
        $pipeline->add(new ReplaceBareLineFeed());
        $pipeline->add(new AppendHtml('<div>Hello world éèéêôâ</div>'));
        $pipeline->add(new ParseRss());
        $pipeline->add(new TransformTag($campaign, $subscriber, 'MSGID', $server = null));
        $pipeline->add(new TransformUrl($template, 'MSGID', $trackingDomain = null));
        $pipeline->add(new MakeInlineCss([storage_path('tests/sample.css')]));
        $pipeline->add(new InjectTrackingPixel($campaign, 'MSGID'));

        $html = $pipeline->build()->process("<title class='blue'>\nThisis the template title</title><a class=' big blue' href='https://mailchimp.com'></a><div>Campaign: '{CAMPAIGN_NAME}' Subscriber: '{SUBSCRIBER_UID}'</div>");

        $this->assertTrue(strpos($html, '<!DOCTYPE html>') === 0);
        $this->assertTrue(strpos($html, '<title>') === false);
        $this->assertTrue(strpos($html, "\n") === false);
        $this->assertTrue(strpos($html, '<div>Hello world éèéêôâ</div>') !== false);
        $this->assertTrue(strpos($html, "'%UID%'") !== false); // isStdClassSubscriber is always TRUE in test
    }

    public function test_test_campaign_html_with_types_of_urls()
    {
        // Template
        $template = Mockery::mock(new Template(['name' => 'Template']));
        $template->generateUid();
        $msgId = 'TEST';

        // List
        $list = Mockery::mock(new MailList());

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;
        $campaign->shouldReceive('isStdClassSubscriber')->andReturn(true);

        // Subscriber
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->uid = '000000';

        $pipeline = new PipelineBuilder();
        $pipeline->add(new AddDoctype());
        $pipeline->add(new RemoveTitleTag());
        $pipeline->add(new ReplaceBareLineFeed());
        $pipeline->add(new AppendHtml('<div>test</div>'));
        $pipeline->add(new ParseRss());
        $pipeline->add(new TransformTag($campaign, $subscriber, 'MSGID', $server = null));
        $pipeline->add(new TransformUrl($template, 'MSGID', $trackingDomain = null));
        $pipeline->add(new MakeInlineCss([storage_path('tests/sample.css')]));
        $pipeline->add(new InjectTrackingPixel($campaign, 'MSGID'));

        $html = $pipeline->build()->process("<a href='image/hehe'>Relative</a><img src='//cdn.fake.com/image/hehe'><a href='/absolute/url'>Absolute</a><a href='//absolute/url'>Two slashes</a><a href='mailto:hello@example.com'>hello@example.com</a><a href='#sectionN'>Scroll to Section N</a>");

        $this->assertTrue(strpos($html, '<!DOCTYPE html>') === 0);

        // mailto:hello@example.com should not be modified
        $this->assertTrue(strpos($html, '<a href="mailto:hello@example.com">hello@example.com</a>') !== false);
        $this->assertTrue(strpos($html, '<a href="#sectionN">Scroll to Section N</a>') !== false);
    }

    public function test_test_campaign_html_content_with_tracking_domain()
    {
        // Tracking domain
        $domain = new TrackingDomain();
        $domain->name = 'track.example.com';
        $domain->scheme = 'https';
        $domain->verification_method = TrackingDomain::VERIFICATION_METHOD_HOST;

        // Template
        $html = "<html><body><a href='/hello/world'></body></html>";
        $template = Mockery::mock(new Template(['name' => 'Template']));
        $template->generateUid();

        // List
        $list = Mockery::mock(new MailList());

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;
        $campaign->shouldReceive('isStdClassSubscriber')->andReturn(true);

        // Subscriber
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->uid = '000000';

        $pipeline = new PipelineBuilder();
        $pipeline->add(new AddDoctype());
        $pipeline->add(new RemoveTitleTag());
        $pipeline->add(new ReplaceBareLineFeed());
        $pipeline->add(new AppendHtml('<div>Hello world éèéêôâ</div>'));
        $pipeline->add(new ParseRss());
        $pipeline->add(new TransformTag($campaign, $subscriber, 'MSGID', $server = null));

        $pipeline->add(new TransformUrl($template, 'MSGID', $domain));
        $pipeline->add(new MakeInlineCss([storage_path('tests/sample.css')]));
        $pipeline->add(new InjectTrackingPixel($campaign, 'MSGID'));

        $html = $pipeline->build()->process("<title class='blue'>\nThisis the template title</title><a class=' big blue' href='https://mailchimp.com'></a><div>Campaign: '{CAMPAIGN_NAME}' Subscriber: '{SUBSCRIBER_UID}'</div>");

        // Examine the output with DOM
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $a = $dom->getElementsByTagName('a')[0];
        $url = $a->getAttribute('href');

        // At first, URL is a tracking URL
        // Something like: https://localhost/p/aHR0cHM6Ly9tYWlsY2hpbXAuY29t/click/TVNHSUQ
        $trackableUrl = StringHelper::makeTrackableLink('https://mailchimp.com', 'MSGID');

        // Something like:
        // https://track.example.com/aHR0cHM6Ly9iZjNlL.....
        $trackableUrlWithDomain = $domain->buildTrackingUrl($trackableUrl);

        $this->assertStringStartsWith('https://track.example.com/', $trackableUrlWithDomain);

        // Other assertinos
        $this->assertTrue(strpos($html, '<!DOCTYPE html>') === 0);
        $this->assertTrue(strpos($html, '<title>') === false);
        $this->assertTrue(strpos($html, "\n") === false);
        $this->assertTrue(strpos($html, '<div>Hello world éèéêôâ</div>') !== false);
        $this->assertTrue(strpos($html, "'%UID%'") !== false);
    }

    public function test_test_campaign_with_special_tags()
    {
        // Tracking domain
        $domain = new TrackingDomain();
        $domain->name = 'track.example.com';
        $domain->scheme = 'https';
        $domain->verification_method = TrackingDomain::VERIFICATION_METHOD_HOST;

        $customer = Mockery::mock(new Customer());
        $customer->shouldReceive('getLanguageCode')->andReturn('en');

        // Template
        $template = Mockery::mock(new Template(['name' => 'Template']));
        $template->generateUid();

        // List
        $list = Mockery::mock(new MailList());
        $list->generateUid();
        $list->customer = $customer;

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;
        $campaign->trackingDomain = $domain;

        // Subscriber
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->mailList = $list;
        $subscriber->uid = '000000';

        $pipeline = new PipelineBuilder();
        $pipeline->add(new AddDoctype());
        $pipeline->add(new RemoveTitleTag());
        $pipeline->add(new ReplaceBareLineFeed());
        $pipeline->add(new AppendHtml('<div>Hello world éèéêôâ</div>'));
        $pipeline->add(new ParseRss());
        $pipeline->add(new TransformTag($campaign, $subscriber, 'MSGID', $server = null));
        $pipeline->add(new InjectTrackingPixel($campaign, 'MSGID'));
        $pipeline->add(new TransformUrl($template, 'MSGID', $domain)); // click url, tracking domain applied
        $pipeline->add(new MakeInlineCss([storage_path('tests/sample.css')]));


        $html = $pipeline->build()->process("<title class='blue'>\nThisis the template title</title><a class=' big blue' href='https://mailchimp.com'></a><div>Campaign: '{CAMPAIGN_NAME}' Subscriber: '{SUBSCRIBER_UID}'</div><a name='unsubscribe' href='{UNSUBSCRIBE_URL}'>Unsubscribe</a>|{UPDATE_PROFILE_URL}|{WEB_VIEW_URL}");

        // Examine the output with DOM
        $dom = new DOMDocument();
        $dom->loadHTML($html);

        // Other assertinos
        $this->assertTrue(strpos($html, '<!DOCTYPE html>') === 0);
        $this->assertTrue(strpos($html, '<title>') === false);
        $this->assertTrue(strpos($html, "\n") === false);
        $this->assertTrue(strpos($html, '<div>Hello world éèéêôâ</div>') !== false);
        $this->assertTrue(strpos($html, "'000000'") !== false);

        // Special tags
        //$this->assertTrue(strpos($html, 'c/000000/unsubscribe/TVNHSUQ') !== false);
        //$this->assertTrue(strpos($html, 'update-profile') !== false);
        //$this->assertTrue(strpos($html, 'campaigns/TVNHSUQ/web-view') !== false);
    }

    public function test_test_campaign_with_special_tags_without_tracking_domain()
    {
        $customer = Mockery::mock(new Customer());
        $customer->shouldReceive('getLanguageCode')->andReturn('en');

        // Template
        $template = Mockery::mock(new Template(['name' => 'Template']));
        $template->generateUid();

        // List
        $list = Mockery::mock(new MailList());
        $list->generateUid();
        $list->customer = $customer;

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;

        // Subscriber
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->mailList = $list;
        $subscriber->uid = '000000';

        $pipeline = new PipelineBuilder();
        $pipeline->add(new AddDoctype());
        $pipeline->add(new RemoveTitleTag());
        $pipeline->add(new ReplaceBareLineFeed());
        $pipeline->add(new AppendHtml('<div>Hello world éèéêôâ</div>'));
        $pipeline->add(new ParseRss());
        $pipeline->add(new TransformTag($campaign, $subscriber, 'MSGID', $server = null));
        $pipeline->add(new InjectTrackingPixel($campaign, 'MSGID'));
        $pipeline->add(new TransformUrl($template, 'MSGID', null)); // click url, tracking domain applied
        $pipeline->add(new MakeInlineCss([storage_path('tests/sample.css')]));


        $html = $pipeline->build()->process("<title class='blue'>\nThisis the template title</title><a class=' big blue' href='https://mailchimp.com'></a><div>Campaign: '{CAMPAIGN_NAME}' Subscriber: '{SUBSCRIBER_UID}'</div><a name='unsubscribe' href='{UNSUBSCRIBE_URL}'>Unsubscribe</a>|{UPDATE_PROFILE_URL}|{WEB_VIEW_URL}");

        // Other assertinos
        $this->assertTrue(strpos($html, "'000000'") !== false);
        $this->assertTrue(strpos($html, '<!DOCTYPE html>') === 0);
        $this->assertTrue(strpos($html, '<title>') === false);
        $this->assertTrue(strpos($html, "\n") === false);
        $this->assertTrue(strpos($html, '<div>Hello world éèéêôâ</div>') !== false);

        // Special tags
        $this->assertTrue(strpos($html, 'c/000000/unsubscribe/TVNHSUQ') !== false);
        $this->assertTrue(strpos($html, 'update-profile') !== false);
        $this->assertTrue(strpos($html, 'campaigns/TVNHSUQ/web-view') !== false);
    }

    public function test_caching_just_works()
    {
        // Template
        $template = Mockery::mock(new Template(['name' => 'Template', 'content' => 'Original']));
        $template->generateUid();

        // List
        $list = Mockery::mock(new MailList());
        $list->generateUid();

        // Campaign
        $campaign = Mockery::mock(new Campaign(['name' => 'Campaign']));
        $campaign->defaultMailList = $list;
        $campaign->template = $template;

        // Flush any cache
        $campaign->clearCache();

        // Get
        $html = $campaign->getHtmlContent();
        $cachedHtml = $campaign->getHtmlContent($subscriber = null, $messageId = null, $server = null, $fromCache = true, $seconds = 1);

        // Notice that message should end with an "\r\n" character as suggested by RFC2822
        $this->assertEquals($html, '<!DOCTYPE html><html><body><p>Original</p><img src="'.asset('/images/transparent.gif').'" data="X-Client-Message-Id: [ null ]" alt="X-Client-Message-Id: [ null ]" width="0" height="0" style="visibility:hidden"></body></html>'."\r\n");
        $this->assertEquals($html, $cachedHtml);

        $campaign->template->content = 'Updated';
        $html = $campaign->getHtmlContent();
        $cachedHtml = $campaign->getHtmlContent($subscriber = null, $messageId = null, $server = null, $fromCache = true, $seconds = 1);

        // Notice that message should end with an "\r\n" character as suggested by RFC2822
        $this->assertEquals($html, '<!DOCTYPE html><html><body><p>Updated</p><img src="'.asset('/images/transparent.gif').'" data="X-Client-Message-Id: [ null ]" alt="X-Client-Message-Id: [ null ]" width="0" height="0" style="visibility:hidden"></body></html>'."\r\n");
        $this->assertEquals($cachedHtml, '<!DOCTYPE html><html><body><p>Original</p><img src="'.asset('/images/transparent.gif').'" data="X-Client-Message-Id: [ null ]" alt="X-Client-Message-Id: [ null ]" width="0" height="0" style="visibility:hidden"></body></html>'."\r\n");

        // wait 2 seconds for cache to expire
        sleep(2);
        $cachedHtml = $campaign->getHtmlContent($subscriber = null, $messageId = null, $server = null, $fromCache = true);
        $this->assertEquals($cachedHtml, $html);
    }

    public function test_spintax_parser_just_works_and_does_not_break_css()
    {
        $pipeline = new PipelineBuilder();
        $pipeline->add(new GenerateSpintax());

        // HTML with CSS
        $html = '<style>div > p {color:red}</style><p>{Hi|Hello} everyone</p>';

        // Transformed HTML with CSS retained
        $transformedHtml = $pipeline->build()->process($html);

        // Output
        $this->assertTrue($transformedHtml == '<style>div > p {color:red}</style><p>Hello everyone</p>' || $transformedHtml = '<style>div > p {color:red}</style><p>Hi everyone</p>');
    }

    public function test_inject_message_id_to_body()
    {
        $pipeline = new PipelineBuilder();
        $pipeline->add(new InjectMessageIdToBody('MSGID'));

        // HTML with CSS
        $html = '<body>Hello</body>';

        // Transformed HTML with CSS retained
        $transformedHtml = $pipeline->build()->process($html);

        $imgUrl = asset('/images/transparent.gif');

        // Output
        $this->assertEquals(
            $transformedHtml,
            '<!DOCTYPE html><html><body>Hello<img src="'.$imgUrl.'" data="X-Client-Message-Id: MSGID" alt="X-Client-Message-Id: MSGID" width="0" height="0" style="visibility:hidden"></body></html>'
        );
    }
}
