<?php

namespace App\Controller\Admin;

use App\Entity\Auction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/closed-auctions')]
#[IsGranted('ROLE_ADMIN')]
class ClosedAuctionController extends AbstractController
{
    #[Route('', name: 'admin_closed_auctions')]
    public function index(EntityManagerInterface $em): Response
    {
        $auctions = $em->getRepository(Auction::class)->findBy(['status' => 'CLOSED']);

        return $this->render('admin/closed_auctions.html.twig', [
            'auctions' => $auctions,
        ]);
    }
}
