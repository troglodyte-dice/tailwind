<?php
namespace TroglodyteDice\Tailwind\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MergeTailwindCommand extends Command
{
    protected $signature = 'troglodyte-dice:tailwind';

    protected $description = 'Will add necessary plugins to the tailwind installation and packages.json. Designed to work with tailwind and tailwind components';

	public function handle()
	{
		$sourceFiles = [
			'tailwind' => __DIR__.'/../../configs/tailwind.config.js',
			'package' => __DIR__.'/../../configs/package.json',
			'postcss' => __DIR__.'/../../configs/postcss.config.js',
			'appcss' => __DIR__.'/../../resources/css/package.css',
		];

		$targetFiles = [
			'tailwind' => base_path('tailwind.config.js'),
			'package' => base_path('package.json'),
			'postcss' => base_path('postcss.config.js'),
			'appcss' => resource_path('css/app.css'),
		];
		$this->mergePackageJson($sourceFiles['package'], $targetFiles['package']);

		$tailwindInstruction = "You will need to manually add the following plugins to your tailwind.conf.js\n[require('@tailwindcss/forms'), require('@tailwindcss/typography'), require('@tailwindcss/aspect-ratio')]";

		$this->mergeFilePrompt($sourceFiles['tailwind'], $targetFiles['tailwind'], $tailwindInstruction);
		$this->mergeFilePrompt($sourceFiles['postcss'], $targetFiles['postcss']);
		$this->mergeFilePrompt($sourceFiles['appcss'], $targetFiles['appcss']);


		$this->info("Configuration complete. Now run npm install && npm run dev");
	}

	private function mergeFilePrompt(string $sourceFile, string $targetFile, string $instruction = null)
	{
		if(!file_exists($targetFile)) {
			File::copy($sourceFile, $targetFile);
		}
		else
		{
			$replaceFileQuestion = $this->ask('Do you want to replace your '.$targetFile.' file? (y/n)');
			if($replaceFileQuestion == 'y')
			{
				File::copy($sourceFile, $targetFile);
			}
		}
	}
	private function mergePackageJson(string $libPackageJsonFilePath, string $userPackageJsonFilePath)
	{
		$userPackageArray =  json_decode(File::get($userPackageJsonFilePath), true);
		$packagePackageArray = json_decode(File::get($libPackageJsonFilePath), true);
		$userPackageDependencies = $userPackageArray['devDependencies'] ?? [];
		$packagePackageDependencies = $packagePackageArray['devDependencies'] ?? [];
		$mergedDependencies = array_merge($userPackageDependencies, $packagePackageDependencies);

		$finalPackageJson = $userPackageArray;
		$finalPackageJson['devDependencies'] = $mergedDependencies;
		File::put($userPackageJsonFilePath, json_encode($finalPackageJson, JSON_PRETTY_PRINT));
	}
}
