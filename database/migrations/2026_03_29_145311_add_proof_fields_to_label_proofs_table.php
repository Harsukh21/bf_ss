<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('label_proofs', function (Blueprint $table) {
            $table->foreignId('whitelabel_id')->nullable()->constrained('label_whitelabels')->nullOnDelete()->after('proof_type_id');
            $table->string('whatsapp_group')->nullable()->after('whitelabel_id');
            $table->string('agent_name')->nullable()->after('whatsapp_group');
            $table->string('user_name')->nullable()->after('agent_name');
            $table->decimal('amount', 15, 2)->nullable()->after('user_name');
            $table->foreignId('sport_id')->nullable()->constrained('label_sports')->nullOnDelete()->after('amount');
            $table->string('event_name')->nullable()->after('sport_id');
            $table->string('market_name')->nullable()->after('event_name');
            $table->decimal('profit_loss', 15, 2)->nullable()->after('market_name');
            $table->date('proof_date')->nullable()->after('profit_loss');
            $table->text('navigation')->nullable()->after('proof_date');
            $table->json('images')->nullable()->after('navigation');
            $table->text('navigation2')->nullable()->after('images');
            $table->json('navigation2_images')->nullable()->after('navigation2');
        });
    }

    public function down(): void
    {
        Schema::table('label_proofs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('whitelabel_id');
            $table->dropConstrainedForeignId('sport_id');
            $table->dropColumn([
                'whatsapp_group','agent_name','user_name','amount',
                'event_name','market_name','profit_loss','proof_date',
                'navigation','images','navigation2','navigation2_images',
            ]);
        });
    }
};
