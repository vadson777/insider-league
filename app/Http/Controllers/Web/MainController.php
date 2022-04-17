<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Team;
use App\Services\FootballProbabilityService;
use App\Services\GamesService;
use Illuminate\Http\Request;

// @todo just temp for development testing
// @todo it should be a SPA
class MainController extends Controller
{

	public function home()
    {
		$teams = Team::withGamesStats()->get()->sortByDesc('points')->values();
	    $gamesCount = Game::count();
	    $curWeekGames = Game::withTeams()->currentWeek()->get();
	    $futureWeekGames = Game::withTeams()->futureWeek()->get();

		if($curWeekGames->first()?->week > 1) {
			app(FootballProbabilityService::class)
				->calculateTeamsChampionProbabilities($teams);
		}

	    return view('web.home', compact('teams', 'curWeekGames', 'futureWeekGames', 'gamesCount'));
	}

	public function generate(Request $request, GamesService $games)
	{
		$games->generateFixtures();

		return redirect()->back()
			->withMessage('Fixtures generated!', 'success');
	}

	public function playNext(Request $request, GamesService $games)
	{
		Game::withTeams()->currentWeek()->get()
			->each(fn(Game $game) => $games->simulate($game));

		return redirect()->back();
	}

	public function playAll(Request $request, GamesService $games)
	{
		Game::withTeams()->notPlayed()->get()
			->each(fn(Game $game) => $games->simulate($game));

		return redirect()->back();
	}

	public function reset(Request $request)
	{
		Game::query()->delete();

		return redirect()->back()
			->withMessage('Fixtures removed!');
	}

}
