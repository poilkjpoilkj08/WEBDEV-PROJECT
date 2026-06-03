<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert all 'completed' status to 'approved'
        DB::table('refunds')->where('status', 'completed')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'approved' back to 'completed' (only those that were originally completed)
        // Note: This is a best-effort approach since we can't guarantee which ones were converted
        // It's safer to not revert in this case
    }
};
