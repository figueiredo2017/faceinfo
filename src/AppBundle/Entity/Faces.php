<?php
/**
 * @author Figueiredo Luiz <lffigueiredo@gmail.com>
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class Faces
{

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please, upload a picture.")
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png" })
     */
    private $face;

    public function getFace()
    {
        return $this->face;
    }

    public function setFace($face)
    {
        $this->face = $face;

        return $this;
    }
}