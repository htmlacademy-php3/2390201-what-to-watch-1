<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('serials_watched', 'serial_watching');
    }

    public function down()
    {
        Schema::rename('serial_watching', 'serials_watched');
    }
};
