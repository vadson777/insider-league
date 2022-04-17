<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{

	public function definition() : array
	{
		return [
			'name' => $this->faker->company,
			'code' => \Str::upper($this->faker->randomLetter.$this->faker->randomLetter.$this->faker->randomLetter),
			'goals_stats' => [
				'home_for' => mt_rand(70, 250)/100,
				'home_against' => mt_rand(70, 250)/100,
				'guest_for' => mt_rand(70, 250)/100,
				'guest_against' => mt_rand(70, 250)/100,
			],
		];
	}

}