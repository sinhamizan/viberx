<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_setup_intent_id')->nullable()->after('current_step');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_setup_intent_id');
            $table->string('card_brand')->nullable()->after('stripe_payment_method_id');
            $table->string('card_last_four', 4)->nullable()->after('card_brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_setup_intent_id',
                'stripe_payment_method_id',
                'card_brand',
                'card_last_four',
            ]);
        });
    }
};
