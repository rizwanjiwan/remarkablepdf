<?php
/*
Todo:
- Clean up resources folder between runs
- Build tighter docker image
- Build proper/distributable docker image
- Support templates
*/

use jiwan\remarkable\RemarkableFiles;
use rizwanjiwan\common\classes\Config;
use phpseclib3\Net\SFTP;
use rizwanjiwan\common\classes\LogManager;

date_default_timezone_set('America/Toronto');

require_once realpath(dirname(__FILE__)).'/vendor/autoload.php';

LogManager::consoleLogingOn();
$downloadFolder=realpath(dirname(__FILE__)).'/'.Config::get('DOWNLOAD_DIR');
$log=LogManager::createLogger('RUN');

//https://phpseclib.com/docs/sftp

$log->info('Downloading from reMarkable');
$sftp = new SFTP(Config::get('SFTP_HOST'));
$sftp->login(Config::get('SFTP_USER'), Config::get('SFTP_PASS'));
download($sftp,Config::get('SFTP_PATH'),$downloadFolder);
$log->info('Parsing downloaded files...');
$files=new RemarkableFiles($downloadFolder);
$log->info('Saving files as PDF....');
$files->saveAll();


/**
 * Download a source directory to a target folder
 *
 * @param SFTP $sftp
 * @param string $source
 * @param string $target
 * @return void
 */
function download(SFTP $sftp,string $source, string $target)
{
    $log=LogManager::createLogger('Download');
    $log->debug('Called: '.$source.' to '.$target);
    $skippedFileExt=array('pdf','epub','epubindex','thumbnails','textconversion');
    if(((strcmp($source,'.')===0)||(strcmp($source,'..')===0))){
        $log->debug('Dot directory requested, bailing');
        return;//nothing to do
    }
    $sftp->chdir($source);
    //go through all the files in this directory
    $contents=$sftp->rawlist();
    foreach($contents as $name=>$meta){
        $pathinfo=pathinfo($name);
        if((array_key_exists('extension',$pathinfo)===false)||
            (array_search($pathinfo['extension'],$skippedFileExt)===false)){//need to skip this file otherwise
            if($meta['type']===1){
                $log->debug("Downloading File ".$name);
                $sftp->get($name, $target.'/'.$name);
            }
            else{
                $log->debug("Dir ".$name);
                if(((strcmp($name,'.')!==0)&&(strcmp($name,'..')!==0))){
                    //make the local directory and then download the contents into there
                    $newDir=$target.'/'.$name;
                    if(file_exists($newDir)===false){
                        mkdir($target.'/'.$name);
                    }
                    download($sftp,$source.'/'.$name,$newDir);
                    //make sure we are back in this directory for the rest of the loops
                    $sftp->chdir($source);
                }
            }
        }
    }
}