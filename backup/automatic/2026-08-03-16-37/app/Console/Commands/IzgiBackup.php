<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class IzgiBackup extends Command
{

    protected $signature = 'izgi:backup';

    protected $description = 'İZGİ OS otomatik yedekleme sistemi';


    public function handle()
    {

        $time = Carbon::now()->format('Y-m-d-H-i');


        $backupPath = base_path(
            'backup/automatic/'.$time
        );


        /*
        |--------------------------------------------------------------------------
        | Klasör oluştur
        |--------------------------------------------------------------------------
        */


        File::makeDirectory(
            $backupPath,
            0755,
            true
        );



        /*
        |--------------------------------------------------------------------------
        | Dosya yedekleme
        |--------------------------------------------------------------------------
        */


        $folders = [

            'app',
            'resources',
            'routes',
            'database/migrations',

        ];



        foreach($folders as $folder)
        {


            File::copyDirectory(

                base_path($folder),

                $backupPath.'/'.$folder

            );


        }



        $files = [

            'composer.json',
            'composer.lock',
            'package.json',
            'vite.config.js',

        ];



        foreach($files as $file)
        {

            if(File::exists(base_path($file)))
            {

                File::copy(

                    base_path($file),

                    $backupPath.'/'.$file

                );

            }

        }





        /*
        |--------------------------------------------------------------------------
        | Database Backup
        |--------------------------------------------------------------------------
        */


        $databasePath = base_path(
            'backup/database/izgios-'.$time.'.sql'
        );



        $db = Config::get('database.connections.mysql');



        $command = sprintf(

            'mysqldump -h%s -P%s -u%s %s > %s',

            $db['host'],

            $db['port'],

            $db['username'],

            $db['database'],

            $databasePath

        );



        exec($command);





        /*
        |--------------------------------------------------------------------------
        | Log
        |--------------------------------------------------------------------------
        */


        File::append(

            base_path('backup/backup.log'),

            Carbon::now().
            " - Backup tamamlandı : ".
            $time.
            PHP_EOL

        );



        $this->info(
            'İZGİ OS backup tamamlandı.'
        );



        return Command::SUCCESS;


    }


}   