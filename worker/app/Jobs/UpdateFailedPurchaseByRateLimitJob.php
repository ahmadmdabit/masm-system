<?php

namespace App\Jobs;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UpdateFailedPurchaseByRateLimitJob extends Job
{
    protected $purchases;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($purchases)
    {
        $this->purchases = $purchases;
        // $this->onQueue('UpdateFailedPurchaseByRateLimitJob');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "UpdateFailedPurchaseByRateLimitJob STARTED \n\r";
        $result = false;
        try {
            if (isset($this->purchases) && count($this->purchases)) {
                DB::beginTransaction();
                foreach ($this->purchases as $k => $purchase) {
                    DB::update("UPDATE `purchases` SET
                        `status` = ".$purchase->status.",
                        `state` = ".($purchase->state?1:0).",
                        `expire_date` = ".(is_null($purchase->expire_date) ? "NULL" : "'$purchase->expire_date'").",
                        `is_rate_limit` = ".($purchase->is_rate_limit?1:0)."
                    WHERE `id` = ".$purchase->id.";
                    ");
                }
                DB::commit();
                $result = true;
            echo "UpdateFailedPurchaseByRateLimitJob COMMITTED \n\r";
        }
        } catch (\Throwable $e) {
            $result = false;
            Log::error("UpdateFailedPurchaseByRateLimitJob: ".$e->getMessage());
            echo "UpdateFailedPurchaseByRateLimitJob ROLLBACKING \n\r";
            DB::rollBack();
        }
        return $result;
    }

    // /**
    //  * Get the cache driver for the unique job lock.
    //  *
    //  * @return \Illuminate\Contracts\Cache\Repository
    //  */
    // public function uniqueVia()
    // {
    //     return Cache::driver('redis');
    // }
}
