<?php

namespace Subvitamine\LaravelARay\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BeforeArayMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $config = config('laravel-a-ray');

        if ($config['api_health']['enabled']) {
            try {
                $data = [];

                // Push to data startAt
                $data['startAt'] = Carbon::now()->format('Y-m-d H:i:s.u');

                if ($rr = $request->headers->all()['origin'] ?? null) {
                    $data['headers'] = $rr;
                }

                $method = $request->method();
                $url = $request->url();

                $data['url'] = $url;
                $data['method'] = $method;

                if (app('request')->create($url, $method) ?? null) {
                    $data['route'] = app('router')->getRoutes()->match(app('request')->create($url, $method))->action['as'];
                }

                if ($request->getContent() ?? null) {
                    if(!in_array($data['route'], $config['api_health']['routes_without_request_response'])) {
                        if ($request->headers->all()['content-type'][0] == 'application/json') {

                            $editedRequest = json_decode($request->getContent(), true);

                            foreach (array_keys($editedRequest) as $_key) {
                                if(in_array($_key, $config['api_health']['except_fields'])) {
                                    $editedRequest[$_key] = '********';
                                };
                            }

                            $data['request'] = $editedRequest;
                        } else {
                            $data['request'] = 'Not JSON -> ' . $request->headers->all()['content-type'][0];
                        }
                    }
                } else {
                    $data['request'] = 'No content';
                }

                session()->regenerate();
                session()->put('laravel-a-ray', $data);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return $next($request);
    }
}
