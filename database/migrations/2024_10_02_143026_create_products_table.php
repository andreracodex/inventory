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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade'); // Assuming each product has a unit
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->index('category_id');
            $table->index('unit_id');
            $table->timestamps();
            $table->unsignedInteger('minimal_stock')->default(1);
            $table->unsignedInteger('stock')->nullable();
            $table->integer('is_active')->default(1);
            $table->text('url_images')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
