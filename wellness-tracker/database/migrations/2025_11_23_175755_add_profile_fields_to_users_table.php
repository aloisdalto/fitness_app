<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->integer('height_cm')->nullable(); // Estatura en cm
            $table->decimal('weight_kg', 5, 2)->nullable(); // Peso
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age', 'height_cm', 'weight_kg', 'gender']);
        });
    }
}