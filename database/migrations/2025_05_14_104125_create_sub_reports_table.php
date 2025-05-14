<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubReportsTable extends Migration
{
    public function up()
    {
        Schema::create('sub_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade'); // Foreign key to reports table
            $table->string('zone');   // The zone for the subreport
            $table->string('brick_type');  // The brick type for the subreport
            $table->json('weights');  // Store the weights as JSON (array of numbers)
            $table->decimal('average_weight', 8, 2); // Average weight for the subreport
            $table->dateTime('datetime'); // Date and time of the subreport
            $table->string('shift');  // Shift for the subreport
            $table->string('username'); // Username of the person who created the subreport
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_reports');
    }
}
