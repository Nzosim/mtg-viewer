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
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{   

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Parameter(name: 'page', description: 'Page number', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'setCode', description: 'setcode', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Get(description: 'List all cards')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(Request $request): Response
    {
        $this->logger->info('Debut du chargement de la liste de carte');
        $startTime = microtime(true);

        $page = $request->query->get('page', 1);
        $limit = 100;
        $setcode = $request->query->get('setCode');
        $query = null;
        if($setcode === null) {
            $query = $this->entityManager->getRepository(Card::class)->createQueryBuilder('c')
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit)
                ->getQuery();
        } else {
            $query = $this->entityManager->getRepository(Card::class)->createQueryBuilder('c')
                ->where('c.setCode = :setcode')
                ->setParameter('setcode', $setcode)
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit)
                ->getQuery();
        }

        $cards = new Paginator($query);

        $this->logger->info($cards->count());
        if($cards->count() === 0) {
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
    #[OA\Parameter(name: 'setCode', description: 'setcode', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Get a card by name')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardByName(string $name, Request $request): Response 
    {
        $this->logger->info('Debut du chargement de la carte ' . $name);
        $setcode = $request->query->get('setCode');
        $startTime = microtime(true);
        if($setcode === null) {
            $cards = $this->entityManager->getRepository(Card::class)->findAll();
        } else {
            $cards = $this->entityManager->getRepository(Card::class)->createQueryBuilder('c')
                ->where('c.setCode = :setcode')
                ->setParameter('setcode', $setcode)
                ->getQuery()
                ->getResult();
        }
        
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
