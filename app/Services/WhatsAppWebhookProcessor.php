<?php
namespace App\Services;

use App\Models\WebhookEvent;

class WhatsAppWebhookProcessor
{
    public function process(array $webhookData): WebhookEvent
    {
        $result = [
            'event_type' => null,
            'celular' => null,
            'name' => null,
            'api_phone_id' => null,
            'api_phone_number' => null,
            'message' => null,
            'interactive_id' => null,
            'status' => null,
            'status_id' => null,
            'conversation' => null,
            'message_id' => null,
        ];

        $entry = $webhookData['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $changesValue = $changes['value'] ?? null;
        $contacts = $changesValue['contacts'][0] ?? null;

        if (isset($contacts['profile']['name'])) {
            $result['name'] = $contacts['profile']['name'];
        }
        if (isset($changesValue['metadata']['phone_number_id'])) {
            $result['api_phone_id'] = $changesValue['metadata']['phone_number_id'];
            $result['api_phone_number'] = $changesValue['metadata']['display_phone_number'];
        }

        if (isset($changesValue['statuses'])) {
            $result['event_type'] = 'status';
            $result['celular'] = $changesValue['statuses'][0]['recipient_id'];
            $result['status'] = $changesValue['statuses'][0]['status'];
            $result['status_id'] = $changesValue['statuses'][0]['id'];
            $result['conversation'] = $changesValue['statuses'][0]['conversation'] ?? null;

        } elseif (isset($changesValue['messages'])) {
            $message = $changesValue['messages'][0];
            $result['celular'] = $message['from'];
            $result['message_id'] = $message['id'] ?? null;

            switch ($message['type']) {
                case 'text':
                    $result['event_type'] = 'message_text';
                    $result['message'] = $message['text']['body'] ?? null;
                    break;
                case 'button':
                    $result['event_type'] = 'message_button';
                    $result['message'] = $message['button']['payload'] ?? null;
                    break;
                case 'interactive':
                    if (isset($message['interactive']['button_reply'])) {
                        $result['event_type'] = 'message_button';
                        $result['message'] = $message['interactive']['button_reply']['title'] ?? null;
                        $result['interactive_id'] = $message['interactive']['button_reply']['id'] ?? null;
                    } elseif (isset($message['interactive']['list_reply'])) {
                        $result['event_type'] = 'interactive';
                        $result['message'] = $message['interactive']['list_reply']['title'] ?? null;
                        $result['interactive_id'] = $message['interactive']['list_reply']['id'] ?? null;
                    }
                    break;
            }
        }

        return WebhookEvent::create($result);
    }
}
