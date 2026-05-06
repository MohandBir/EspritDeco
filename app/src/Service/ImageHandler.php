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

    public function handleUploadedImage(array $imageFiles, Product $product)
    {
        foreach ($imageFiles as $imageFile) {
            
            $newFileName = 'img' . uniqid() . '.' .  $imageFile->guessExtension();
            $imageFile->move($this->imageDir, $newFileName);
    
            $image = (new Image)
                ->setName($newFileName)
                ->setAlt($product->getTitle())
                ->setIsPrincipal(1)
                ->setProduct($product)
            ;
            $this->em->persist($image);
        }

    }

    
}
