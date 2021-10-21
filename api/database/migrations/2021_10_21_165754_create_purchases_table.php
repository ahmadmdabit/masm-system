<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('receipt')->index('receipt_ndx');
            $table->tinyInteger('state', false, true)->nullable(); // 0: started, 1: renewed, 2: canceled
            $table->timestamp('last_check_at')->nullable();
            $table->boolean('status'); // ios / google verification status of the purchase
            $table->timestamp('expire_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'purchases_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign('purchases_user_id_foreign');
        });
        Schema::dropIfExists('purchases');
    }
}
