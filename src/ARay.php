<?php

namespace LaravelARay\LaravelARay;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ARay
{
    private static string $ENDPOINT = 'https://api.a-ray.subvitamine.com/webhooks/push/';

    /**
     * Check config of a-ray
     */
    static function checkConfig(): bool
    {
        $config = config('laravel-a-ray');

        if ($config['enabled'] && $config['private_key'] === '') {
            throw new \Exception('You must set private key in config file');
        }

        if ($config['notify_errors']['enabled'] && $config['notify_errors']['slack_webhook_url'] === '') {
            throw new \Exception('You must set slack webhook url in config file');
        }

        return true;
    }

    /**
     * Init push class
     * @param string $label Label of push
     */
    static function initPush(string $label): ARayPush
    {
        return new ARayPush($label);
    }

    /**
     * Push a push
     * @param ARayPush $push
     * @return boolean
     */
    public static function push(ARayPush $push): bool
    {
        $push->setEndAt(Carbon::now());

        try {
            $response = Http::post(self::$ENDPOINT . config('laravel-a-ray.private_key'), $push->toJson());

            if ($response->status() !== 200) {
                throw new \Exception('Error when push to a-ray');
            }

            if(config('laravel-a-ray.notify_errors.enabled') && self::checkConfig()) {
                $allErrors  = [];

                foreach ($push->getCommits() as $commit) {
                    if($commit['status'] === CommitStatus::ERROR) {
                        $allErrors += $commit['label'];
                    }
                }

                if(count($allErrors) > 0) {
                    Http::post(config('laravel-a-ray.notify_errors.slack_webhook_url'), [
                        'text' => 'A Ray error : ' . $push->getLabel() . ' : ' . implode(', ', $allErrors)
                    ]);
                }
            }

        } catch (\Exception $e) {
            if (config('laravel-a-ray.notify_errors.enabled')) {
                Http::post(config('laravel-a-ray.notify_errors.slack_webhook_url'), [
                    'text' => 'Error while pushing to a-ray: ' . $e->getMessage()
                ]);
            }
        }

        return true;
    }
}