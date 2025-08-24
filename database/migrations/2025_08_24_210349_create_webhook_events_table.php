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
    Schema::create('webhook_events', function (Blueprint $table) {
        $table->id();
        $table->string('event_type')->nullable();
        $table->string('celular')->nullable();
        $table->string('name')->nullable();
        $table->string('api_phone_id')->nullable();
        $table->string('api_phone_number')->nullable();
        $table->text('message')->nullable();
        $table->string('interactive_id')->nullable();
        $table->string('status')->nullable();
        $table->string('status_id')->nullable();
        $table->json('conversation')->nullable();
        $table->string('message_id')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
