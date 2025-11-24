<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExerciseTables extends Migration
{
    public function up()
    {
        // 1. Rutinas
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); 
            $table->text('description')->nullable();
            
            // CAMBIO: Usamos 'text' en lugar de 'json' para compatibilidad con MariaDB antiguo
            $table->text('days_of_week')->nullable(); 
            
            $table->timestamps();
        });

        // 2. Ejercicios
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_id')->nullable(); 
            $table->string('muscle_group')->nullable();
            $table->timestamps();
        });

        // 3. Pivote (Rutina <-> Ejercicios)
        Schema::create('exercise_routine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('suggested_sets')->default(3);
            $table->string('suggested_reps')->default('10-12');
        });

        // 4. Sesiones (Historial)
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('routine_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('duration_seconds')->default(0); 
            $table->text('comments')->nullable(); 
            $table->timestamp('completed_at'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workout_sessions');
        Schema::dropIfExists('exercise_routine');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('routines');
    }
}