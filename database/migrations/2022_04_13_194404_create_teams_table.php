<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() : void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
			$table->string('name')->unique();
			$table->char('code', 3)->unique();
			$table->json('goals_stats')->default(null);
            $table->timestamps();
        });
    }


    public function down() : void
    {
        Schema::dropIfExists('teams');
    }

};
