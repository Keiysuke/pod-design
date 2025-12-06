<?php

namespace App\Controller\Api;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/shops', name: 'api_shops_')]
class ShopController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ShopRepository $shopRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $shops = $this->shopRepository->findAll();

        return $this->json([
            'data' => array_map(fn(Shop $s) => [
                'id' => $s->getId(),
                'name' => $s->getName(),
                'url' => $s->getUrl(),
                'logo' => $s->getLogo(),
            ], $shops),
            'count' => count($shops),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $name = $request->request->get('name');
        $url = $request->request->get('url');

        if (!$name || !$url) {
            return $this->json(['message' => 'Missing name or url'], 400);
        }

        // Logo file is required
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('logo');
        if (!$uploadedFile instanceof UploadedFile) {
            return $this->json(['message' => 'Logo file is required'], 400);
        }

        $mime = $uploadedFile->getMimeType();
        if (!in_array($mime, ['image/png', 'image/jpeg'])) {
            return $this->json(['message' => 'Invalid file type. Only PNG and JPEG are allowed.'], 400);
        }

        $ext = $uploadedFile->guessExtension() ?: $uploadedFile->getClientOriginalExtension();
        $ext = strtolower(trim($ext, '.'));

        $shop = new Shop();
        $shop->setName($name);
        $shop->setUrl($url);
        // Set the extension now so the DB NOT NULL constraint is satisfied
        $shop->setLogo($ext);

        $this->entityManager->persist($shop);
        $this->entityManager->flush();

        // Save the logo file
        $targetDir = $this->getParameter('kernel.project_dir') . '/public/shops';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newFilename = $shop->getId() . '.' . $ext;
        try {
            $uploadedFile->move($targetDir, $newFilename);
            $shop->setLogo($ext);
            $this->entityManager->flush();
        } catch (FileException $e) {
            // Clean up shop if file upload fails
            $this->entityManager->remove($shop);
            $this->entityManager->flush();
            return $this->json(['message' => 'File upload failed', 'error' => $e->getMessage()], 400);
        }

        return $this->json([
            'message' => 'Shop created successfully',
            'data' => [
                'id' => $shop->getId(),
                'name' => $shop->getName(),
                'url' => $shop->getUrl(),
                'logo' => $shop->getLogo(),
            ],
        ], 201);
    }
}