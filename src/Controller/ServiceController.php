<?php

  // src/Controller/ServiceController.php

namespace App\Controller;

use App\Entity\Service;
use App\Form\FormNameType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('admin/services_list.html.twig', [  // Correct template path for your services list
            'services' => $serviceRepository->findAll(),
        ]);
    }
    

    
    #[Route('/service/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(FormNameType::class, $service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index');
        }

        return $this->render('admin/services_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/service/{id}/edit', name: 'app_service_edit')]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        // Check if it's a POST request (form submission)
        if ($request->isMethod('POST')) {
            // Get the data from the form
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $price = $request->request->get('price');
    
            // Update the service
            $service->setName($name);
            $service->setDescription($description);
            $service->setPrice($price);
    
            // Save the updated service
            $entityManager->flush();
    
            // Redirect to the service list
            return $this->redirectToRoute('app_service_index');
        }
    
        // If it's a GET request, render the edit page with the current service data
        return $this->render('admin/edit.html.twig', [
            'service' => $service,
        ]);
    }
    

    #[Route('/service/{id}', name: 'app_service_delete', methods: ['POST'])]
public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
        $entityManager->remove($service);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_service_index');
}

}
