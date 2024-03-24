<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{   

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Put(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(): Response
    {
        $this->logger->info('Debut du chargement de la liste de carte');
        $startTime = microtime(true);
        $cards = $this->entityManager->getRepository(Card::class)->findAll();
        if(!$cards) {
            $this->logger->error('Aucune carte trouvée');
            return $this->json(['error' => 'No card found'], 404);
        }
        $endTime = microtime(true);
        $this->logger->info('Chargement de la liste de carte réussi apres : ' . ($endTime - $startTime) . ' secondes');
        return $this->json($cards);
    }

    #[Route('/{uuid}', name: 'Show card', methods: ['GET'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $this->logger->info('Debut du chargement de la carte ' . $uuid);
        $startTime = microtime(true);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
        if (!$card) {
            $this->logger->error('Carte ' . $uuid . ' non trouvée');
            return $this->json(['error' => 'Card not found'], 404);
        }
        $endTime = microtime(true);
        $this->logger->info('Chargement de la carte ' . $uuid . ' réussi apres ' . ($endTime - $startTime) . ' secondes');
        return $this->json($card);
    }

    #[Route('/name/{name}', name: 'Show card by name', methods: ['GET'])]
    #[OA\Parameter(name: 'name', description: 'Name of the card', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Get a card by name')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardByName(string $name): Response 
    {
        $this->logger->info('Debut du chargement de la carte ' . $name);
        $startTime = microtime(true);
        $cards = $this->entityManager->getRepository(Card::class)->findAll();
        $res = [];
        foreach ($cards as $card) {
            if (strpos($card->getName(), $name) !== false) {
                $res[] = $card;
            }
        }
        
        $endTime = microtime(true);
        $this->logger->info('Chargement des carte qui contienent ' . $name . ' réussi apres ' . ($endTime - $startTime) . ' secondes');
        return $this->json($res);
    }
}
