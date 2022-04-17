<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Collection;

class GamesService
{

	/**
	 * @param array<Team>|Collection<Team>|null $teams
	 * @return Collection
	 * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
	 */
	public function generateFixtures($teams = null) : Collection
	{
		if(!$teams) {
			$teams = Team::all();
		} else {
			$teams = collect($teams)->values();
		}

		$count = $teams->count();
		if($count < 2 || $count % 2 != 0) {
			throw new \InvalidArgumentException('Teams count should be even and greater or equal than 2');
		}

		$roundsCount = $count - 1;
		$matchesPerRound = $count / 2;

		\DB::beginTransaction();

		$games = collect();
		for($round = 0; $round < $roundsCount; $round++) {
			for($match = 0; $match < $matchesPerRound; $match++) {
				$tkA = ($round + $match) % $roundsCount;

				if($match == 0) {
					$tkB = $roundsCount;
				} else {
					$tkB = ($roundsCount - $match + $round) % $roundsCount;
				}

				$games->push(
					Game::updateOrCreate([
						'home_team_id' => $teams->get($tkA)->id,
						'guest_team_id' => $teams->get($tkB)->id,
					], [
						'week' => $round + 1
					]),
					Game::updateOrCreate([
						'home_team_id' => $teams->get($tkB)->id,
						'guest_team_id' => $teams->get($tkA)->id,
					], [
						'week' => $round + 1 + $roundsCount
					]),
				);
			}
		}

		\DB::commit();

		return $games;
	}

	public function simulate(Game $game, bool $store = true) : Game
	{
		$probs = app(FootballProbabilityService::class)
			->getScoreProbabilities($game->homeTeam, $game->guestTeam);

		$home_team_goals = $guest_team_goals = null;
		$rand = mt_rand(0, ceil($probs->sum()*1000000))/1000000;
		foreach($probs as $score => $prob) {
			if (($rand -= $prob) <= 0) {
				list($home_team_goals, $guest_team_goals) = explode(':', $score);
				break;
			}
		}

		$game->fill(compact('home_team_goals', 'guest_team_goals'));

		if($store) {
			$game->save();
		}

		return $game;
	}

}