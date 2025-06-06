<?php

namespace App\Controller\Admin;

use App\Entity\Auction;
use App\Entity\Bid;
use App\Entity\Product;
use App\Entity\TokenPack;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $url = $adminUrlGenerator
            ->setController(ProductCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Reverse-Auction Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('🏠 Dashboard', 'fa fa-home');

        yield MenuItem::section('Catalogue');
        yield MenuItem::linkToCrud('Produits', 'fa fa-box', Product::class);
        yield MenuItem::linkToCrud('Packs de jetons', 'fa fa-coins', TokenPack::class);

        yield MenuItem::section('Enchères');
        yield MenuItem::linkToCrud('Enchères', 'fa fa-gavel', Auction::class);
        yield MenuItem::linkToCrud('Mises', 'fa fa-ticket', Bid::class);

        yield MenuItem::section('Retour au site');
        yield MenuItem::linkToUrl('Tableau de bord client', 'fa fa-user', $this->generateUrl('dashboard'));
    }

    /** Ajoute, si tu le souhaites, tes assets custom */
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('build/admin.css');
    }
}
