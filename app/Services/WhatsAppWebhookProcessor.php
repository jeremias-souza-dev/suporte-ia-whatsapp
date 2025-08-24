<?php

namespace App\Services;

class WhatsAppWebhookProcessor
{
    public function process(array $webhookData): array
    {
        $result = [
            'event_type' => null,
            'celular' => null,
            'name' => null,
            'api_phone_id' => null,
            'api_phone_number' => null,
            'message' => null,
            'interactive_id' => null,
        ];

        $entry = $webhookData['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $changesValue = $changes['value'] ?? null;
        $contacts = $changesValue['contacts'][0] ?? null;

        if (isset($entry['id'])) {
            $result['id'] = $entry['id'];
        }
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
                    if (isset($message['text']['body'])) {
                        $result['event_type'] = 'message_text';
                        $result['message'] = $message['text']['body'];
                    }
                    break;
                case 'button':
                    if (isset($message['button']['paylWebhookController oad'])) {
                        $result['event_type'] = 'message_button';
                        $result['message'] = $message['button']['payload'];
                    }
                    break;
                case 'interactive':
                    if (isset($message['interactive']['button_reply']['title'])) {
                        $result['event_type'] = 'message_button';
                        $result['message'] = $message['interactive']['button_reply']['title'];
                        $result['interactive_id'] = $message['interactive']['button_reply']['id'];
                    } elseif (isset($message['interactive']['list_reply']['id'])) {
                        $result['event_type'] = 'interactive';
                        $result['interactive'] = $message['interactive']['list_reply'];
                        $result['message'] = $message['interactive']['list_reply']['title'];
                        $result['interactive_id'] = $message['interactive']['list_reply']['id'];
                    }
                    break;
            }
        }
        
        return $result;
    }
}