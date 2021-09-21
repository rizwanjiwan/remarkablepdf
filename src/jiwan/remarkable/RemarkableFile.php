<?php

namespace jiwan\remarkable;
use stdClass;

class RemarkableFile extends RemarkableEntity
{
 

    public function __construct(string $name,stdClass $metaData)
    {
        $this->name=$name;
        $this->metaData=$metaData;
    }
}