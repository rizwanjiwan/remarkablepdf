<?php
namespace jiwan\remarkable;

use rizwanjiwan\common\interfaces\Nameable;
use stdClass;
/**
 * Represents anything in the remarkable that has metadata
 */
class RemarkableEntity implements Nameable{

    /**
     * $this->getType returns this when it's a folder
     */
    const TYPE_FOLDER='CollectionType';
    /**
     * $this->getType returns this when it's a document
     */
    const TYPE_DOC='DocumentType';
    /**
     * The meta data on an entity
     *
    {
    "deleted": false,
    "lastModified": "1631239553257",
    "lastOpened": "0",
    "lastOpenedPage": 0,
    "metadatamodified": false,
    "modified": false,
    "parent": "77e55ebf-fb37-4e58-9939-2fc3e44059b3",
    "pinned": false,
    "synced": true,
    "type": "DocumentType",
    "version": 3,
    "visibleName": "Before & After Getting Your Puppy"
    }
     * @var stdClass
     */
    protected stdClass $metaData;

    /**
     * The name of this entity
     *
     * @var string
     */
    protected string $name;

    /**
     * Create a new RemarkableFile
     *
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $pathinfo=pathinfo($fileName);
        $this->name=$pathinfo['basename'];
        $this->metaData=json_decode(file_get_contents($fileName));  //parse the meta data
    }
    public function isDeleted():bool
    {
        return $this->metaData->deleted;

    }
    public function getLastModified():int
    {
        return intval($this->metaData->lastModified);
    }
    public function getLastOpenedPage():int
    {
        return intval($this->metaData->lastOpenedPage);
    }
    public function getMetadataModified():bool
    {
        return $this->metaData->metadatamodified;
    }
    public function getModified():bool
    {
        return $this->metaData->modified;
    }
    public function getParent():string
    {
        return $this->metaData->parent;
    }
    public function isInTrash():string
    {
        return strcmp($this->metaData->parent,'trash')===0;
    }
    public function isTopLevel():bool
    {
        return strlen($this->getParent())===0;
    }
    public function getPinned():bool
    {
        return $this->metaData->pinned;
    }
    public function getSynced():bool
    {
        return $this->metaData->synced;
    }
    public function getType():string
    {
        return $this->metaData->type;
    }
    public function isFolder():bool
    {
        return strcmp($this->getType(),RemarkableEntity::TYPE_FOLDER)===0;
    }
    public function getVersion():int
    {
        return $this->metaData->version;

    }
    public function getVisibleName():string
    {
        return $this->metaData->visibleName;

    }
    public function getName():string
    {
        return $this->name;

    }
    /**
	 * A friendly name for the end user to see
	 * @return string friendly name
	 */
	public function getFriendlyName():string
    {
        return $this->getVisibleName();
    }

	/**
	 * A name for use that is unique
	 * @return string name
	 */
	public function getUniqueName():string
    {
        return $this->getName();
    }
    /**
     * Convert this to a file
     *
     * @return RemarkableFile
     */
    public function toFile():RemarkableFile
    {
        return new RemarkableFile($this->name,$this->metaData);
    }
    /**
     * Convert this to a folder
     *
     * @return RemarkableFolder
     */
    public function toFolder():RemarkableFolder
    {
        return new RemarkableFolder($this->name,$this->metaData);
    }
}