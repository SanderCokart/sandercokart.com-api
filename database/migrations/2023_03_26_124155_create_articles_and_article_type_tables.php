<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('excerpt');
            $table->string('slug');
            $table->longText('body');
            $table->string('banner');
            $table->softDeletes();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();

            $table->foreignId('article_type_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_types');
    }
};
