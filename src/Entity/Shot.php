<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
class Shot extends Node
{
    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setShot($this);
        }

        return $this;
    }
}
