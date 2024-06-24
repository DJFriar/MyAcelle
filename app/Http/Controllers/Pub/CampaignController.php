<?php

namespace Acelle\Http\Controllers\Pub;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as LaravelLog;
use Acelle\Http\Controllers\Controller;
use Acelle\Library\StringHelper;
use Acelle\Jobs\ExportCampaignLog;
use Acelle\Model\TrackingLog;
use Acelle\Model\Subscriber;
use Acelle\Model\Campaign;
use Acelle\Model\IpLocation;
use Acelle\Model\ClickLog;
use Acelle\Model\OpenLog;
use Acelle\Model\JobMonitor;
use DB;
use Carbon\Carbon;

class CampaignController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Campaign overview.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function overview(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        // Trigger the CampaignUpdate event to update the campaign cache information
        // The second parameter of the constructor function is false, meanining immediate update
        event(new \Acelle\Events\CampaignUpdated($campaign));

        return view('public.campaigns.overview', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Campaign links.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function links(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);
        $links = $campaign->clickLogs()
                          ->select(
                              'click_logs.url',
                              DB::raw('count(*) AS clickCount'),
                              DB::raw(sprintf('max(%s) AS lastClick', table('click_logs.created_at')))
                          )->groupBy('click_logs.url')->get();

        return view('public.campaigns.links', [
            'campaign' => $campaign,
            'links' => $links,
        ]);
    }

    /**
     * 24-hour chart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chart24h(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);
        $currentTimezone = $campaign->customer->getTimezone();

        $result = [
            'columns' => [],
            'opened' => [],
            'clicked' => [],
        ];



        // 24h collection
        if ($request->period == '24h') {
            $hours = [];

            // columns
            for ($i = 23; $i >= 0; --$i) {
                $time = Carbon::now()->timezone($currentTimezone)->subHours($i);
                $result['columns'][] = $time->format('h') . ':00 ' . $time->format('A');
                $hours[] = $time->format('H');
            }

            $openData24h = $campaign->openUniqHours(Carbon::now('UTC')->subHours(24), Carbon::now('UTC'));
            $clickData24h = $campaign->clickHours(Carbon::now('UTC')->subHours(24), Carbon::now('UTC'));

            // data
            foreach ($hours as $hour) {
                $num = isset($openData24h[$hour]) ? count($openData24h[$hour]) : 0;
                $result['opened'][] = $num;

                $num = isset($clickData24h[$hour]) ? count($clickData24h[$hour]) : 0;
                $result['clicked'][] = $num;
            }
        } elseif ($request->period == '3_days') {
            $days = [];

            // columns
            for ($i = 2; $i >= 0; --$i) {
                $time = Carbon::now()->timezone($currentTimezone)->subDays($i);
                $result['columns'][] = $time->format('m-d');
                $days[] = $time->format('Y-m-d');
            }

            $openData = $campaign->openUniqDays(Carbon::now('UTC')->subDays(3), Carbon::now('UTC')->endOfDay());
            $clickData = $campaign->clickDays(Carbon::now('UTC')->subDays(3), Carbon::now('UTC')->endOfDay());

            // data
            foreach ($days as $day) {
                $num = isset($openData[$day]) ? count($openData[$day]) : 0;
                $result['opened'][] = $num;

                $num = isset($clickData[$day]) ? count($clickData[$day]) : 0;
                $result['clicked'][] = $num;
            }
        } elseif ($request->period == '7_days') {
            $days = [];

            // columns
            for ($i = 6; $i >= 0; --$i) {
                $time = Carbon::now()->timezone($currentTimezone)->subDays($i);
                $result['columns'][] = $time->format('m-d');
                $days[] = $time->format('Y-m-d');
            }

            $openData = $campaign->openUniqDays(Carbon::now('UTC')->subDays(7), Carbon::now('UTC')->endOfDay());
            $clickData = $campaign->clickDays(Carbon::now('UTC')->subDays(7), Carbon::now('UTC')->endOfDay());

            // data
            foreach ($days as $day) {
                $num = isset($openData[$day]) ? count($openData[$day]) : 0;
                $result['opened'][] = $num;

                $num = isset($clickData[$day]) ? count($clickData[$day]) : 0;
                $result['clicked'][] = $num;
            }
        } elseif ($request->period == 'last_month') {
            $days = [];

            // columns
            for ($i = Carbon::now('UTC')->subMonths(1)->diff(Carbon::now('UTC'))->days - 1; $i >= 0; --$i) {
                $time = Carbon::now()->timezone($currentTimezone)->subDays($i);
                $result['columns'][] = $time->format('m-d');
                $days[] = $time->format('Y-m-d');
            }

            $openData = $campaign->openUniqDays(Carbon::now('UTC')->subMonths(1), Carbon::now('UTC')->endOfDay());
            $clickData = $campaign->clickDays(Carbon::now('UTC')->subMonths(1), Carbon::now('UTC')->endOfDay());

            // data
            foreach ($days as $day) {
                $num = isset($openData[$day]) ? count($openData[$day]) : 0;
                $result['opened'][] = $num;

                $num = isset($clickData[$day]) ? count($clickData[$day]) : 0;
                $result['clicked'][] = $num;
            }
        } elseif ($request->period == 'last_year') {
            $months = [];

            // columns
            for ($i = Carbon::now('UTC')->subYears(1)->diffInMonths(Carbon::now('UTC')) - 1; $i >= 0; --$i) {
                $time = Carbon::now()->timezone($currentTimezone)->subMonths($i);
                $result['columns'][] = $time->format('Y, M');
                $months[] = $time->format('Y-m');
            }

            $openData = $campaign->openUniqMonths(Carbon::now('UTC')->subYears(1), Carbon::now('UTC')->endOfDay());
            $clickData = $campaign->clickMonths(Carbon::now('UTC')->subYears(1), Carbon::now('UTC')->endOfDay());

            // data
            foreach ($months as $month) {
                $num = isset($openData[$month]) ? count($openData[$month]) : 0;
                $result['opened'][] = $num;

                $num = isset($clickData[$month]) ? count($clickData[$month]) : 0;
                $result['clicked'][] = $num;
            }
        }


        return response()->json($result);
    }

    /**
     * Chart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chart(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $result = [
            [
                'name' => trans('messages.recipients'),
                'value' => $campaign->readCache('SubscriberCount', 0),
            ],
            [
                'name' => trans('messages.delivered'),
                'value' => $campaign->deliveredCount(),
            ],
            [
                'name' => trans('messages.failed'),
                'value' => $campaign->failedCount(),
            ],
            [
                'name' => trans('messages.Open'),
                'value' => $campaign->openUniqCount(),
            ],
            [
                'name' => trans('messages.Click'),
                'value' => $campaign->uniqueClickCount(),
            ],
            [
                'name' => trans('messages.Bounce'),
                'value' => $campaign->bounceCount(),
            ],
            [
                'name' => trans('messages.report'),
                'value' => $campaign->feedbackCount(),
            ],
            [
                'name' => trans('messages.unsubscribe'),
                'value' => $campaign->unsubscribeCount(),
            ],
        ];

        return response()->json($result);
    }

    /**
     * Chart Country.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chartCountry(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $result = [
            'data' => [],
        ];

        // create data
        $total = $campaign->uniqueOpenCount();
        $count = 0;
        foreach ($campaign->topOpenCountries()->get() as $location) {
            $country_name = (!empty($location->country_name) ? $location->country_name : trans('messages.unknown'));
            $result['data'][] = ['value' => $location->aggregate, 'name' => $country_name];
            $count += $location->aggregate;
        }

        // Others
        if ($total > $count) {
            $result['data'][] = ['value' => $total - $count, 'name' => trans('messages.others')];
        }

        usort($result['data'], function ($a, $b) {
            return strcmp($a['value'], $b['value']);
        });
        $result['data'] = array_reverse($result['data']);

        return response()->json($result);
    }

    /**
     * Chart Country by clicks.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chartClickCountry(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $result = [
            'data' => [],
        ];

        // create data
        $datas = [];
        $total = $campaign->clickCount();
        $count = 0;
        foreach ($campaign->topClickCountries()->get() as $location) {
            $result['data'][] = ['value' => $location->aggregate, 'name' => $location->country_name];
            $count += $location->aggregate;
        }

        // others
        if ($total > $count) {
            $result['data'][] = ['value' => $total - $count, 'name' => trans('messages.others')];
        }

        usort($result['data'], function ($a, $b) {
            return strcmp($a['value'], $b['value']);
        });
        $result['data'] = array_reverse($result['data']);

        return response()->json($result);
    }

    /**
     * 24-hour quickView.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function quickView(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        return view('public.campaigns._quick_view', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Tracking when open.
     */
    public function open(Request $request)
    {
        try {
            // Record open log
            $openLog = OpenLog::createFromRequest($request);

            // Execute open callbacks registered for the campaign
            if ($openLog->trackingLog && $openLog->trackingLog->campaign) {
                $openLog->trackingLog->campaign->queueOpenCallbacks($openLog);
            }
        } catch (\Exception $ex) {
            // do nothing
        }

        return response()->file(public_path('images/transparent.gif'));
    }

    /**
     * Tracking when click link.
     */
    public function click(Request $request)
    {
        list($url, $log) = ClickLog::createFromRequest($request);

        if ($log && $log->trackingLog && $log->trackingLog->campaign) {
            $log->trackingLog->campaign->queueClickCallbacks($log);
        }

        return redirect()->away($url);
    }

    /**
     * Unsubscribe url.
     */
    public function unsubscribe(Request $request)
    {
        $subscriber = Subscriber::findByUid($request->subscriber);
        $message_id = StringHelper::base64UrlDecode($request->message_id);

        if (is_null($subscriber)) {
            LaravelLog::error('Subscriber does not exist');
            return view('somethingWentWrong', ['message' => trans('subscriber.invalid')]);
        }

        if ($subscriber->isUnsubscribed()) {
            return view('notice', ['message' => trans('messages.you_are_already_unsubscribed')]);
        }

        // User Tracking Information
        $trackingInfo = [
            'message_id' => $message_id,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ];

        // GeoIP information
        $location = IpLocation::add($request->ip());
        if (!is_null($location)) {
            $trackingInfo['ip_address'] = $location->ip_address;
        }

        // Actually Unsubscribe with tracking information
        $subscriber->unsubscribe($trackingInfo);

        // Page content
        $list = $subscriber->mailList;
        $layout = \Acelle\Model\Layout::where('alias', 'unsubscribe_success_page')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);

        $page->renderContent(null, $subscriber);

        return view('pages.default', [
            'list' => $list,
            'page' => $page,
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Tracking logs.
     */
    public function trackingLog(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = $campaign->trackingLogs();

        return view('public.campaigns.tracking_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Tracking logs ajax listing.
     */
    public function trackingLogListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = TrackingLog::search($request, $campaign)->paginate($request->per_page);

        return view('public.campaigns.tracking_logs_list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Download tracking logs.
     */
    public function trackingLogDownload(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $logtype = $request->input('logtype');

        $job = new ExportCampaignLog($campaign, $logtype);
        $monitor = $campaign->dispatchWithMonitor($job);

        return view('public.campaigns.download_tracking_log', [
            'campaign' => $campaign,
            'job' => $monitor,
        ]);
    }

    /**
     * Tracking logs export progress.
     */
    public function trackingLogExportProgress(Request $request)
    {
        $job = JobMonitor::findByUid($request->uid);

        $progress = $job->getJsonData();
        $progress['status'] = $job->status;
        $progress['error'] = $job->error;
        $progress['download'] = action('Pub\CampaignController@download', ['uid' => $job->uid]);

        return response()->json($progress);
    }

    /**
     * Actually download.
     */
    public function download(Request $request)
    {
        $job = JobMonitor::findByUid($request->uid);
        $path = $job->getJsonData()['path'];
        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * Bounce logs.
     */
    public function bounceLog(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = $campaign->bounceLogs();

        return view('public.campaigns.bounce_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Bounce logs listing.
     */
    public function bounceLogListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = \Acelle\Model\BounceLog::search($request, $campaign)->paginate($request->per_page);

        return view('public.campaigns.bounce_logs_list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * FBL logs.
     */
    public function feedbackLog(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = $campaign->openLogs();

        return view('public.campaigns.feedback_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * FBL logs listing.
     */
    public function feedbackLogListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = \Acelle\Model\FeedbackLog::search($request, $campaign)->paginate($request->per_page);

        return view('public.campaigns.feedback_logs_list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Open logs.
     */
    public function openLog(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = $campaign->openLogs();

        return view('public.campaigns.open_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Open logs listing.
     */
    public function openLogListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = \Acelle\Model\OpenLog::search($request, $campaign)->paginate($request->per_page);

        return view('public.campaigns.open_log_list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Click logs.
     */
    public function clickLog(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = $campaign->clickLogs();

        return view('public.campaigns.click_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Click logs listing.
     */
    public function clickLogListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = \Acelle\Model\ClickLog::search($request, $campaign)->paginate($request->per_page);

        return view('public.campaigns.click_log_list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Unscubscribe logs.
     */
    public function unsubscribeLog(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = $campaign->unsubscribeLogs();

        return view('public.campaigns.unsubscribe_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Unscubscribe logs listing.
     */
    public function unsubscribeLogListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $items = \Acelle\Model\UnsubscribeLog::search($request, $campaign)->paginate($request->per_page);

        return view('public.campaigns.unsubscribe_logs_list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Open map.
     */
    public function openMap(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        return view('public.campaigns.open_map', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Subscribers list.
     */
    public function subscribers(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        $subscribers = $campaign->subscribers();

        return view('public.campaigns.subscribers', [
            'subscribers' => $subscribers,
            'campaign' => $campaign,
            'list' => $campaign->defaultMailList,
        ]);
    }

    /**
     * Subscribers listing.
     */
    public function subscribersListing(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);

        // Subscribers
        $subscribers = $campaign->getDeliveryReport()
                                ->addSelect('subscribers.*')
                                ->addSelect('bounce_logs.raw AS bounced_message')
                                ->addSelect('feedback_logs.feedback_type AS feedback_message')
                                ->addSelect('tracking_logs.error AS failed_message');

        // Check open conditions
        if ($request->open) {
            // Query of email addresses that DID open
            $openByEmails = $campaign->openLogs()->join('subscribers', 'tracking_logs.subscriber_id', '=', 'subscribers.id')->groupBy('subscribers.email')->select('subscribers.email');

            if ($request->open == 'yes') {
                $subscribers = $subscribers->joinSub($openByEmails, 'OpenedByEmails', function ($join) {
                    $join->on('subscribers.email', '=', 'OpenedByEmails.email');
                });
            } elseif ($request->open = 'no') {
                $subscribers = $subscribers->leftJoinSub($openByEmails, 'OpenedByEmails', function ($join) {
                    $join->on('subscribers.email', '=', 'OpenedByEmails.email');
                })->whereNull('OpenedByEmails.email');
            }
        }

        // Check click conditions
        if ($request->click) {
            // Query of email addresses that DID click
            $clickByEmails = $campaign->clickLogs()->join('subscribers', 'tracking_logs.subscriber_id', '=', 'subscribers.id')->groupBy('subscribers.email')->select('subscribers.email');

            if ($request->click == 'clicked') {
                $subscribers = $subscribers->joinSub($clickByEmails, 'ClickedByEmails', function ($join) {
                    $join->on('subscribers.email', '=', 'ClickedByEmails.email');
                });
            } elseif ($request->click = 'not_clicked') {
                $subscribers = $subscribers->leftJoinSub($clickByEmails, 'ClickedByEmails', function ($join) {
                    $join->on('subscribers.email', '=', 'ClickedByEmails.email');
                })->whereNull('ClickedByEmails.email');
            }
        }

        // Paging
        $subscribers = $subscribers->search($request->keyword)->paginate($request->per_page ? $request->per_page : 50);

        // Field information
        $fields = $campaign->defaultMailList->getFields->whereIn('uid', $request->columns);

        return view('public.campaigns._subscribers_list', [
            'subscribers' => $subscribers,
            'list' => $campaign->defaultMailList,
            'campaign' => $campaign,
            'fields' => $fields,
        ]);
    }

    /**
     * Preview template.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $campaign = Campaign::findByUid($id);

        return view('public.campaigns.preview', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Preview content template.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function previewContent(Request $request)
    {
        $campaign = Campaign::findByUid($request->uid);
        $subscriber = Subscriber::findByUid($request->subscriber_uid);

        echo $campaign->getHtmlContent($subscriber);
    }

    /**
     * Email web view.
     */
    public function webView(Request $request)
    {
        $message_id = StringHelper::base64UrlDecode($request->message_id);
        $tracking_log = TrackingLog::where('message_id', '=', $message_id)->first();

        try {
            if (!$tracking_log) {
                throw new \Exception(trans('messages.web_view_can_not_find_tracking_log_with_message_id'));
            }

            $subscriber = $tracking_log->subscriber;
            $campaign = $tracking_log->campaign;

            if (!$campaign || !$subscriber) {
                throw new \Exception(trans('messages.web_view_can_not_find_campaign_or_subscriber'));
            }

            return view('public.campaigns.web_view', [
                'campaign' => $campaign,
                'subscriber' => $subscriber,
                'message_id' => $message_id,
            ]);
        } catch (\Exception $e) {
            return view('somethingWentWrong', ['message' => trans('messages.the_email_no_longer_exists')]);
        }
    }

    /**
     * Email web view for previewing before sending
     */
    public function webViewPreview(Request $request)
    {
        $subscriber = Subscriber::findByUid($request->subscriber_uid);
        $campaign = Campaign::findByUid($request->campaign_uid);

        if (is_null($subscriber) || is_null($campaign)) {
            throw new \Exception('Invalid subscriber or campaign UID');
        }

        return view('public.campaigns.web_view', [
            'campaign' => $campaign,
            'subscriber' => $subscriber,
            'message_id' => null,
        ]);
    }

    /**
     * Template review.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateReview(Request $request)
    {
        // Get current user
        $campaign = Campaign::findByUid($request->uid);

        return view('public.campaigns.template_review', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Template review iframe.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateReviewIframe(Request $request)
    {
        // Get current user
        $campaign = Campaign::findByUid($request->uid);

        return view('public.campaigns.template_review_iframe', [
            'campaign' => $campaign,
        ]);
    }
}
