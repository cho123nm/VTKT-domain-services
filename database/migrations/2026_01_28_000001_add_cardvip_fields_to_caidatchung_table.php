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
        Schema::table('caidatchung', function (Blueprint $table) {
            $table->string('cardvip_partner_id', 255)->nullable()->after('callback')->comment('CardVIP Partner ID');
            $table->string('cardvip_partner_key', 255)->nullable()->after('cardvip_partner_id')->comment('CardVIP Partner Key');
            $table->string('cardvip_api_url', 500)->nullable()->after('cardvip_partner_key')->comment('CardVIP API URL');
            $table->string('cardvip_callback', 500)->nullable()->after('cardvip_api_url')->comment('CardVIP Callback URL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caidatchung', function (Blueprint $table) {
            $table->dropColumn(['cardvip_partner_id', 'cardvip_partner_key', 'cardvip_api_url', 'cardvip_callback']);
        });
    }
};

