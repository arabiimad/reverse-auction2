<?php
// src/Controller/ClientDashboardController.php

namespace App\Controller;

use App\Entity\Auction;
use App\Repository\AuctionRepository;
use App\Repository\BidRepository;
use App\Repository\PurchaseRepository;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ClientDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(
        WalletRepository $walletRepo,
        BidRepository $bidRepo,
        PurchaseRepository $purchaseRepo,
        TransactionRepository $transactionRepo,
        AuctionRepository $auctionRepo
    ): Response {
        $user = $this->getUser();

        // solde
        $wallet = $walletRepo->findOneBy(['user' => $user]);

        // achats de jetons
        $purchases = $purchaseRepo->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        // enchères ouvertes
        $openAuctions = $auctionRepo->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', Auction::STATUS_OPEN)
            ->orderBy('a.endAt', 'ASC')
            ->getQuery()
            ->getResult();

        // historique transactions
        $transactions = $transactionRepo->findBy(
            ['wallet' => $wallet],
            ['createdAt' => 'DESC']
        );

        return $this->render('dashboard/index.html.twig', [
            'wallet'       => $wallet,
            'purchases'    => $purchases,
            'openAuctions' => $openAuctions,
            'transactions' => $transactions,
        ]);
    }
}
