<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('episodes_watched', function (Blueprint $table) {
            $table->dropForeign(['serial_id']);
            $table->dropColumn('serial_id');
        });
    }

    public function down()
    {
        Schema::table('episodes_watched', function (Blueprint $table) {
            $table->foreignId('serial_id')->constrained()->after('episode_id');
        });
    }
};
