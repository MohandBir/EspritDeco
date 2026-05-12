<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageHandler
{
    public function __construct
    (
        private string $imageDir,
        private EntityManagerInterface $em,
    ) {}

    public function deleteImages(Image $image)
    {
        $imagePath = $this->imageDir . $image->getName();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    public function handleUploadedImage(UploadedFile $imageFile, Product $product)
    {       
            $newFileName = 'img' . uniqid() . '.' .  $imageFile->guessExtension();
            $imageFile->move($this->imageDir, $newFileName);
    
            $image = (new Image)
                ->setName($newFileName)
                ->setAlt($product->getTitle())
                ->setIsPrincipal(1)
                ->setProduct($product)
            ;
            return $image;
    }  
}
