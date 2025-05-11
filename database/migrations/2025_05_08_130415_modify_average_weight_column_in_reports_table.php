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
        Schema::table('reports', function (Blueprint $table) {
            $table->decimal('average_weight', 15, 2)->change(); // Adjust precision as needed
        });
    }
    /**
     * Reverse the migrations.
     */

     public function down(): void
     {
         Schema::table('reports', function (Blueprint $table) {
             $table->decimal('average_weight', 8, 2)->change(); // Revert back to original precision
         });
     }
};
