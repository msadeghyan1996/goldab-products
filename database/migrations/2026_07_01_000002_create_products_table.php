<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->unsignedBigInteger('code_sequence');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('main_image')->nullable();
            $table->decimal('weight', 12, 3)->nullable();
            $table->string('weight_unit', 10)->default('گرم');
            $table->decimal('wage_amount', 15, 2)->nullable();
            $table->decimal('wage_percentage', 5, 2)->nullable();
            $table->string('availability', 20)->default('available')->index();
            $table->timestamps();

            $table->unique(['category_id', 'code_sequence']);
            $table->index(['category_id', 'availability']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
