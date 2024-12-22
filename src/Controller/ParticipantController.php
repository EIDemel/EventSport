<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    #[Route('/events/{eventId}/participants/new', name: 'add_participant')]
    public function addParticipant(int $eventId, Request $request, EntityManagerInterface $em): Response
    {
        $event = $em->getRepository(Event::class)->find($eventId);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $participant = new Participant();
        $participant->setEvent($event);

        $form = $this->createFormBuilder($participant)
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('view_event', ['id' => $eventId]);
        }

        return $this->render('participant/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
