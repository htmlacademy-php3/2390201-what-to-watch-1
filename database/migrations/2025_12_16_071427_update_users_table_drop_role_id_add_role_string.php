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
    Schema::table('users', function (Blueprint $table) {
      // Удаляем внешний ключ, если он был
      if (Schema::hasColumn('users', 'role_id')) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
      }

      // Добавляем новое поле role
      $table->string('role')->default(\App\Constants\UserRole::USER);
    });

    // Удаляем таблицу roles, если она существует
    Schema::dropIfExists('roles');
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::create('roles', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->timestamps();
    });

    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('role');
      $table->foreignId('role_id')->nullable()->constrained();
    });
  }
};

