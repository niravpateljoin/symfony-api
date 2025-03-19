<?php

namespace App\Controller;

use App\Service\PropertyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PropertyController extends AbstractController
{
    private PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    #[Route('/properties', name: 'get_properties', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getProperties(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json([
            'user' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'properties' => $this->propertyService->getProperties()
        ]);
    }
}
