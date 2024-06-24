<?php

/**
 * FeedbackLoopHandler class.
 *
 * Model class for feedback loop handler
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

use Acelle\Library\Traits\HasUid;

class FeedbackLoopHandler extends DeliveryHandler
{
    use HasUid;

    protected $logfile = 'feedback-loop-handler';

    // Available feedback types
    // Reference https://en.wikipedia.org/wiki/Abuse_Reporting_Format
    public const FEEDBACK_TYPES = ['abuse', 'fraud', 'virus', 'other', 'not-spam'];
    public const EXCLUDED_FEEDBACK_TYPES = ['not-spam'];

    /**
     * Process feedback message, extract the feedback information (comply to RFC).
     *
     * @return mixed
     */
    public function processMessage($mbox, $msgNo)
    {
        try {
            // Get subject
            $info = imap_headerinfo($mbox, $msgNo);
            $subject = isset($info->subject) ? $info->subject : '[no subject]';

            // Sometimes the Subject is encoded like this: =?UTF-8?B?WWFob28gZW1haWzCoHZlcmlmaWNhdGlvbsKgY29kZQ==?=
            // Use imap_utf8() to safely decode it
            $subject = imap_utf8($subject);
            $this->logger()->info("Message: {$subject}");

            // Get headers & body
            $header = imap_fetchheader($mbox, $msgNo);
            $body = imap_body($mbox, $msgNo, FT_PEEK);

            // Sometimes the header is included in the body of the bounced message
            // So, search for feedback type in both header and body
            $feedbackType = $this->getFeedbackType($header) ?: $this->getFeedbackType($body);

            if (is_null($feedbackType)) {
                imap_setflag_full($mbox, $msgNo, '\\Seen \\Flagged');
                throw new \Exception('Cannot find Feedback-Type header, not an ARF message. Skipped');
            }

            if (in_array($feedbackType, self::EXCLUDED_FEEDBACK_TYPES)) {
                imap_setflag_full($mbox, $msgNo, '\\Seen \\Flagged');
                throw new \Exception('Feedback-Type is "not-spam". Ignored.');
            }

            $msgId = $this->getMessageId($body);

            if (empty($msgId)) {
                imap_setflag_full($mbox, $msgNo, '\\Seen \\Flagged');
                throw new \Exception('cannot find Message-ID, skipped');
            }

            $this->logger()->info('Processing abuse notification for message '.$msgId);

            $trackingLog = TrackingLog::where('message_id', $msgId)->first();
            if (empty($trackingLog)) {
                imap_setflag_full($mbox, $msgNo, '\\Seen \\Flagged');
                throw new \Exception('message-id not found in tracking_logs, skipped');
            }

            // record a bounce log, one message may have more than one
            $feedbackLog = new FeedbackLog();
            $feedbackLog->message_id = $msgId;
            $feedbackLog->feedback_type = $feedbackType;
            $feedbackLog->raw_feedback_content = $header.PHP_EOL.$body;
            $feedbackLog->save();

            // just delete the bounce notification email
            imap_delete($mbox, $msgNo);

            $this->logger()->info('Feedback recorded for message '.$msgId);
        } catch (\Exception $ex) {
            $this->logger()->warning($ex->getMessage());
        }
    }

    /**
     * Extract FeedbackType from feedback email.
     *
     * @return mixed
     */
    public function getFeedbackType($header)
    {
        preg_match('/(?<=Feedback-Type:)\s*[^\s]*/', $header, $matched);
        if (sizeof($matched) == 0) {
            return;
        }

        $type = strtolower(trim($matched[0]));
        return $type;
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return self::select('*');
    }

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $query = self::select('feedback_loop_handlers.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('feedback_loop_handlers.name', 'like', '%'.$keyword.'%')
                        ->orWhere('feedback_loop_handlers.type', 'like', '%'.$keyword.'%')
                        ->orWhere('feedback_loop_handlers.host', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->all();
        if (!empty($filters)) {
            if (!empty($filters['type'])) {
                $query = $query->where('feedback_loop_handlers.type', '=', $filters['type']);
            }
        }

        if (!empty($request->admin_id)) {
            $query = $query->where('feedback_loop_handlers.admin_id', '=', $request->admin_id);
        }

        return $query;
    }

    /**
     * Search items.
     *
     * @return collect
     */
    public static function search($request)
    {
        $query = self::filter($request);

        if (!empty($request->sort_order)) {
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        }

        return $query;
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'host', 'port', 'username', 'password', 'protocol', 'email', 'encryption',
    ];

    /**
     * Get validation rules.
     *
     * @return object
     */
    public static function rules()
    {
        return [
            'name' => 'required',
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'protocol' => 'required',
            'email' => 'required|email',
        ];
    }

    /**
     * Get select options.
     *
     * @return array
     */
    public static function getSelectOptions()
    {
        $query = self::getAll();

        $options = $query->orderBy('name', 'asc')->get()->map(function ($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });

        return $options;
    }

    /**
     * Protocol select options.
     *
     * @return array
     */
    public static function protocolSelectOptions()
    {
        return [
            ['value' => 'imap', 'text' => 'imap'],
        ];
    }

    /**
     * Encryption select options.
     *
     * @return array
     */
    public static function encryptionSelectOptions()
    {
        return [
            ['value' => 'tls', 'text' => 'tls'],
            ['value' => 'starttls', 'text' => 'starttls'],
            ['value' => 'notls', 'text' => 'notls'],
            ['value' => 'ssl', 'text' => 'ssl'],
        ];
    }
}
