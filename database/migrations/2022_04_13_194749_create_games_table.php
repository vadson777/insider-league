<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() : void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
			$table->unsignedTinyInteger('week')->index();
			$table->unsignedBigInteger('home_team_id');
	        $table->unsignedTinyInteger('home_team_goals')->nullable()->index();
	        $table->unsignedBigInteger('guest_team_id');
	        $table->unsignedTinyInteger('guest_team_goals')->nullable()->index();
            $table->timestamps();

			$table->unique(['home_team_id', 'guest_team_id']);
			$table->foreign('home_team_id')->on('teams')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign('guest_team_id')->on('teams')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('games');
    }

};
