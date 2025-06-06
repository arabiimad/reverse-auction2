<?php
// src/Controller/Admin/AuctionCrudController.php
namespace App\Controller\Admin;

use App\Entity\Auction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action as EasyAdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

// Champs EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\HttpFoundation\Response;

class AuctionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Auction::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Enchère')
            ->setEntityLabelInPlural('Enchères')
            ->setDefaultSort(['endAt' => 'DESC'])
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        // champ de relation vers Product
        yield AssociationField::new('product', 'Produit');

        // dates de début et de fin
        yield DateTimeField::new('startAt', 'Date de début');
        yield DateTimeField::new('endAt',   'Date de clôture');

        // coût de la mise
        yield IntegerField::new('bidCost', 'Coût de la mise (jetons)');

        // statut (ou tu peux le cacher à la création si tu initialises en OPEN par défaut)
        yield ChoiceField::new('status')
            ->setChoices([
                'Ouvert'   => Auction::STATUS_OPEN,
                'Fermé'    => Auction::STATUS_CLOSED,
            ])
            ->renderExpanded(false)
            ->renderAsBadges([
                Auction::STATUS_OPEN   => 'success',
                Auction::STATUS_CLOSED => 'danger',
            ]);

        // flags paiement / livraison
        yield BooleanField::new('paymentReceived',  'Paiement reçu');
        yield BooleanField::new('productDelivered', 'Produit livré');
    }

    public function configureActions(Actions $actions): Actions
    {
        // bouton « Clôturer » dans la liste & détail
        $close = EasyAdminAction::new('close', 'Clôturer', 'fas fa-lock')
            ->linkToCrudAction('closeAuction')
            ->addCssClass('btn btn-warning');

        return $actions
            ->add(Crud::PAGE_INDEX,  $close)
            ->add(Crud::PAGE_DETAIL, $close);
    }

    public function closeAuction(
        AdminContext $context,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        /** @var Auction $auction */
        $auction = $context->getEntity()->getInstance();
        $auction->setStatus(Auction::STATUS_CLOSED);
        $em->flush();

        $this->addFlash('success', 'Enchère clôturée manuellement.');

        // redirection vers la liste
        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(EasyAdminAction::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
