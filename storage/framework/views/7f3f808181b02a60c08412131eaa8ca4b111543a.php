<?php $__env->startSection('body'); ?>
	<h1><?php echo e(config('app.name')); ?> Simulator</h1>

	<?php if(session()->has('message')): ?>
		<div class="alert alert-<?php echo e(session()->get('message.type', 'primary')); ?>" role="alert">
			<?php echo e(session()->get('message.text')); ?>

		</div>
	<?php endif; ?>

	<?php if($teams->isNotEmpty()): ?>
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
						<?php $__currentLoopData = $teams->sortByDesc('points')->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key+1); ?></td>
								<td><?php echo e($team->name); ?></td>
								<td class="text-center"><?php echo e($team->games_played ?: '0'); ?></td>
								<td class="text-center"><?php echo e($team->games_played ? $team->games_wins : '-'); ?></td>
								<td class="text-center"><?php echo e($team->games_played ? $team->games_drafts : '-'); ?></td>
								<td class="text-center"><?php echo e($team->games_played ? $team->games_loses : '-'); ?></td>
								<td class="text-center"><?php echo e($team->games_played ? $team->goals_for : '-'); ?></td>
								<td class="text-center"><?php echo e($team->games_played ? $team->goals_against : '-'); ?></td>
								<td class="text-center"><?php echo e($team->games_played ? ($team->goals_diff > 0 ? '+' : '').$team->goals_diff : '-'); ?></td>
								<td class="text-center fw-bold"><?php echo e($team->games_played ? $team->points : '-'); ?></td>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
			</div>

			<?php if($curWeekGames->isNotEmpty()): ?>
				<div class="col-12 col-md-6 col-xl-3">
					<table class="table table-bordered table-hover align-middle">
						<thead>
							<tr class="table-dark">
								<th colspan="2">Next week: <?php echo e($curWeekGames->first()->week); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php $__currentLoopData = $curWeekGames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="text-end w-50"><?php echo e($game->homeTeam->name); ?></td>
								<td class="text-start w-50"><?php echo e($game->guestTeam->name); ?></td>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>

			<?php if($gamesCount && $curWeekGames->isNotEmpty()): ?>
				<div class="col-12 col-md-6 col-xl-3">
					<table class="table table-bordered table-hover align-middle">
						<thead>
						<tr class="table-dark">
							<th>Championship Predictions</th>
							<th class="text-center" style="width:80px;">%</th>
						</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $teams->sortByDesc('champion_probability')->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($team->name); ?></td>
									<td class="text-end">
										<?php if(!is_null($team->champion_probability)): ?>
											<?php echo e(sprintf("%01.2f", 100*($team->champion_probability ?? 0))); ?>%
										<?php else: ?>
											-
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
		<div class="mt-3">
			<?php if($gamesCount): ?>
				<?php if($curWeekGames->isNotEmpty()): ?>
					<form method="post"
					      action="<?php echo e(route('play_all')); ?>"
						  class="d-inline-block me-2">
						<?php echo e(csrf_field()); ?>

						<button type="submit"
						        class="btn btn-info">
							Play all weeks
						</button>
					</form>
					<form method="post"
					      action="<?php echo e(route('play_next')); ?>"
					      class="d-inline-block me-2">
						<?php echo e(csrf_field()); ?>

						<button type="submit"
						        class="btn btn-info">
							Play next week
						</button>
					</form>
				<?php else: ?>
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
				<?php endif; ?>

				<form method="post"
				      action="<?php echo e(route('reset')); ?>"
				      class="d-inline-block me-2"
				      onsubmit="return confirm('Are you sure?')">
					<?php echo e(csrf_field()); ?>

					<?php echo e(method_field('DELETE')); ?>

					<button type="submit"
					        class="btn btn-danger">
						Reset
					</button>
				</form>
			<?php else: ?>
				<form method="post"
				      action="<?php echo e(route('generate')); ?>"
				      class="d-inline-block me-2">
					<?php echo e(csrf_field()); ?>

					<button type="submit"
					        class="btn btn-info">
						Generate Fixtures
					</button>
				</form>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/web/home.blade.php ENDPATH**/ ?>