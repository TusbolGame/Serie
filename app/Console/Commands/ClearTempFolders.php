<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearTempFolders extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clearTempFolders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wipe all the temp folders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $filesArray = new \FilesystemIterator(config('custom.imgTempFolder'), \FilesystemIterator::SKIP_DOTS);
        $files = [];

        foreach ($filesArray as $fileInfo) {
            if ($fileInfo->isFile()) {
                unlink($fileInfo->getFilename());
            }
        }

        $this->info('Folders cleared.');
    }
}
