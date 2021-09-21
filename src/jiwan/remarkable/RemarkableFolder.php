<?php

namespace jiwan\remarkable;

use Iterator;
use rizwanjiwan\common\classes\NameableContainer;
use stdClass;

class RemarkableFolder extends RemarkableEntity implements Iterator
{
    /**
     * The contents of this folder. contains RemarkableEntity
     *
     * @var NameableContainer
     */
    protected NameableContainer $contents;

    public function __construct(string $name,stdClass $metaData)
    {
        $this->name=$name;
        $this->metaData=$metaData;
        $this->contents=new NameableContainer($this->getName(),$this->getVisibleName());
    }   

    public function add(RemarkableEntity $entity)
    {
        $this->contents->add($entity,true);
    }
    public function current():RemarkableEntity
    {
        return $this->contents->current();
    }
    public function key(): mixed
    {
        return $this->contents->key();
    }
    public function next(): void
    {
        $this->contents->next();
    }
    public function rewind(): void
    {
        $this->contents->rewind();
    }
    public function valid(): bool
    {
        return $this->contents->valid();
    }

    /**
     * Find out if this folder contains a given entity
     *
     * @param string $name the contain
     * @return bool true if contained in this folder
     */
    public function contains(string $name):bool
    {
        return $this->contents->contains($name);
    }

    /**
     * Get a given contained entity
     *
     * @param string $name
     * @return RemarkableEntity the entity
     */
    public function get(string $name):RemarkableEntity
    {
        return $this->contents->get($name);
    }
}