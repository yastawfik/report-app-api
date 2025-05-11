<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->string('zone');
        $table->string('brick_type');
        // database/migrations/xxxx_xx_xx_create_reports_table.php

$table->json('weights')->nullable();
 // This stores the array of weights
        $table->float('average');
        $table->timestamp('datetime');
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('reports');
    }

};
