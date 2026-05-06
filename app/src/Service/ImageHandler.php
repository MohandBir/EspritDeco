<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;


class ImageHandler
{
    public function __construct
    (
        private string $imageDir,
        private EntityManagerInterface $em
    ) {}
    public function deleteImages(Product $product)
    {
        foreach ($product->getImages() as $image) 
        {
            $imagePath = $this->imageDir . $image->getName();

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $this->em->remove($image);
        }
    }
}
