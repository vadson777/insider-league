@extends('layout')

@section('body')
	<h1>{{ config('app.name') }} Simulator</h1>

	@if(session()->has('message'))
		<div class="alert alert-{{ session()->get('message.type', 'primary') }}" role="alert">
			{{ session()->get('message.text') }}
		</div>
	@endif

	@if($teams->isNotEmpty())
		<div class="row">
			<div class="col-12 col-xl-6">
				<table class="table table-bordered table-hover align-middle">
					<thead>
						<tr class="table-dark">
							<th style="width:20px;">#</th>
							<th>Team</th>
							<th class="text-center" style="width:50px;" title="Games played">P</th>
							<th class="text-center" style="width:50px;" title="Wins">W</th>
							<th class="text-center" style="width:50px;" title="Drafts">D</th>
							<th class="text-center" style="width:50px;" title="Loses">L</th>
							<th class="text-center" style="width:50px;" title="Goals For">GF</th>
							<th class="text-center" style="width:50px;" title="Goals Against">GA</th>
							<th class="text-center" style="width:50px;" title="Goals Diff">GD</th>
							<th class="text-center" style="width:80px;">Points</th>
						</tr>
					</thead>
					<tbody>
						@foreach($teams->sortByDesc('points')->values() as $key => $team)
							<tr>
								<td>{{ $key+1 }}</td>
								<td>{{ $team->name }}</td>
								<td class="text-center">{{ $team->games_played ?: '0' }}</td>
								<td class="text-center">{{ $team->games_played ? $team->games_wins : '-' }}</td>
								<td class="text-center">{{ $team->games_played ? $team->games_drafts : '-' }}</td>
								<td class="text-center">{{ $team->games_played ? $team->games_loses : '-' }}</td>
								<td class="text-center">{{ $team->games_played ? $team->goals_for : '-' }}</td>
								<td class="text-center">{{ $team->games_played ? $team->goals_against : '-' }}</td>
								<td class="text-center">{{ $team->games_played ? ($team->goals_diff > 0 ? '+' : '').$team->goals_diff : '-' }}</td>
								<td class="text-center fw-bold">{{ $team->games_played ? $team->points : '-' }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			@if($curWeekGames->isNotEmpty())
				<div class="col-12 col-md-6 col-xl-3">
					<table class="table table-bordered table-hover align-middle">
						<thead>
							<tr class="table-dark">
								<th colspan="2">Next week: {{ $curWeekGames->first()->week }}</th>
							</tr>
						</thead>
						<tbody>
						@foreach($curWeekGames as $game)
							<tr>
								<td class="text-end w-50">{{ $game->homeTeam->name }}</td>
								<td class="text-start w-50">{{ $game->guestTeam->name }}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			@endif

			@if($gamesCount && $curWeekGames->isNotEmpty())
				<div class="col-12 col-md-6 col-xl-3">
					<table class="table table-bordered table-hover align-middle">
						<thead>
						<tr class="table-dark">
							<th>Championship Predictions</th>
							<th class="text-center" style="width:80px;">%</th>
						</tr>
						</thead>
						<tbody>
							@foreach($teams->sortByDesc('champion_probability')->values() as $key => $team)
								<tr>
									<td>{{ $team->name }}</td>
									<td class="text-end">
										@if(!is_null($team->champion_probability))
											{{ sprintf("%01.2f", 100*($team->champion_probability ?? 0)) }}%
										@else
											-
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</div>
		<div class="mt-3">
			@if($gamesCount)
				@if($curWeekGames->isNotEmpty())
					<form method="post"
					      action="{{ route('play_all') }}"
						  class="d-inline-block me-2">
						{{ csrf_field() }}
						<button type="submit"
						        class="btn btn-info">
							Play all weeks
						</button>
					</form>
					<form method="post"
					      action="{{ route('play_next') }}"
					      class="d-inline-block me-2">
						{{ csrf_field() }}
						<button type="submit"
						        class="btn btn-info">
							Play next week
						</button>
					</form>
				@else
					<button type="button"
					        class="btn btn-info me-2"
					        disabled>
						Play all weeks
					</button>
					<button type="button"
					        class="btn btn-info me-2"
							disabled>
						Play next week
					</button>
				@endif

				<form method="post"
				      action="{{ route('reset') }}"
				      class="d-inline-block me-2"
				      onsubmit="return confirm('Are you sure?')">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}
					<button type="submit"
					        class="btn btn-danger">
						Reset
					</button>
				</form>
			@else
				<form method="post"
				      action="{{ route('generate') }}"
				      class="d-inline-block me-2">
					{{ csrf_field() }}
					<button type="submit"
					        class="btn btn-info">
						Generate Fixtures
					</button>
				</form>
			@endif
		</div>
	@endif
@endsection