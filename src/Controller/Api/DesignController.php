<?php

namespace App\Controller\Api;

use App\Entity\Design;
use App\Repository\DesignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/designs', name: 'api_designs_')]
class DesignController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DesignRepository $designRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $designs = $this->designRepository->findAll();
        
        return $this->json([
            'data' => array_map(fn(Design $design) => [
                'id' => $design->getId(),
                'title' => $design->getTitle(),
                'description' => $design->getDescription(),
                'picture' => $design->getPicture(),
            ], $designs),
            'count' => count($designs),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Support both JSON body and multipart/form-data. Prefer form data for file uploads.
        $title = $request->request->get('title', null);
        $description = $request->request->get('description', null);

        // If JSON was sent
        if (null === $title) {
            $data = json_decode($request->getContent(), true) ?? [];
            $title = $data['title'] ?? '';
            $description = $data['description'] ?? null;
        }

        $design = new Design();
        $design->setTitle($title ?? '');
        $design->setDescription($description ?? null);
        $design->setPicture(null);

        $this->entityManager->persist($design);
        $this->entityManager->flush();

        // Handle uploaded file if present
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('picture');
        if ($uploadedFile instanceof UploadedFile) {
            // validate mime type (allow only jpg/jpeg and png)
            $mime = $uploadedFile->getMimeType();
            if (!in_array($mime, ['image/png', 'image/jpeg'])) {
                return $this->json(['message' => 'Invalid file type. Only PNG and JPEG are allowed.'], 400);
            }

            $ext = $uploadedFile->guessExtension() ?: $uploadedFile->getClientOriginalExtension();
            $ext = strtolower(trim($ext, '.'));

            $targetDir = $this->getParameter('kernel.project_dir') . '/public/designs';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $newFilename = $design->getId() . '.' . $ext;
            try {
                $uploadedFile->move($targetDir, $newFilename);
                $design->setPicture($ext);
                $this->entityManager->flush();
            } catch (FileException $e) {
                return $this->json([
                    'message' => 'Design created but file upload failed',
                    'error' => $e->getMessage(),
                    'data' => [
                        'id' => $design->getId(),
                        'title' => $design->getTitle(),
                        'description' => $design->getDescription(),
                        'picture' => $design->getPicture(),
                    ],
                ], 201);
            }
        }

        return $this->json([
            'message' => 'Design created successfully',
            'data' => [
                'id' => $design->getId(),
                'title' => $design->getTitle(),
                'description' => $design->getDescription(),
                'picture' => $design->getPicture(),
            ],
        ], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Design $design): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $design->getId(),
                'title' => $design->getTitle(),
                'description' => $design->getDescription(),
                'picture' => $design->getPicture(),
            ],
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[Route('/{id}', name: 'update', methods: ['PUT', 'POST'])]
    public function update(Design $design, Request $request): JsonResponse
    {
        // Support both JSON and multipart/form-data (POST used by frontend for files)
        $title = $request->request->get('title', null);
        $description = $request->request->get('description', null);

        if (null === $title) {
            $data = json_decode($request->getContent(), true) ?? [];
            $title = $data['title'] ?? null;
            $description = $data['description'] ?? null;
        }

        if (null !== $title) {
            $design->setTitle($title);
        }
        if (null !== $description) {
            $design->setDescription($description);
        }

        // Handle uploaded file replacement
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('picture');
        if ($uploadedFile instanceof UploadedFile) {
            $mime = $uploadedFile->getMimeType();
            if (!in_array($mime, ['image/png', 'image/jpeg'])) {
                return $this->json(['message' => 'Invalid file type. Only PNG and JPEG are allowed.'], 400);
            }

            $ext = $uploadedFile->guessExtension() ?: $uploadedFile->getClientOriginalExtension();
            $ext = strtolower(trim($ext, '.'));

            $targetDir = $this->getParameter('kernel.project_dir') . '/public/designs';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Remove old file if exists
            if ($design->getPicture()) {
                $oldPath = $targetDir . '/' . $design->getId() . '.' . $design->getPicture();
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $newFilename = $design->getId() . '.' . $ext;
            try {
                $uploadedFile->move($targetDir, $newFilename);
                $design->setPicture($ext);
            } catch (FileException $e) {
                return $this->json(['message' => 'File upload failed', 'error' => $e->getMessage()], 500);
            }
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Design updated successfully',
            'data' => [
                'id' => $design->getId(),
                'title' => $design->getTitle(),
                'description' => $design->getDescription(),
                'picture' => $design->getPicture(),
            ],
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Design $design): JsonResponse
    {
        // delete associated file if exists
        $targetDir = $this->getParameter('kernel.project_dir') . '/public/designs';
        if ($design->getPicture()) {
            $path = $targetDir . '/' . $design->getId() . '.' . $design->getPicture();
            if (is_file($path)) {
                @unlink($path);
            }
        }

        $this->entityManager->remove($design);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Design deleted successfully',
        ], 204);
    }
}
