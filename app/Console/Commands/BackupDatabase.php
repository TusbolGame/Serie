<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $executablePath = 'C:/Wamp/bin/mysql/mysql8.0.16/bin/';
        $filenamePattern = config('custom.backupFolder') . config('database.connections.Serie.database') . '_*.sql';
        $filename = config('database.connections.Serie.database') . '_' . Carbon::now()->format('Y-m-d_His') . '.sql';

        $backupFiles = array_map(function ($item) {
            return basename($item);
        }, glob($filenamePattern));

        sort($backupFiles);

        if (sizeof($backupFiles) > 4) {
            unlink(config('custom.backupFolder') . $backupFiles[0]);
        }

        $this->process = new Process(sprintf(
            '%smysqldump -u%s -p%s %s > %s',
            $executablePath,
            config('database.connections.Serie.username'),
            config('database.connections.Serie.password'),
            config('database.connections.Serie.database'),
            config('custom.backupFolder') . $filename
        ));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            $this->process->mustRun();

            $this->info('The backup was successful.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup failed.');
        }
    }
}
