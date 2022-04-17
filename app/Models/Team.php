<?php

namespace App\Models;

use App\Enums\GamePoint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{

	use HasFactory;

	protected $fillable = [
		'name', 'code', 'goals_stats',
	];

	protected $casts = [
		'goals_stats' => 'array',
	];

	public function code() : Attribute
	{
		return Attribute::make(
			set: fn($value) => \Str::upper($value)
		);
	}

	public function homeGames()
	{
		return $this->hasMany(Game::class, 'home_team_id');
	}

	public function guestGames()
	{
		return $this->hasMany(Game::class, 'guest_team_id');
	}

	public function scopeWithGames(Builder $builder)
	{
		$builder->with(['homeGames', 'guestGames']);
	}

	/**
	 * Not very effective, but we won't have large amount of data
	 * @param Builder $builder
	 * @return void
	 */
	public function scopeWithGamesStats(Builder $builder)
	{
		$cols = [
			'games_played' => 'count(*)',
			'games_wins' => 'sum(
				if(teams.id = home_team_id and home_team_goals > guest_team_goals, 1, 0) + 
				if(teams.id = guest_team_id and guest_team_goals > home_team_goals, 1, 0)
			)',
			'games_loses' => 'sum(
				if(teams.id = home_team_id and home_team_goals < guest_team_goals, 1, 0) + 
				if(teams.id = guest_team_id and guest_team_goals < home_team_goals, 1, 0)
			)',
			'games_drafts' => 'sum(if(home_team_goals = guest_team_goals, 1, 0))',
			'goals_for' => 'sum(if(teams.id = home_team_id, home_team_goals, guest_team_goals))',
			'goals_against' => 'sum(if(teams.id = home_team_id, guest_team_goals, home_team_goals))',
		];
		$cols['goals_diff'] = $cols['goals_for'].'-'.$cols['goals_against'];
		$cols['points'] = $cols['games_wins'].'*'.GamePoint::WIN.'+'.$cols['games_drafts'].'*'.GamePoint::DRAFT .'+'.$cols['games_loses'].'*'.GamePoint::LOSE;

		foreach($cols as $name => $raw) {
			$builder->addSelect([
				$name => Game::selectRaw($raw)->played()
					->whereRaw('teams.id in (home_team_id, guest_team_id)')
			]);
		}
	}

}