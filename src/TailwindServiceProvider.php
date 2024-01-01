<?php

namespace TroglodyteDice\Tailwind;

use Carbon\Laravel\ServiceProvider;
use TroglodyteDice\Tailwind\Console\Commands\MergeTailwindCommand;

class TailwindServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->commands([
			MergeTailwindCommand::class,
		]);
	}
}