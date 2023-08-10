<?php

namespace App\Controller;

use App\Entity\ChargingStation;
use App\Repository\ChargingStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChargingStationController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    #[Route('/charging/station', name: 'app_charging_station')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ChargingStationController.php',
        ]);
    }

    #[Route('/charging/stations ', name: 'app_charging_station', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): Response
    {
        $data = $request->request->all();

        $chargingStation = (new ChargingStation())
            ->setName($data['name'])
            ->setLocation($data['location'])
            ->setStatus($data['status']);
;

        // Persist the ChargingStation object
        $this->entityManager->persist($chargingStation);
        $this->entityManager->flush();

        return $this->json(['message' => 'Charging station created'], Response::HTTP_CREATED);
    }

    #[Route('/charging-stations/{id<\d+>?}', methods: ['GET'])]
    public function read(?int $id, SerializerInterface $serializer): Response
    {
        $chargingStationRepo = $this->entityManager->getRepository(ChargingStation::class);
        if($id !== null) {
            $chargingStation = $chargingStationRepo->find($id);
            if (!$chargingStation) {
                throw $this->createNotFoundException('Charging station not found');
            }
        } else {
            $chargingStation = $chargingStationRepo->findAll();
        }

        $json = $serializer->serialize($chargingStation, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
