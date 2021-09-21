<?php

namespace jiwan\remarkable;

use rizwanjiwan\common\classes\Config;
use rizwanjiwan\common\classes\NameableContainer;
use jiwan\remarkable\RemarkableEntity;
use jiwan\remarkable\RemarkableFile;
use jiwan\remarkable\RemarkableFolder;
use rizwanjiwan\common\classes\LogManager;
use Monolog\Logger;

class RemarkableFiles
{
    /**
     * Holds all the top level folders and files
     *
     * @var NameableContainer
     */
    private NameableContainer|RemarkableFolder $topLevel;

    private Logger $log;

    public function __construct(string $sourceDirectory)
    {
        $this->log=LogManager::createLogger('RemarkableFiles');
        $this->log->debug('Start');
        $this->topLevel=new NameableContainer();
        $files=scandir($sourceDirectory);
        foreach($files as $file){
            if(((strcmp($file,'.')!==0)&&(strcmp($file,'..')!==0))){//not a dot file
                $this->log->debug('Parsing '.$file);
                $pathinfo=pathinfo($file);
                if((array_key_exists('extension',$pathinfo))&&(strcmp($pathinfo['extension'],'metadata')===0)){//meta data file
                    $file=$sourceDirectory.'/'.$file;   //add folder
                    $this->log->debug(' -> Confirmed metadata in '.$file);
                    $newFolderchain=self::parseFile($file);
                    if($newFolderchain===null){
                        $this->log->debug('      -> in trash');
                    }
                    else{
                        $this->log->debug('      -> Merging');
                        $this->topLevel=self::addFolderchain($this->topLevel,$newFolderchain);
                    }
                }
            }
       }
    }

    private static function addFolderchain(NameableContainer|RemarkableFolder $existing,RemarkableEntity $toAdd):mixed
    {
        if($toAdd->isFolder()===false){
            $existing->add($toAdd,true);//no where to go, we got to a file. Add it and pop out of this recursion
            return $existing;
        }
        else if(array_search($toAdd->getVisibleName(),Config::getArray('SKIP_FOLDERS'))!==false){
            //is this folder to be skipped
                return $existing;
        }
        /**
         * @var RemarkableFolder $toAdd Must be a folder based on above if
         * */
        if($existing->contains($toAdd->getName())){//folder is already there
            if($toAdd->valid()===false){//doesn't contain anything
                return $existing;//the existing is good to go
            }
            //go a level deeper to see if it already exists
            $merged=self::addFolderchain(
                $existing->get($toAdd->getName()),
                $toAdd->current());
            if($merged===null)
                return $existing;
            return $existing->add($merged,true);
        }
        else{   //folder doesn't exist, add it in and pop up out of this recursion
            $existing->add($toAdd);
            return $existing;
        }
    }
    /**
     * Get the whole path tree to a given file
     *
     * @param string $fileName a given metadata file name
     * @param ?RemarkableEntity $childEntity In the case where the filename will lead to a folder, add this child entity into that folder
     * @return ?RemarkableEntity of the top level folder ultimately leading to this $fileName entity
     */
    private static function parseFile(string $fileName, ?RemarkableEntity $childEntity=null):?RemarkableEntity{
            $entity=new RemarkableEntity($fileName);
            if($entity->isFolder()){
                $entity=$entity->toFolder();
                if($childEntity!==null){
                    $entity->add($childEntity);
                }
            }
            else{
                $entity=$entity->toFile();
            }
            //if there's a parent, we need to get that and add it on to this
            if($entity->isTopLevel()){
                //no parent, we're at the top level:
                return $entity;
            }
            else if($entity->isInTrash()) {
                return null;
            }
            else{
                //Get parent and add this to it
                $pathinfo=pathinfo($fileName);
                $parentFileName=$pathinfo['dirname'].'/'.$entity->getParent().'.metadata';
                $topLevelFolder=self::parseFile($parentFileName,$entity);
                return $topLevelFolder;
            }
    }

    public function __toString():string
    {
        $str="Top Lev\n";
        foreach($this->topLevel as $entity){
            $str.=$this->getString($entity,1);
        }
        return $str;
    }
    private function getString(RemarkableEntity $entity,int $tabLev):string
    {
        $this->log->debug('Parsing '.$entity->getVisibleName());
        $str=$this->getTabs($tabLev).$entity->getVisibleName()."\n";
        if($entity->isFolder()===false){
            $this->log->debug('   ->isFolder()=false'. $entity->getType());
            return $str;
        }
        $this->log->debug('   ->isFolder()=true');
        /**@var RemarkableFolder $str*/
        $tabLev++;
        foreach($entity as $subEntity){
            $str.=$this->getString($subEntity,$tabLev);
        }
        return $str;
    }
    private function getTabs(int $num):string
    {
        return str_repeat("\t", $num);;
    }

    /**
     * After parsing everything, save it to the Config OUTPUT_DIR
     */
    public function saveAll()
    {
        $outputDirectoryRoot=self::getPathToAppRoot().Config::get('OUTPUT_DIR');
        foreach($this->topLevel as $entity){
            $this->save($entity,$outputDirectoryRoot);
        }
    }

    /**
     * Save a single entity
     */
    private function save(RemarkableEntity $entity, string $baseFolder)
    {
        if($entity->isFolder()) {//make the directory and recurse through
            $newFolder=$baseFolder.'/'.$entity->getVisibleName();
            if(file_exists($newFolder)===false)
                mkdir($newFolder);
            foreach($entity as $childEntity){
                $this->save($childEntity,$newFolder);
            }
        }
        else{   //file to convert to pdf
            $appRoot=self::getPathToAppRoot();
            $pathInfo=pathinfo($entity->getName());
            //todo: escape visible name
            exec($appRoot."rm2pdf/rm2pdf ".$appRoot.Config::get('DOWNLOAD_DIR').'/'.$pathInfo['filename']." '".$baseFolder."/".$entity->getVisibleName().".pdf' -t ".$appRoot."template.pdf");
        }
    }

    private static function getPathToAppRoot():string
    {
        return realpath(dirname(__FILE__)).'/../../../';
    }
}