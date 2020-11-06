<?php


namespace App;


use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Folder;

class ImapClient extends Client
{
    public function getFolder($folder_name, $attributes = 32, $delimiter = null)
    {
        $delimiter = $delimiter == null ? config('imap.options.delimiter', '/') : $delimiter;

        $oFolder = new ImapFolder($this, (object)[
            'name'       => $this->getAddress().$folder_name,
            'attributes' => $attributes,
            'delimiter'  => $delimiter
        ]);

        return $oFolder;
    }
    
    /**
     * @param string $folderName
     * @return bool
     * @throws ConnectionFailedException
     */
    public function folderExists(string $folderName): string
    {
        $folders = $this->getFolders(false);
        foreach ($folders as $folder) {
            $pattern = '/' . str_replace('\*', '.*', preg_quote($folderName)) . '/';
            if (preg_match($pattern, $folder->name)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param string $domain
     * @param $port
     * @return bool
     * @throws \ErrorException
     */
    public function pingDomain(string $domain, $port)
    {
        try {
            $startTime = microtime(true);
            $file = fsockopen($domain, $port, $errno, $errstr, 2);
            $stopTime = microtime(true);
            
            if (!$file) {
                $status = false; // Site is down
            } else {
                fclose($file);
                $status = ($stopTime - $startTime) * 1000;
                $status = floor($status);
            }
            return (bool) $status;
        } catch (\Exception $e) {
            throw new \ErrorException('Domain ' . $domain . ' is unreachable');
        }
    }
}