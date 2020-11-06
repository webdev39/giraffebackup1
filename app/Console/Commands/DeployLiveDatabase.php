<?php

namespace App\Console\Commands;

use App\Models\User;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Console\Command;

class DeployLiveDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:live_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the database version from the live server (the database is stored under git)';

    /**
     * The Database name
     *
     * @var string
     */
    private $database;

    /**
     * @var \PDO
     */
    private $conn;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->database = config('database.connections.mysql.database');
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (!$this->database) {
            $this->warn('Skipping creation of database as env(DB_DATABASE) is empty');
            return;
        }

        if (config('app.env') === 'production') {
            $this->warn('It is forbidden to run this command on the production server!');
            return;
        }

        if ($this->confirm('Do you wish to continue?', 1)) {
            $this->dropDB();
            $this->createDB();

            \DB::unprepared(\Storage::disk('storage')->get('live_dump.sql'));
            $this->info('Successfully load dump');

            User::whereNotNull('password')->update(['password' => bcrypt('secret')]);
            $this->info('Successfully change all passwords to `secret`');

            if ($this->confirm('To run commands for deployment?', 1)) {
                $this->info('Run deploy command');
                \Artisan::call('deploy');
            }

            $this->info('Finish');
        }
    }

    /**
     * Drop current database
     */
    private function dropDB()
    {
        try {
            $this->getPDOConnection()->exec(sprintf(
                'DROP DATABASE IF EXISTS %s;',
                $this->database
            ));

            $this->info(sprintf('Successfully drop %s database', $this->database));
        } catch (PDOException $exception) {
            $this->error(sprintf('Failed to drop %s database, %s', $this->database, $exception->getMessage()));
        }
    }

    /**
     * Create new database
     */
    private function createDB()
    {
        try {
            $this->getPDOConnection()->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
                $this->database,
                config('database.connections.mysql.charset'),
                config('database.connections.mysql.collation')
            ));

            $this->info(sprintf('Successfully created %s database', $this->database));
        } catch (PDOException $exception) {
            $this->error(sprintf('Failed to create %s database, %s', $this->database, $exception->getMessage()));
        }
    }

    /**
     * @return \PDO
     */
    private function getPDOConnection()
    {
        if (!$this->conn) {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');

            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $this->conn = new \PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
        }

        return $this->conn;
    }
}
