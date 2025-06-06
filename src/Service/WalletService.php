<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class WalletService
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Ajoute des jetons au portefeuille de l’utilisateur.
     */
    public function credit(User $user, int $amount, string $type, ?int $referenceId = null): void
    {
        $wallet = $this->getOrCreateWallet($user);
        $wallet->setBalance($wallet->getBalance() + $amount);

        $this->record($wallet, $type, $amount, $referenceId);
    }

    /**
     * Débite des jetons (mise, achat, etc.).
     */
    public function debit(User $user, int $amount, string $type, ?int $referenceId = null): void
    {
        $wallet = $this->getOrCreateWallet($user);

        if ($wallet->getBalance() < $amount) {
            throw new AccessDeniedException('Solde insuffisant.');
        }

        $wallet->setBalance($wallet->getBalance() - $amount);

        $this->record($wallet, $type, -$amount, $referenceId);
    }

    /**
     * Remboursement de jetons.
     */
    public function refund(User $user, int $amount, ?int $referenceId = null): void
    {
        $this->credit($user, $amount, 'REFUND', $referenceId);
    }

    // --------------------------------------------------------------------- //
    // Helpers
    // --------------------------------------------------------------------- //

    private function getOrCreateWallet(User $user): Wallet
    {
        if (!$user->getWallet()) {
            $wallet = (new Wallet())
                ->setUser($user)
                ->setBalance(0)
                ->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($wallet);
            $user->setWallet($wallet);
        }

        return $user->getWallet();
    }

    private function record(Wallet $wallet, string $type, int $amount, ?int $referenceId): void
    {
    $wallet->setUpdatedAt(new \DateTimeImmutable());

    $tx = (new Transaction())
        ->setWallet($wallet)
        ->setType($type)
        ->setAmount($amount)
        ->setReferenceId($referenceId)
        ->setCreatedAt(new \DateTimeImmutable())
        ->setCreatedId($wallet->getUser()->getId());  // ← ajouté

    $this->em->persist($tx);
        // Pas de flush ici : le contrôleur ou la commande l’effectuera.
    }
}
