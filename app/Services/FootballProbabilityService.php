<?php

namespace App\Services;

use App\Enums\GamePoint;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Array_;

class FootballProbabilityService
{

	// Goals prediction range
	private const GOALS_MIN = 0;
	private const GOALS_MAX = 7;

	// Average goals count by previous seasons
	private const GOALS_HOME_FOR = 1.49;
	private const GOALS_HOME_AGAINST = 1.21;
	private const GOALS_GUEST_FOR = 1.21;
	private const GOALS_GUEST_AGAINST = 1.49;

	public function teamWinProbability(Team $team, Team $rival) : float
	{
		return $this->getScoreProbabilities($team, $rival)
			->reduce(function($res, $prob, $score) {
				list($i, $j) = explode(':', $score);
				return ($res + ($i > $j ? $prob : 0));
			}, 0);
	}

	public function teamDraftProbability(Team $team, Team $rival) : float
	{
		return $this->getScoreProbabilities($team, $rival)
			->reduce(function($res, $prob, $score) {
				list($i, $j) = explode(':', $score);
				return ($res + ($i == $j ? $prob : 0));
			}, 0);
	}

	public function teamLoseProbability(Team $team, Team $rival) : float
	{
		return $this->getScoreProbabilities($team, $rival)
			->reduce(function($res, $prob, $score) {
				list($i, $j) = explode(':', $score);
				return ($res + ($i < $j ? $prob : 0));
			}, 0);
	}

	/**
	 * @param Team $team
	 * @param Team $rival
	 * @return Collection<string, float>
	 */
	public function getScoreProbabilities(Team $team, Team $rival) : Collection
	{
		$teamGoalsFor = $team->goals_stats['home_for']/self::GOALS_HOME_FOR;
		$rivalGoalsAgainst = $rival->goals_stats['guest_against']/self::GOALS_GUEST_AGAINST;
		$teamGoalsExpected = $teamGoalsFor*$rivalGoalsAgainst*self::GOALS_HOME_FOR;

		$rivalGoalsFor = $rival->goals_stats['guest_for']/self::GOALS_GUEST_FOR;
		$teamGoalsAgainst = $team->goals_stats['home_against']/self::GOALS_HOME_AGAINST;
		$rivalGoalsExpected = $rivalGoalsFor*$teamGoalsAgainst*self::GOALS_GUEST_FOR;

		$teamGoalsDistr = $this->getGoalsDistribution($teamGoalsExpected);
		$rivalGoalsDistr = $this->getGoalsDistribution($rivalGoalsExpected);

		$prob = collect();
		for($i = 0; $i < $teamGoalsDistr->count(); $i++) {
			for($j = 0; $j < $rivalGoalsDistr->count(); $j++) {
				$prob->put($i.':'.$j, $teamGoalsDistr->get($i) * $rivalGoalsDistr->get($j));
			}
		}

		return $prob;
	}

	/**
	 * @param float $goalsMean
	 * @return Collection<int, float>
	 */
	protected function getGoalsDistribution(float $goalsMean) : Collection
	{
		if($goalsMean < 0) {
			throw new \InvalidArgumentException('Goals mean number can not be negative');
		}

		return collect(range(self::GOALS_MIN, self::GOALS_MAX))
			->mapWithKeys(fn($goals) => [$goals => poisson_distribution($goals, $goalsMean)]);
	}

	/**
	 * @param Collection<Team>|array<Team> $teams
	 * @return Collection
	 */
	public function calculateTeamsChampionProbabilities($teams) : Collection
	{
		if(!$teams) {
			$teams = Team::withGamesStats()->get();
		} else {
			$teams = collect($teams);
		}

		$teams = $teams->sortByDesc('points');
		$maxPoints = $teams->first()->points;

		$games = Game::withTeams()->notPlayed()->get();
		$weeksLeft = $games->max('week') - $games->min('week') + 1;

		// If we have already a champion(s) by points
		if($maxPoints > $teams->firstWhere('points', '<', $maxPoints)->points+$weeksLeft*GamePoint::WIN) {
			$maxPointsCount = $teams->where('points', '=', $maxPoints)->count();
			$teams->each(function(Team $team, $key) use ($maxPoints, $maxPointsCount) {
				$team->champion_probability = $team->points == $maxPoints ? 1/$maxPointsCount : 0;
			});
		} else {
			// Check teams that will never reach a leader team
			$teams->each(function (Team $team) use ($weeksLeft, $teams) {
				if ($team->points + $weeksLeft * GamePoint::WIN < $teams->first()->points) {
					$team->champion_probability = 0;
				}
			});

			// Make simulations and calculate probabilities
			$gamesService = app(GamesService::class);
			$simulationsCount = 30;
			$standings = collect();
			for($s = 1; $s <= $simulationsCount; $s++) {
				// Current points
				$iStandings = $teams->mapWithKeys(fn(Team $team) => [$team->id => $team->points]);

				$teams->each(function (Team $team) use ($games, $iStandings, $gamesService) {
					if(is_null($team->champion_probability)) {
						$teamGames = $games->where('home_team_id', $team->id)
							->merge($games->where('guest_team_id', $team->id));

						foreach ($teamGames as $game) {
							$gamesService->simulate($game, false);
							if ($game->home_team_goals == $game->guest_team_goals) {
								$iStandings->put(
									$game->homeTeam->id,
									$iStandings->get($game->homeTeam->id) + GamePoint::DRAFT
								);
								$iStandings->put(
									$game->guestTeam->id,
									$iStandings->get($game->guestTeam->id) + GamePoint::DRAFT
								);
							} elseif ($game->home_team_goals > $game->guest_team_goals) {
								$iStandings->put(
									$game->homeTeam->id,
									$iStandings->get($game->homeTeam->id) + GamePoint::WIN
								);
							} else {
								$iStandings->put(
									$game->guestTeam->id,
									$iStandings->get($game->guestTeam->id) + GamePoint::WIN
								);
							}
						}
					}
				});

				$standings->push($iStandings->sortDesc()->keys()->first());
			}

			$winnersCount = collect();
			foreach($standings as $team_id) {
				$winnersCount->put($team_id, $winnersCount->get($team_id) + 1);
			}

			foreach($teams as $team) {
				if(is_null($team->champion_probability)) {
					$team->champion_probability = $winnersCount->get($team->id) / $simulationsCount;
				}
			}
		}

		return $teams;
	}

}