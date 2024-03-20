<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->string('slug')->nullable();
            $table->string('env', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->json('settings')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['slug', 'env', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
