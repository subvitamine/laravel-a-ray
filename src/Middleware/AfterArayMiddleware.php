<?php

namespace Subvitamine\LaravelARay\Middleware;

use Closure;
use Illuminate\Http\Response;
use Subvitamine\LaravelARay\Models\ArayRequest;

class AfterArayMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $config = config('laravel-a-ray');
        if ($config['api_health']['enabled']) {
            /** @var Response $response */

            /** @var array $requestData */
            $requestData = session()->get('laravel-a-ray');

            $responseData = [];

            if (!in_array($requestData['route'], $config['api_health']['routes_without_request_response'])) {
                if ($response->headers->all()['content-type'][0] == 'application/json') {
                    $responseData['response'] = json_decode($response->getContent(), true);
                } else {
                    $responseData['response'] = 'Not JSON -> ' . $response->headers->all()['content-type'][0];
                }
            }

            if ($response->status() ?? null) {
                $responseData['status'] = '' . $response->status();
            }

            $responseData['endAt'] = now()->format('Y-m-d H:i:s.u');

            try {
                ArayRequest::create([
                    'request' => $requestData,
                    'response' => $responseData
                ]);
            } catch (\Exception $e) {
                dump($e);
            }
        }

        return $next($request);
    }
}
