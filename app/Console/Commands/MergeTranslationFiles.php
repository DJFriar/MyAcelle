<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;

use function Acelle\Helpers\updateTranslationFile;

class MergeTranslationFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:merge {current} {update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge translation phrases from $new to $current (overwrite). The utility is helpful when we have a new translation file and want to apply it to a current file in the repos.
        IMPORTANT: do not merge any files under lang/en/ folder (which is considered the main language) or it may add redundant keys to the main file which will in turn propogate to the other files of other languages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $current = $this->argument('current');
        $update = $this->argument('update');

        $maindir = realpath(resource_path('lang/en'));

        if (strpos(realpath($current), $maindir) === 0) {
            throw new \Exception('Cannot update a translation file of the main language (EN)');
        }

        updateTranslationFile($current, $update, $overwrite = true, $deleteTargetKeys = false, $sort = true);
    }
}
