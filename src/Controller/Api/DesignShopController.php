<?php

namespace App\Controller\Api;

use App\Entity\DesignShop;
use App\Repository\DesignRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/design-shops', name: 'api_design_shops_')]
class DesignShopController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DesignRepository $designRepository,
        private ShopRepository $shopRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $repo = $this->entityManager->getRepository(DesignShop::class);
        $items = $repo->findAll();

        $data = array_map(fn(DesignShop $it) => [
            'id' => $it->getId(),
            'design' => $it->getDesign()?->getId(),
            'shop' => $it->getShop()?->getId(),
            'publishedAt' => $it->getPublishedAt()?->format(DATE_ATOM),
            'lastUpdateAt' => $it->getLastUpdateAt()?->format(DATE_ATOM),
        ], $items);

        return $this->json(['data' => $data, 'count' => count($data)]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $designId = $request->request->get('design_id');
        $shopId = $request->request->get('shop_id');
        $published = $request->request->get('publishedAt');
        $lastUpdate = $request->request->get('lastUpdateAt');

        if (!$designId || !$shopId) {
            return $this->json(['message' => 'Missing design_id or shop_id'], 400);
        }

        $design = $this->designRepository->find($designId);
        $shop = $this->shopRepository->find($shopId);

        if (!$design || !$shop) {
            return $this->json(['message' => 'Design or Shop not found'], 404);
        }

        $ds = new DesignShop();
        $ds->setDesign($design);
        $ds->setShop($shop);

        try {
            if ($published) {
                $ds->setPublishedAt(new \DateTime($published));
            }
            if ($lastUpdate) {
                $ds->setLastUpdateAt(new \DateTime($lastUpdate));
            }
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid date format'], 400);
        }

        $this->entityManager->persist($ds);
        $this->entityManager->flush();

        return $this->json(['message' => 'Association created', 'data' => [
            'id' => $ds->getId(),
            'design' => $design->getId(),
            'shop' => $shop->getId(),
            'publishedAt' => $ds->getPublishedAt()?->format(DATE_ATOM),
            'lastUpdateAt' => $ds->getLastUpdateAt()?->format(DATE_ATOM),
        ]], 201);
    }
}
