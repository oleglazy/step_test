<?php


namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;

class SaveTopMusic
{
    const FILE_NAME = '\tmp\music_top.txt';

    /**
     * @param array $musicTop
     * @return bool
     * @throws \Exception
     */
    public function saveToFileMusic(array $musicTop)
    {
        $date = new \DateTime();
        $fileName = getcwd() . self::FILE_NAME;
        $filesystem = new Filesystem();

        $strData = 'Top by ' . $date->format('Y-m-d') . PHP_EOL;

        if (!$filesystem->exists($fileName)) {
            $filesystem->touch($fileName);
            $filesystem->chmod($fileName, 0777);
            $filesystem->dumpFile($fileName, $strData);
        } else {
            $filesystem->appendToFile($fileName, $strData);
        }

        foreach ($musicTop as $music) {
            $filesystem->appendToFile($fileName, $music . PHP_EOL);
        }

        return true;
    }
}