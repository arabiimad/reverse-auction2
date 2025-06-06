<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Bid;
use App\Form\BidType;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bids')]
class BidController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private WalletService $walletService
    ) {}

    #[Route('', name: 'bids_index')]
    public function index(): Response
    {
        $auctions = $this->em->getRepository(Auction::class)->findBy(['status' => 'OPEN']);

        return $this->render('auction/index.html.twig', [
            'auctions' => $auctions,
        ]);
    }

        #[Route('/mes-mises', name: 'bids_user')]
#[IsGranted('ROLE_USER')]
public function userBids(): Response
{
    $bids = $this->em->getRepository(Bid::class)->findBy(
        ['user' => $this->getUser()],
        ['createdAt' => 'DESC']
    );

    return $this->render('bid/user_bids.html.twig', [
        'bids' => $bids,
    ]);
}

    #[Route('/{id}', name: 'bids_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Auction $auction): Response
    {
        $bid = new Bid();
        $form = $this->createForm(BidType::class, $bid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bid->setAuction($auction);
            $bid->setUser($this->getUser());
            $bid->setCreatedAt(new \DateTimeImmutable());

            // Débit des jetons
            $this->walletService->debit($this->getUser(), $auction->getBidCost(), 'BID', $auction->getId());

            $this->em->persist($bid);
            $this->em->flush();

            $this->addFlash('success', 'Votre mise a été enregistrée !');
            return $this->redirectToRoute('bids_index');
        }

        return $this->render('auction/show.html.twig', [
            'auction' => $auction,
            'form' => $form->createView(),
        ]);
    }
}
