<?php
  // src/Controller/ConsultationController.php

namespace App\Controller;
use App\Entity\Service; // This is the correct import for the Service entity

use App\Entity\Consultation;
use App\Form\ConsultationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
final class ConsultationController extends AbstractController
{
    //#[Route('/consultation', name: 'app_consultation')]
 //   public function index(Request $request, EntityManagerInterface $entityManager): Response
 //   {
   //     $consultation = new Consultation();
     //   $form = $this->createForm(ConsultationType::class, $consultation);

       // $form->handleRequest($request);

    //    if ($form->isSubmitted() && $form->isValid()) {
            // Get the patient identifier from the form
    //        $patientIdentifier = $consultation->getPatientIdentifier();

      //      if ($patientIdentifier) {
                // Check if this patient already has a consultation
        //        $existingConsultation = $entityManager->getRepository(Consultation::class)
          //          ->findOneBy(['patientIdentifier' => $patientIdentifier]);

            //    if ($existingConsultation) {
              //      $this->addFlash('error', 'A consultation for this patient already exists.');
                //    return $this->redirectToRoute('app_consultation');
       //         }

                // Persist the consultation
         //       $entityManager->persist($consultation);
           //     $entityManager->flush();

                // Redirect to the consultations page for this patient
             //   return $this->redirectToRoute('app_patient_consultations', [
               //     'patientIdentifier' => $patientIdentifier
         //       ]);
     //       } else {
       //         $this->addFlash('error', 'Patient identifier is missing.');
         //       return $this->redirectToRoute('app_consultation');
     //       }
   //     }

     //   return $this->render('consultation/new.html.twig', [
     //       'controller_name' => 'ConsultationController',
       //     'form' => $form->createView(),
   //     ]);
  //  }


















// src/Controller/ConsultationController.php
 

      

// src/Controller/ConsultationController.php

 


// src/Controller/ConsultationController.php

 

// src/Controller/ConsultationController.php
 
   

   // src/Controller/ConsultationController.php

 // src/Controller/ConsultationController.php
 #[Route('/consultation', name: 'app_consultation', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $consultation = new Consultation();
    $form = $this->createForm(ConsultationType::class, $consultation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $patientIdentifier = $consultation->getPatientIdentifier();

        // Check if the patient already has a consultation
        $existingConsultation = $entityManager->getRepository(Consultation::class)
            ->findOneBy(['patientIdentifier' => $patientIdentifier]);

        if ($existingConsultation) {
            // If consultation exists, show an error message
            $this->addFlash('error', 'A consultation for this patient already exists.');
            return $this->redirectToRoute('app_consultation'); // Redirect to the consultation form
        }

        // Persist the consultation if no existing consultation was found
        $entityManager->persist($consultation);
        $entityManager->flush();

        // Redirect to the confirmation page
        return $this->redirectToRoute('app_consultation_confirmation', [
            'patientIdentifier' => $patientIdentifier,
        ]);
    }

    // Pass services to the view
    $services = $entityManager->getRepository(Service::class)->findAll();

    return $this->render('consultation/new.html.twig', [
        'form' => $form->createView(),
        'services' => $services,
    ]);
}

// src/Controller/ConsultationController.php

// Add the following route for confirmation
#[Route('/consultation/confirmation/{patientIdentifier}', name: 'app_consultation_confirmation')]
public function consultationConfirmation(string $patientIdentifier, EntityManagerInterface $entityManager): Response
{
    // Fetch any additional consultation details if necessary (e.g., patient's consultations)
    // For simplicity, we'll just display a confirmation message for now.

    return $this->render('consultation/confirmation.html.twig', [
        'patientIdentifier' => $patientIdentifier,
    ]);
}















    #[Route('/patient/consultations/{patientIdentifier}', name: 'app_patient_consultations')]
    public function patientConsultations(string $patientIdentifier, EntityManagerInterface $entityManager): Response
    {
        // Retrieve consultations based on the patient's unique identifier
        $consultations = $entityManager->getRepository(Consultation::class)
            ->findBy(['patientIdentifier' => $patientIdentifier]);

        if (!$consultations) {
            $this->addFlash('info', 'No consultations found for this patient.');
        }

        return $this->render('consultation/view.html.twig', [
            'consultations' => $consultations,
            'patientIdentifier' => $patientIdentifier,
        ]);
    }

    #[Route('/consultation/search', name: 'app_search_consultation')]
    public function searchConsultation(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a form to enter the patient identifier
        $form = $this->createFormBuilder()
            ->add('patientIdentifier', TextType::class, [
                'label' => 'Enter Patient Identifier',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $patientIdentifier = $form->getData()['patientIdentifier'];

            // Check if consultations exist for the given patient identifier
            return $this->redirectToRoute('app_patient_consultations', [
                'patientIdentifier' => $patientIdentifier,
            ]);
        }

        return $this->render('consultation/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/consultation/{id}/edit', name: 'app_edit_consultation')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Find the consultation by ID
        $consultation = $entityManager->getRepository(Consultation::class)->find($id);

        if (!$consultation) {
            $this->addFlash('error', 'Consultation not found.');
            return $this->redirectToRoute('app_consultation');
        }

        // Create the form to edit the consultation
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the updated consultation data
            $entityManager->flush();

            // Redirect to the patient's consultation page
            return $this->redirectToRoute('app_patient_consultations', [
                'patientIdentifier' => $consultation->getPatientIdentifier(),
            ]);
        }

        return $this->render('consultation/edit.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }

    #[Route('/consultation/{id}/delete', name: 'app_delete_consultation')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        // Find the consultation by ID
        $consultation = $entityManager->getRepository(Consultation::class)->find($id);

        if (!$consultation) {
            $this->addFlash('error', 'Consultation not found.');
            return $this->redirectToRoute('app_consultation');
        }

        // Remove the consultation
        $entityManager->remove($consultation);
        $entityManager->flush();

        // Redirect to the patient's consultation list after deletion
        $this->addFlash('success', 'Consultation successfully deleted.');
        return $this->redirectToRoute('app_patient_consultations', [
            'patientIdentifier' => $consultation->getPatientIdentifier(),
        ]);
















        
    }









    // src/Controller/ConsultationController.php

#[Route('/consultation/search', name: 'app_search_consultation')]
public function search1Consultation(Request $request, EntityManagerInterface $entityManager): Response
{
    // Create a form to enter the patient identifier
    $form = $this->createFormBuilder()
        ->add('patientIdentifier', TextType::class, [
            'label' => 'Enter Patient Identifier',
        ])
        ->add('search', SubmitType::class, ['label' => 'Search'])
        ->getForm();

    $form->handleRequest($request);

    // If the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        $patientIdentifier = $form->getData()['patientIdentifier'];

        // Fetch consultations based on the patient identifier
        $consultations = $entityManager->getRepository(Consultation::class)
            ->findBy(['patientIdentifier' => $patientIdentifier]);

        // If consultations are found, pass them to the view
        if ($consultations) {
            return $this->render('consultation/view.html.twig', [
                'consultations' => $consultations,
                'patientIdentifier' => $patientIdentifier,
            ]);
        } else {
            $this->addFlash('error', 'No consultations found for this patient.');
        }
    }

    return $this->render('consultation/search.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
