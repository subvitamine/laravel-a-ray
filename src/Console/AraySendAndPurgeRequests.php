<?php

namespace Subvitamine\LaravelARay\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Subvitamine\LaravelARay\ARay;
use Subvitamine\LaravelARay\CommitStatus;
use Subvitamine\LaravelARay\Models\ArayRequest;

class AraySendAndPurgeRequests extends Command
{
    protected $signature = 'aray:requests-push-purge';

    protected $description = 'Send all requests to A-Ray and purge the database';

    public function handle()
    {
        $this->info('Send pushes...');

        $allRequests = ArayRequest::all();
        ArayRequest::truncate();

        $arayPushes = [];

        foreach ($allRequests as $request) {

            $push = ARay::initPush("Request", Carbon::createFromFormat('Y-m-d H:i:s.u', $request->request['startAt']) ?? null);

            $push->setType('REQUEST');
            $push->setMeta([
                'route' => $request->request['route'] ?? null,
                'method' => $request->request['method'] ?? null,
                'url' => $request->request['url'] ?? null,
            ]);

            $push->setStartAt(Carbon::createFromFormat('Y-m-d H:i:s.u', $request->request['startAt']) ?? null);
            $push->setEndAt(Carbon::createFromFormat('Y-m-d H:i:s.u', $request->response['endAt']) ?? null);

            $push->addCommit('Request', $request->request ?? null, CommitStatus::INFO, Carbon::createFromFormat('Y-m-d H:i:s.u', $request->request['startAt']) ?? null);
            $push->addCommit('Response', $request->response ?? null, CommitStatus::INFO, Carbon::createFromFormat('Y-m-d H:i:s.u', $request->response['endAt']) ?? null);

            $arayPushes[] = $push;
        }

        ARay::pushMultiple($arayPushes);

        $this->info('End sended...');

    }
}
