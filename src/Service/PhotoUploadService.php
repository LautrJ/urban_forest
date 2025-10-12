<?php

namespace App\Service;

use App\Entity\Photo;
use App\Entity\Tree;
use App\Entity\TreeRequest;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhotoUploadService
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_PHOTOS_PER_ENTITY = 5;

    public function __construct(
        private string $uploadsDirectory,
        private SluggerInterface $slugger
    ) {
    }

    /**
     * Upload une ou plusieurs photos pour un arbre
     *
     * @param array<UploadedFile> $uploadedFiles
     * @param Tree $tree
     * @param User $uploadedBy
     * @param bool $setFirstAsPrimary
     * @return array<Photo>
     * @throws \Exception
     */
    public function uploadPhotosForTree(
        array $uploadedFiles,
        Tree $tree,
        User $uploadedBy,
        bool $setFirstAsPrimary = false
    ): array {
        $this->validatePhotoLimit($tree->getPhotos()->count(), count($uploadedFiles));

        $photos = [];
        $isFirst = true;

        foreach ($uploadedFiles as $uploadedFile) {
            $photo = $this->processUpload(
                $uploadedFile,
                'trees',
                $uploadedBy,
                $setFirstAsPrimary && $isFirst && $tree->getPhotos()->isEmpty()
            );

            $photo->setTree($tree);
            $photos[] = $photo;

            $isFirst = false;
        }

        return $photos;
    }

    /**
     * Upload une ou plusieurs photos pour une demande
     *
     * @param array<UploadedFile> $uploadedFiles
     * @param TreeRequest $treeRequest
     * @param User $uploadedBy
     * @param bool $setFirstAsPrimary
     * @return array<Photo>
     * @throws \Exception
     */
    public function uploadPhotosForRequest(
        array $uploadedFiles,
        TreeRequest $treeRequest,
        User $uploadedBy,
        bool $setFirstAsPrimary = false
    ): array {
        $this->validatePhotoLimit($treeRequest->getPhotos()->count(), count($uploadedFiles));

        $photos = [];
        $isFirst = true;

        foreach ($uploadedFiles as $uploadedFile) {
            $photo = $this->processUpload(
                $uploadedFile,
                'requests',
                $uploadedBy,
                $setFirstAsPrimary && $isFirst && $treeRequest->getPhotos()->isEmpty()
            );

            $photo->setTreeRequest($treeRequest);
            $photos[] = $photo;

            $isFirst = false;
        }

        return $photos;
    }

    /**
     * Transfère les photos d'une TreeRequest vers un Tree
     *
     * @param TreeRequest $treeRequest
     * @param Tree $tree
     * @return array<Photo>
     */
    public function transferPhotosFromRequestToTree(TreeRequest $treeRequest, Tree $tree): array
    {
        $transferredPhotos = [];

        foreach ($treeRequest->getPhotos() as $requestPhoto) {
            // Copier le fichier physique
            $oldPath = $this->uploadsDirectory . '/requests/' . $requestPhoto->getFilename();
            $newPath = $this->uploadsDirectory . '/trees/' . $requestPhoto->getFilename();

            if (file_exists($oldPath)) {
                copy($oldPath, $newPath);

                // Créer une nouvelle entité Photo pour le Tree
                $treePhoto = new Photo();
                $treePhoto->setFilename($requestPhoto->getFilename());
                $treePhoto->setOriginalName($requestPhoto->getOriginalName());
                $treePhoto->setPath('trees/' . $requestPhoto->getFilename());
                $treePhoto->setMimeType($requestPhoto->getMimeType());
                $treePhoto->setSize($requestPhoto->getSize());
                $treePhoto->setUploadedBy($requestPhoto->getUploadedBy());
                $treePhoto->setIsPrimary($requestPhoto->isPrimary());
                $treePhoto->setTree($tree);
                $treePhoto->setCreatedAt(new \DateTimeImmutable());

                $transferredPhotos[] = $treePhoto;
            }
        }

        return $transferredPhotos;
    }

    /**
     * Supprime une photo (fichier + entité)
     *
     * @param Photo $photo
     */
    public function deletePhoto(Photo $photo): void
    {
        $filePath = $this->uploadsDirectory . '/' . $photo->getPath();

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Définit une photo comme principale et retire le flag des autres
     *
     * @param Photo $photo
     * @param Tree|TreeRequest $entity
     */
    public function setPrimaryPhoto(Photo $photo, Tree|TreeRequest $entity): void
    {
        // Retirer le flag isPrimary de toutes les photos
        foreach ($entity->getPhotos() as $existingPhoto) {
            $existingPhoto->setIsPrimary(false);
        }

        // Définir cette photo comme principale
        $photo->setIsPrimary(true);
    }

    /**
     * Traite l'upload d'un fichier
     *
     * @param UploadedFile $uploadedFile
     * @param string $subDirectory (trees ou requests)
     * @param User $uploadedBy
     * @param bool $isPrimary
     * @return Photo
     * @throws \Exception
     */
    private function processUpload(
        UploadedFile $uploadedFile,
        string $subDirectory,
        User $uploadedBy,
        bool $isPrimary = false
    ): Photo {
        // ⭐ VALIDATION MINIMALISTE - ne pas toucher au fichier temporaire
        if (!$uploadedFile->isValid()) {
            throw new \Exception('Erreur lors de l\'upload du fichier');
        }

        if ($uploadedFile->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('Le fichier est trop volumineux. Taille maximale: 5 Mo');
        }

        $mimeType = $uploadedFile->getMimeType();
        if ($mimeType && !in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Type de fichier non autorisé. Formats acceptés: JPEG, PNG, WEBP');
        }

        // Générer un nom de fichier unique
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        // Créer le répertoire si nécessaire
        $targetDirectory = $this->uploadsDirectory . '/' . $subDirectory;
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // ⭐ DÉPLACER LE FICHIER IMMÉDIATEMENT
        try {
            $uploadedFile->move($targetDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception('Erreur lors de l\'upload du fichier: ' . $e->getMessage());
        }

        // Créer l'entité Photo
        $photo = new Photo();
        $photo->setFilename($newFilename);
        $photo->setOriginalName($uploadedFile->getClientOriginalName());
        $photo->setPath($subDirectory . '/' . $newFilename);
        $photo->setMimeType($mimeType ?? 'image/jpeg');
        $photo->setSize($uploadedFile->getSize());
        $photo->setUploadedBy($uploadedBy);
        $photo->setIsPrimary($isPrimary);
        $photo->setCreatedAt(new \DateTimeImmutable());

        return $photo;
    }

    /**
     * Valide un fichier uploadé
     *
     * @param UploadedFile $file
     * @throws \Exception
     */
    private function validateFile(UploadedFile $file): void
    {
        // ⭐ CETTE MÉTHODE N'EST PLUS UTILISÉE
        // La validation se fait directement dans processUpload()
    }

    /**
     * Valide la limite de photos
     *
     * @param int $currentCount
     * @param int $newCount
     * @throws \Exception
     */
    private function validatePhotoLimit(int $currentCount, int $newCount): void
    {
        if ($currentCount + $newCount > self::MAX_PHOTOS_PER_ENTITY) {
            throw new \Exception(sprintf(
                'Nombre maximum de photos atteint (%d). Actuellement: %d, tentative d\'ajout: %d',
                self::MAX_PHOTOS_PER_ENTITY,
                $currentCount,
                $newCount
            ));
        }
    }
}