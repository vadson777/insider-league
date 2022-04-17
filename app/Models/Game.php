<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Game extends Model
{

	protected $fillable = [
		'week', 'home_team_id', 'guest_team_id',
		'home_team_goals', 'guest_team_goals',
	];

	protected $casts = [
		'week' => 'integer',
		'home_team_id' => 'integer',
		'guest_team_id' => 'integer',
		'home_team_goals' => 'integer',
		'guest_team_goals' => 'integer',
	];

	public function played() : Attribute
	{
		return Attribute::make(get: function() {
			return !is_null($this->home_team_goals)
				|| !is_null($this->guest_team_goals);
		});
	}

	public function homeTeam()
	{
		return $this->belongsTo(Team::class, 'home_team_id');
	}

	public function guestTeam()
	{
		return $this->belongsTo(Team::class, 'guest_team_id');
	}

	public function scopePlayed(Builder $builder)
	{
		$builder->whereNotNull('home_team_goals');
	}

	public function scopeNotPlayed(Builder $builder)
	{
		$builder->whereNull('home_team_goals');
	}

	public function scopeWithTeams(Builder $builder)
	{
		$builder->with(['homeTeam', 'guestTeam']);
	}

	public function scopeFutureWeek(Builder $builder)
	{
		$builder->notPlayed()
			->where('week', self::notPlayed()->min('week') + 1)
			->orderBy($this->getKeyName(), 'asc');
	}

	public function scopeCurrentWeek(Builder $builder)
	{
		$builder->notPlayed()
			->where('week', self::notPlayed()->min('week'))
			->orderBy($this->getKeyName(), 'asc');
	}

}