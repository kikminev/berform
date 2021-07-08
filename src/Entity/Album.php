<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
class Album extends Node
{
    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="album")
     */
    protected $files;

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getAlbum() === $this) {
                $file->setAlbum(null);
            }
        }

        return $this;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setAlbum($this);
        }

        return $this;
    }
}
