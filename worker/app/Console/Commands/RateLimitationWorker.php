<?php

namespace App\Console\Commands;

use DateTime;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use App\Jobs\UpdateFailedPurchaseByRateLimitJob;

class RateLimitationWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RateLimitationWorker:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Worker for updating the subscriptions states of the faileds by rate limitation and the ios/google verification.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $purchases = DB::select(
            'SELECT pr.id, pr.receipt, dv.os, NULL as status, NULL as expire_date, NULL as is_rate_limit FROM `purchases` as pr
            INNER JOIN `devices` as dv ON pr.user_id = dv.user_id
            WHERE pr.`status` = 0 AND pr.`state` <> 2 AND pr.`is_rate_limit` = 1;',
            [(new DateTime())->format('Y-m-d H:i:s')]
        );
        $collection = collect($purchases);
        $purchases = null;
        $chunks = $collection->chunk(10)->all();
        $collection = null;
        $this->warn('n of collected garbage cycles: '. @gc_collect_cycles());
        foreach ($chunks as $k => $purchasesChunk) {
            $this->warn($k);
            foreach ($purchasesChunk as $key => $purchase) {
                $this->info($key);
                $verificationResult = $this->mockingVerification($purchase->receipt, $purchase->os);
                $purchasesChunk[$key]->status = $verificationResult['status'];
                $purchasesChunk[$key]->expire_date = $verificationResult['expire_date'];
                $purchasesChunk[$key]->state = $verificationResult['status'] ? 1 : null; // state: 1 renewed
                $purchasesChunk[$key]->is_rate_limit = !$verificationResult['status'] && isset($verificationResult['data']) && $verificationResult['data']['error_code'] == 429;
            }
            // Passe this chunk ($purchasesChunk) to the queue
            // for doing the update in DB::transaction and bulk update script
            $this->info('UpdateFailedPurchaseByRateLimitJob::dispatch');
            Queue::push(new UpdateFailedPurchaseByRateLimitJob($purchasesChunk));
        }

        $this->info('The command was successful!');

        return 0;
    }

    /**
     * Mocking the purchase verification.
     *
     * @param  string $receipt
     * @param  int $os: 0: ios, 1:google
     * @return mixed
     */
    protected function mockingVerification(string $receipt, int $os)
    {
        $status = 0;
        $expire_date = null;
        $error = null;
        try {
            $client = new Client();
            $result = $client->post(env('MOCKING_ENDPOINT').'/purchases/verification/'.($os == 0 ? 'ios' : 'google'),[
                'form_params' => [
                    "receipt" => $receipt,
                ]
            ]);

            if (isset($result)) {
                $status = json_decode($result->getBody(), false)->status ? 1 : 0;
                $expire_date = json_decode($result->getBody(), false)->data->expire_date;
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return [
            'status' => $status,
            'expire_date' => $expire_date,
            'error' => $error,
        ];
    }
}
