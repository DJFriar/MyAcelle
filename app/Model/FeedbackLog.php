<?php

/**
 * DeliveryHandler class.
 *
 * Model class for feedback loop logs
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;

class FeedbackLog extends Model
{
    public const FEEDBACK_TYPE_ABUSE = 'abuse';
    /**
     * Associations.
     *
     * @var object | collect
     */
    public function trackingLog()
    {
        return $this->belongsTo('Acelle\Model\TrackingLog', 'message_id', 'message_id');
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return self::select('feedback_logs.*');
    }

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $query = self::select('feedback_logs.*');
        $query = $query->join('tracking_logs', 'feedback_logs.message_id', '=', 'tracking_logs.message_id');
        $query = $query->leftJoin('subscribers', 'subscribers.id', '=', 'tracking_logs.subscriber_id');
        $query = $query->leftJoin('campaigns', 'campaigns.id', '=', 'tracking_logs.campaign_id');
        $query = $query->leftJoin('sending_servers', 'sending_servers.id', '=', 'tracking_logs.sending_server_id');
        $query = $query->leftJoin('customers', 'customers.id', '=', 'tracking_logs.customer_id');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('campaigns.name', 'like', '%'.$keyword.'%')
                        ->orwhere('feedback_logs.feedback_type', 'like', '%'.$keyword.'%')
                        ->orwhere('feedback_logs.raw_feedback_content', 'like', '%'.$keyword.'%')
                        ->orwhere('sending_servers.name', 'like', '%'.$keyword.'%')
                        ->orwhere('subscribers.email', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->all();
        if (!empty($filters)) {
            if (!empty($filters['campaign_uid'])) {
                $query = $query->where('campaigns.uid', '=', $filters['campaign_uid']);
            }
        }

        return $query;
    }

    /**
     * Find corresponding subscriber by 'runtime_message_id'.
     *
     * @return mix
     */
    public function findSubscriberByRuntimeMessageId()
    {
        $trackingLog = TrackingLog::where('runtime_message_id', $this->runtime_message_id)->first();
        if ($trackingLog) {
            return $trackingLog->subscriber;
        } else {
            return;
        }
    }

    public static function recordFeedback($runtimeMessageId, $raw, $logCallback)
    {
        $feedbackLog = new static();
        $feedbackLog->runtime_message_id = $runtimeMessageId;

        // retrieve the associated tracking log in Acelle
        $trackingLog = TrackingLog::where('runtime_message_id', $feedbackLog->runtime_message_id)->first();
        if ($trackingLog) {
            $feedbackLog->message_id = $trackingLog->message_id;
        } else {
            $logCallback("Cannot find a tracking log record with runtime_message_id of #{$runtimeMessageId}. Record it anyway without runtime_message_id");
        }

        $feedbackLog->feedback_type = self::FEEDBACK_TYPE_ABUSE;
        $feedbackLog->raw_feedback_content = $raw; // notice that {$raw} might contain more than one events
        $feedbackLog->save();

        // add subscriber's email to blacklist
        $subscriber = $feedbackLog->findSubscriberByRuntimeMessageId();
        if ($subscriber) {
            $subscriber->sendToBlacklist($feedbackLog->raw);
            $logCallback("Feedback recorded for runtime_message_id '$feedbackLog->runtime_message_id' and {$subscriber->email} is blacklisted!");
        } else {
            $logCallback("Feedback recorded for runtime_message_id '$feedbackLog->runtime_message_id' although it is not associated with any tracking log.");
        }
    }

    /**
     * Search items.
     *
     * @return collect
     */
    public static function search($request, $campaign = null)
    {
        $query = self::filter($request);

        if (isset($campaign)) {
            $query = $query->where('tracking_logs.campaign_id', '=', $campaign->id);
        }

        $query = $query->orderBy($request->sort_order, $request->sort_direction);

        return $query;
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;
}
