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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
                $table->string('name');
                $table->string('email');
                $table->text('text');
                $table->boolean('is_approved')->default(false);
                $table->foreignUuid('post_id')->constrained('posts')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
