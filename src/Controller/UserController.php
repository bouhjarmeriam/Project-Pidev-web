<?php



namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index(): Response
    {
        // You can render a Twig template or return a JSON response
        return $this->render('admin/users.html.twig');
    }
}


