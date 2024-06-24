<?php

/**
 * SendingServerSendGridApi class.
 *
 * Abstract class for SendGrid API sending server
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

use GuzzleHttp\Client;
use Soundasleep\Html2Text;

class SendingServerBlastengineApi extends SendingServerBlastengine
{
    protected $table = 'sending_servers';
    public const API_URI = 'https://app.engn.jp/api/v1/deliveries/transaction';

    public function send($message, $params = array())
    {
        $fromEmail = array_keys($message->getFrom())[0];
        $fromName = (is_null($message->getFrom())) ? null : array_values($message->getFrom())[0];
        $toEmail = array_keys($message->getTo())[0];
        $toName = (is_null($message->getTo())) ? null : array_values($message->getTo())[0];
        $replyToEmail = (is_null($message->getReplyTo())) ? $fromEmail : array_keys($message->getReplyTo())[0];

        $html = null;
        $plain = null;
        foreach ($message->getChildren() as $part) {
            $contentType = $part->getContentType();

            if ($contentType == 'text/html') {
                $html = $part->getBody();
            } elseif ($contentType == 'text/plain') {
                $plain = $part->getBody();
            }
        }

        if (empty($plain)) {
            $options = array(
              'ignore_errors' => true,
            );

            // Plain part is required by Blastengine
            $plain = Html2Text::convert($html, $options);
        }

        // トークン生成
        $str = "{$this->username}{$this->api_key}";
        $token = base64_encode(strtolower(hash('sha256', $str)));

        // POSTデータ
        $data = [
            "from" => [
                "email" => $fromEmail,
                "name" => $fromName
            ],
            "to" => $toEmail,
            "subject" => $message->getSubject(),
            "encode" => "UTF-8",
            "text_part" => $plain,
            "html_part" => $html
        ];
        $data = json_encode($data);

        $header = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$token}"
        ];

        $client = new Client(['verify' => false]);
        $response = $client->request(
            'POST',
            self::API_URI,
            [ 'headers' => $header, 'body' => $data, 'verify' => false]
        );

        $resjson = json_decode($response->getBody(), true);

        $deliveryOutput = [
            'runtime_message_id' => $resjson['delivery_id'],
            'status' => self::DELIVERY_STATUS_SENT,
        ];

        return $deliveryOutput;
    }
}
