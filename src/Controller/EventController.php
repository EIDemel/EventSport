<?php

namespace App\Controller;

use App\Entity\Event;
use App\Service\DistanceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'list_events')]
    public function listEvents(EntityManagerInterface $em): Response
    {
        $events = $em->getRepository(Event::class)->findAll();
        return $this->render('event/list.html.twig', ['events' => $events]);
    }

    #[Route('/events/{id}', name: 'view_event')]
    public function viewEvent(int $id, EntityManagerInterface $em): Response
    {
        $event = $em->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        return $this->render('event/view.html.twig', ['event' => $event]);
    }

    #[Route('/events/{id}/distance', name: 'event_distance')]
    public function calculateDistanceToEvent(int $id, float $lat, float $lon, EntityManagerInterface $em, DistanceCalculator $calculator): Response
    {
        $event = $em->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $distance = $calculator->calculateDistance($lat, $lon, ...$this->extractLatLon($event->getLocation()));
        return $this->json(['distance' => $distance]);
    }

    private function extractLatLon(string $location): array
    {
        // Assumes location is stored as "lat,lon"
        return array_map('floatval', explode(',', $location));
    }
}