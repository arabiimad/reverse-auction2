<?php
namespace App\Controller;

use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Entity\TokenPack;
use App\Repository\TokenPackRepository;
use App\Repository\WalletRepository;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/purchase')]
#[IsGranted('ROLE_USER')]
class PurchaseController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenPackRepository   $packRepo,
        private WalletRepository      $walletRepo,
        private WalletService         $walletService,
    ) {}

    // 1) Choix du pack
    #[Route('', name: 'purchase_choose', methods: ['GET','POST'])]
    public function choose(Request $request, SessionInterface $session): Response
    {
        $purchase = new Purchase();
        $form     = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('pending_purchase', $purchase->getTokenPack()->getId());
            return $this->redirectToRoute('purchase_confirm');
        }

        return $this->render('purchase/choose.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 2) Confirmation
    #[Route('/confirm', name: 'purchase_confirm')]
    public function confirm(SessionInterface $session): Response
    {
        $packId = $session->get('pending_purchase');
        if (!$packId) {
            return $this->redirectToRoute('purchase_choose');
        }
        $pack   = $this->packRepo->find($packId);
        $wallet = $this->walletRepo->findOneBy(['user' => $this->getUser()]);

        return $this->render('purchase/confirm.html.twig', [
            'pack'   => $pack,
            'wallet' => $wallet,
        ]);
    }

    // 3) Validation finale
    #[Route('/validate', name: 'purchase_validate', methods:['POST'])]
    public function validate(SessionInterface $session): Response
    {
        $packId = $session->get('pending_purchase');
        $session->remove('pending_purchase');
        if (!$packId) {
            return $this->redirectToRoute('purchase_choose');
        }

        $pack     = $this->packRepo->find($packId);
        $user     = $this->getUser();
        $purchase = (new Purchase())
            ->setUser($user)
            ->setTokenPack($pack)
            ->setTotalCents($pack->getPriceCents())
            ->setStatus('paid')
            ->setPaidAt(new \DateTimeImmutable());

        $this->em->persist($purchase);
        // on crédite ensuite le wallet
        $this->walletService->credit(
            $user,
            $pack->getTokens(),
            'PURCHASE',
            $purchase->getId()
        );
        $this->em->flush();

        $this->addFlash('success','Achat confirmé avec succès !');
        return $this->redirectToRoute('dashboard');
    }
}
