<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('category', 100);
            $table->jsonb('images');

            $table->foreignId('created_by_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('created_by', 100);

            $table->foreignId('updated_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('updated_by', 100)->nullable();

            $table->timestamps();

            $table->index('category');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products'); 
    }
};