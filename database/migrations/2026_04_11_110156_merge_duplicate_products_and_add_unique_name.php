<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Merge existing duplicate products before adding unique constraint
        // Find all duplicate names (case-insensitive)
        $duplicates = DB::table('products')
            ->select(DB::raw('LOWER(name) as lower_name'), DB::raw('MIN(id) as keep_id'), DB::raw('SUM(stock) as total_stock'))
            ->groupBy(DB::raw('LOWER(name)'))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            // Update the kept row with merged stock total
            DB::table('products')
                ->where('id', $dup->keep_id)
                ->update(['stock' => $dup->total_stock]);

            // Delete all other duplicates except the one we're keeping
            DB::table('products')
                ->whereRaw('LOWER(name) = ?', [$dup->lower_name])
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        // Step 2: Add unique constraint on name column
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};