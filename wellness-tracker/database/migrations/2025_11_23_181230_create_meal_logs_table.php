<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealLogsTable extends Migration
{
    public function up()
    {
        Schema::create('meal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->integer('calories')->default(0);
            $table->decimal('protein_g', 5, 2)->default(0);
            $table->decimal('carbs_g', 5, 2)->default(0);
            $table->decimal('fats_g', 5, 2)->default(0);
            $table->dateTime('eaten_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_logs');
    }
}