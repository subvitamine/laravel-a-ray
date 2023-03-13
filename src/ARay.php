<?php

namespace Subvitamine\LaravelARay;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Subvitamine\LaravelARay\Jobs\PushMiddlewareResult;
use Throwable;

class ARay
{
    private static string $ENDPOINT_MONO = 'http://localhost:3000/webhooks/push/';
    private static string $ENDPOINT_MULTIPLE = 'http://localhost:3000/webhooks/pushes/';

    /**
     * Check config of a-ray
     */
    static function checkConfig(): bool
    {
        $config = config('laravel-a-ray');

        if ($config['enabled'] && $config['private_key'] === '') {
            throw new Exception('You must set private key in config file');
        }

        if ($config['notify_errors']['enabled'] && $config['notify_errors']['channel'] === '') {
            throw new Exception('You must set slack webhook url in config file');
        }

        return true;
    }

    /**
     * Init push class
     * @param string $label Label of push
     */
    static function initPush(string $label, Carbon $startAt = null): ARayPush
    {
        return new ARayPush($label, $startAt);
    }

    /**
     * Init push class
     * @param Throwable $error
     * @return bool
     * @throws Exception
     */
    static function handleErrors(Throwable $error): bool
    {
        $push = self::initPush("Error handler");

        $push->addCommit($error->getMessage(), (array)json_decode(str_replace("\u0000", "", json_encode((array)$error))), CommitStatus::ERROR);

        self::push($push);

        return true;
    }

    /**
     * Push a push
     * @param ARayPush $push
     * @return boolean
     */
    public static function push(ARayPush $push, Carbon $endAt = null): bool
    {
        $push->setEndAt($endAt ? $endAt : Carbon::now());

        try {
            $response = Http::post(self::$ENDPOINT_MONO . config('laravel-a-ray.private_key'), $push->toJson());

            if ($response->status() !== 201) {
                throw new Exception('Error when push to a-ray');
            }

            if(config('laravel-a-ray.notify_errors.enabled') && self::checkConfig()) {
                $allErrors  = [];

                foreach ($push->getCommits() as $commit) {
                    if($commit['status'] === CommitStatus::ERROR) {
//                        $allErrors += $commit['label'];
                        $allErrors[] = $commit['label'];
                    }
                }

                if (count($allErrors) > 0) {
                    Log::channel(config('laravel-a-ray.notify_errors.channel'))->error('A Ray error : ' . $push->getLabel() . ' : ' . implode(', ', $allErrors));
                }
            }

        } catch (Exception $e) {
            if (config('laravel-a-ray.notify_errors.enabled')) {
                Log::channel(config('laravel-a-ray.notify_errors.channel'))->error($e->getMessage());
            }
        }

        return true;
    }

    public static function pushMultiple(array $pushes)
    {
        $payload = [];

        foreach ($pushes as $push) {
            $payload[] = $push->toJson();
        }

        try {
            $response = Http::post(self::$ENDPOINT_MULTIPLE . config('laravel-a-ray.private_key'), $payload);

            if ($response->status() !== 201) {
                throw new Exception('Error when push to a-ray');
            }
        } catch (\Exception $e) {
        }
    }
}
