<?php

namespace App\Command;

use App\Entity\Auction;
use App\Entity\Bid;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:close-auctions',
    description: 'Clôture les enchères terminées et gère les remboursements'
)]
class CloseAuctionsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private WalletService $walletService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable();

        // 1) Récupère toutes les enchères encore ouvertes
        /** @var Auction[] $auctions */
        $auctions = $this->em->getRepository(Auction::class)->findBy([
            'status' => Auction::STATUS_OPEN,
        ]);

        foreach ($auctions as $auction) {
            // 2) Vérifie si la date de fin est dépassée
            if ($auction->getEndAt() < $now) {
                // Récupère toutes les mises pour cette enchère
                /** @var Bid[] $bids */
                $bids = $this->em->getRepository(Bid::class)
                                 ->findBy(['auction' => $auction]);

                if (count($bids) === 0) {
                    // ❌ Aucune mise : on clôt sans gagnant et sans remboursement
                    $output->writeln("❌ Enchère #{$auction->getId()} clôturée sans mise");
                } else {
                    // 3) Regroupement des mises par prix
                    $counts = [];
                    foreach ($bids as $bid) {
                        $price = $bid->getPriceCents();
                        $counts[$price] = ($counts[$price] ?? 0) + 1;
                    }
                    // Garde les prix proposés une seule fois
                    $uniquePrices = array_filter($counts, fn($c) => $c === 1);

                    if (!empty($uniquePrices)) {
                        // 🎯 Il existe au moins un prix unique : on choisit le plus bas
                        $lowest = min(array_keys($uniquePrices));
                        foreach ($bids as $bid) {
                            if ($bid->getPriceCents() === $lowest) {
                                $auction->setWinnerBid($bid);
                                $output->writeln("🏆 Enchère #{$auction->getId()} gagnée par mise #{$bid->getId()}");
                                break;
                            }
                        }
                        // 4a) Rembourse toutes les autres mises
                        foreach ($bids as $bid) {
                            if ($bid !== $auction->getWinnerBid()) {
                                $this->walletService->refund(
                                    $bid->getUser(),
                                    $auction->getBidCost(),
                                    $auction->getId()
                                );
                            }
                        }
                    } else {
                        // ❌ Aucun prix unique : pas de gagnant
                        $output->writeln("❌ Enchère #{$auction->getId()} sans gagnant (aucune mise unique)");
                        // 4b) Rembourse TOUTES les mises
                        foreach ($bids as $bid) {
                            $this->walletService->refund(
                                $bid->getUser(),
                                $auction->getBidCost(),
                                $auction->getId()
                            );
                        }
                    }
                }

                // 5) Clôture l’enchère
                $auction->setStatus(Auction::STATUS_CLOSED);
            }
        }

        // 6) Persistance
        $this->em->flush();

        $output->writeln('✅ Clôture des enchères terminée.');
        return Command::SUCCESS;
    }
}
