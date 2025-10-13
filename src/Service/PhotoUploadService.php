<?php

namespace App\Service;

use App\Entity\Photo;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhotoUploadService
{
    private string $uploadsDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $uploadsDirectory, SluggerInterface $slugger)
    {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Upload une photo et retourne l'entité Photo créée
     *
     * @param UploadedFile $file
     * @param User $user
     * @param string $type 'tree' ou 'request'
     * @return Photo
     */
    public function upload(UploadedFile $file, User $user, string $type = 'request'): Photo
    {
        $subDir = $type === 'tree' ? 'trees' : 'requests';
        $targetDirectory = $this->uploadsDirectory . '/' . $subDir;

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $extension = $file->getClientOriginalExtension();
        if (empty($extension)) {
            $extension = $file->guessExtension() ?? 'jpg';
        }

        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

        $size = $file->getSize();

        try {
            $file->move($targetDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception('Erreur lors de l\'upload : ' . $e->getMessage());
        }

        $mimeType = $file->getClientMimeType() ?? 'image/jpeg';

        $photo = new Photo();
        $photo->setFilename($newFilename);
        $photo->setOriginalName($originalFilename);
        $photo->setPath($subDir . '/' . $newFilename);
        $photo->setMimeType($mimeType);
        $photo->setSize($size);
        $photo->setUploadedBy($user);
        $photo->setCreatedAt(new \DateTimeImmutable());

        return $photo;
    }

    /**
     * Supprime une photo du système de fichiers
     *
     * @param Photo $photo
     * @return bool
     */
    public function delete(Photo $photo): bool
    {
        $filepath = $this->uploadsDirectory . '/' . $photo->getPath();

        if (file_exists($filepath)) {
            return unlink($filepath);
        }

        return false;
    }

    /**
     * Copie une photo de requests/ vers trees/ lors de la validation
     *
     * @param Photo $photo
     * @return Photo
     */
    public function copyToTrees(Photo $photo): Photo
    {
        $oldPath = $this->uploadsDirectory . '/' . $photo->getPath();

        $newFilename = $photo->getFilename();
        $targetDirectory = $this->uploadsDirectory . '/trees';

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $newPath = $targetDirectory . '/' . $newFilename;

        if (!copy($oldPath, $newPath)) {
            throw new \Exception('Impossible de copier la photo');
        }

        $newPhoto = new Photo();
        $newPhoto->setFilename($newFilename);
        $newPhoto->setOriginalName($photo->getOriginalName());
        $newPhoto->setPath('trees/' . $newFilename);
        $newPhoto->setMimeType($photo->getMimeType());
        $newPhoto->setSize($photo->getSize());
        $newPhoto->setUploadedBy($photo->getUploadedBy());
        $newPhoto->setIsPrimary($photo->isPrimary());

        return $newPhoto;
    }
}