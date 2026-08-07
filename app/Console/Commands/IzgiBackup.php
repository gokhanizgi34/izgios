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


    private int $fileCount = 0;


    public function handle()
    {

        $time = Carbon::now()->format('Y-m-d-H-i');


        $backupPath = base_path(
            'backup/automatic/'.$time.'/project'
        );


        File::makeDirectory(
            $backupPath,
            0755,
            true
        );


        $this->info('İZGİ OS backup başladı.');



        /*
        |--------------------------------------------------------------------------
        | Klasörler
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

            $source = base_path($folder);

            $target = $backupPath.'/'.$folder;


            if(File::exists($source))
            {

                $this->copyFolder(
                    $source,
                    $target
                );


                $this->line(
                    $folder.' kopyalandı'
                );

            }


        }





        /*
        |--------------------------------------------------------------------------
        | Dosyalar
        |--------------------------------------------------------------------------
        */


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


                $this->fileCount++;


            }


        }





        /*
        |--------------------------------------------------------------------------
        | Database
        |--------------------------------------------------------------------------
        */


        $databasePath = base_path(
            'backup/database/izgios-'.$time.'.sql'
        );



        $db = Config::get(
            'database.connections.mysql'
        );



        $command = sprintf(

            'mysqldump -h%s -P%s -u%s %s > "%s"',

            $db['host'],

            $db['port'],

            $db['username'],

            $db['database'],

            $databasePath

        );



        exec($command);



        $this->info(
            'Database backup tamamlandı.'
        );



        /*
        |--------------------------------------------------------------------------
        | Log
        |--------------------------------------------------------------------------
        */


        File::append(

            base_path('backup/backup.log'),

            Carbon::now().
            " - Backup tamamlandı ".
            $time.
            " Dosya: ".
            $this->fileCount.
            PHP_EOL

        );



        $this->info(
            'Toplam '.$this->fileCount.' dosya yedeklendi.'
        );


        return Command::SUCCESS;


    }




    private function copyFolder($source,$destination)
    {


        $items = File::allFiles($source);



        foreach($items as $item)
        {


            $relative =
                $item->getRelativePathname();



            $target =
                $destination.'/'.$relative;



            File::ensureDirectoryExists(
                dirname($target)
            );



            File::copy(

                $item->getPathname(),

                $target

            );


            $this->fileCount++;


        }


    }



}