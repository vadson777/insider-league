<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{

	private const TEAMS = [
		[
			'name' => 'Manchester City',
			'code' => 'MCI',
			'goals_stats' => [
				'home_for' => 43/19,
				'home_against' => 17/19,
				'guest_for' => 40/19,
				'guest_against' => 15/19,
			],
		],
		[
			'name' => 'Liverpool',
			'code' => 'LIV',
			'goals_stats' => [
				'home_for' => 29/19,
				'home_against' => 20/19,
				'guest_for' => 39/19,
				'guest_against' => 22/19,
			],
		],
		[
			'name' => 'Chelsea',
			'code' => 'CHE',
			'goals_stats' => [
				'home_for' => 31/19,
				'home_against' => 18/19,
				'guest_for' => 27/91,
				'guest_against' => 18/19,
			],
		],
		[
			'name' => 'Tottenham',
			'code' => 'TOT',
			'goals_stats' => [
				'home_for' => 35/19,
				'home_against' => 20/19,
				'guest_for' => 33/19,
				'guest_against' => 25/19,
			],
		],
	];

	public function run()
	{
		foreach(self::TEAMS as $team) {
			Team::create($team);
		}
	}

}