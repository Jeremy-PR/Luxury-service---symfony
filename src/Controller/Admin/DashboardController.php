<?php

namespace App\Controller\Admin;

use App\Entity\Gender;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Expe;
use App\Entity\Professional;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Luxury Services')
            ->setFaviconPath('img/luxury-services-logo.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-tachometer-alt');

        yield MenuItem::section('Jobs');


        yield MenuItem::section('Candidates');
        yield MenuItem::linkToCrud('Genders', 'fas fa-venus-mars', Gender::class);
        
        
        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fas fa-user-tie', User::class);

        yield MenuItem::section('Categories');
        yield MenuItem::linkToCrud('Categories', 'fa fa-sign-language', Category::class);

        yield MenuItem::section('Expes');
        yield MenuItem::linkToCrud('Expes', 'fa fa-sign-language', Expe::class);

        yield MenuItem::section('Professionals');
        yield MenuItem::linkToCrud('Professionnals', 'fa fa-sign-language', Professional::class);

        
    }
}
