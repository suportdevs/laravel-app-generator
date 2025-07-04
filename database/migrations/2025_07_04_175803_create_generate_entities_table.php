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
        Schema::create('generate_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('table_name');
            $table->json('fields'); // will store all feilds with types, validation etc.
            $table->foreignId('application_id')->constrained();
            $table->timestamps();
            $table->foreignId('created_by')->constrained();
            $table->foreignId('updated_by')->constrained();
            $table->foreignId('deleted_by')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generate_entities');
    }
};
