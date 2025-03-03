<?php

namespace App\Controller;

use App\Entity\DossierMedicale;
use App\Repository\DossierMedicaleRepository;
use App\Repository\SejourRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/translate/{targetLang}/{id}', name: 'api_translate', methods: ['GET'])]
    public function translate(string $targetLang, DossierMedicale $dossierMedicale): JsonResponse
    {
        try {
            // Préparer le contenu à traduire
            $content = [
                'patient_info' => [
                    'title' => 'Informations du Patient',
                    'name' => $dossierMedicale->getPatient()->getNomUser() . ' ' . $dossierMedicale->getPatient()->getPrenomUser(),
                    'birth_date' => $dossierMedicale->getPatient()->getDateNaissance() ? $dossierMedicale->getPatient()->getDateNaissance()->format('d/m/Y') : 'Non spécifié'
                ],
                'medical_info' => [
                    'title' => 'Informations Médicales',
                    'doctor' => $dossierMedicale->getMedecin()->getNomUser() . ' ' . $dossierMedicale->getMedecin()->getPrenomUser(),
                    'creation_date' => $dossierMedicale->getDateDeCreation() ? $dossierMedicale->getDateDeCreation()->format('d/m/Y') : '',
                    'status' => $dossierMedicale->getStatutDossier(),
                    'history' => $dossierMedicale->getHistoriqueDesMaladies(),
                    'operations' => $dossierMedicale->getOperationsPassees(),
                    'consultations' => $dossierMedicale->getConsultationsPassees(),
                    'notes' => $dossierMedicale->getNotes()
                ]
            ];

            // Utiliser l'API MyMemory Translation
            $url = 'https://api.mymemory.translated.net/get';
            
            // Traduire chaque section
            $translatedContent = [];
            foreach ($content as $section => $data) {
                foreach ($data as $key => $text) {
                    if ($text) {
                        $response = $this->httpClient->request('GET', $url, [
                            'query' => [
                                'q' => $text,
                                'langpair' => 'fr|' . $targetLang
                            ]
                        ]);

                        $result = $response->toArray();
                        $translatedContent[$section][$key] = $result['responseData']['translatedText'];
                    } else {
                        $translatedContent[$section][$key] = '';
                    }
                }
            }

            // Formater le HTML traduit
            $html = "
                <h5>{$translatedContent['patient_info']['title']}</h5>
                <p><strong>Nom:</strong> {$translatedContent['patient_info']['name']}</p>
                <p><strong>Date de naissance:</strong> {$translatedContent['patient_info']['birth_date']}</p>
                
                <h5>{$translatedContent['medical_info']['title']}</h5>
                <p><strong>Médecin traitant:</strong> {$translatedContent['medical_info']['doctor']}</p>
                <p><strong>Date de création:</strong> {$translatedContent['medical_info']['creation_date']}</p>
                <p><strong>Statut:</strong> {$translatedContent['medical_info']['status']}</p>
            ";

            if ($translatedContent['medical_info']['history']) {
                $html .= "<p><strong>Historique des maladies:</strong> {$translatedContent['medical_info']['history']}</p>";
            }
            if ($translatedContent['medical_info']['operations']) {
                $html .= "<p><strong>Opérations passées:</strong> {$translatedContent['medical_info']['operations']}</p>";
            }
            if ($translatedContent['medical_info']['consultations']) {
                $html .= "<p><strong>Consultations passées:</strong> {$translatedContent['medical_info']['consultations']}</p>";
            }
            if ($translatedContent['medical_info']['notes']) {
                $html .= "<p><strong>Notes:</strong> {$translatedContent['medical_info']['notes']}</p>";
            }

            return new JsonResponse([
                'success' => true,
                'translatedContent' => $html
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la traduction: ' . $e->getMessage()
            ]);
        }
    }

    #[Route('/patient/search', name: 'api_patient_search', methods: ['GET'])]
    public function search(
        Request $request,
        UserRepository $userRepository,
        DossierMedicaleRepository $dossierMedicaleRepository,
        SejourRepository $sejourRepository
    ): JsonResponse {
        $query = $request->query->get('q');
        $type = $request->query->get('type', 'patient');
        
        $results = [];
        
        if ($type === 'patient') {
            $patients = $userRepository->searchPatients($query);
            foreach ($patients as $patient) {
                $dossiers = $dossierMedicaleRepository->findBy(['patient' => $patient]);
                $sejours = $sejourRepository->findBy(['patient' => $patient]);
                
                $results[] = [
                    'id' => $patient->getId(),
                    'text' => sprintf(
                        '%s %s (%s dossiers, %s séjours)',
                        $patient->getNomUser(),
                        $patient->getPrenomUser(),
                        count($dossiers),
                        count($sejours)
                    ),
                    'nom' => $patient->getNomUser(),
                    'prenom' => $patient->getPrenomUser(),
                    'dossiers' => count($dossiers),
                    'sejours' => count($sejours)
                ];
            }
        } elseif ($type === 'medecin') {
            $medecins = $userRepository->searchMedecins($query);
            foreach ($medecins as $medecin) {
                $dossiers = $dossierMedicaleRepository->findBy(['medecin' => $medecin]);
                
                $results[] = [
                    'id' => $medecin->getId(),
                    'text' => sprintf(
                        '%s %s (%s dossiers)',
                        $medecin->getNomUser(),
                        $medecin->getPrenomUser(),
                        count($dossiers)
                    ),
                    'nom' => $medecin->getNomUser(),
                    'prenom' => $medecin->getPrenomUser(),
                    'dossiers' => count($dossiers)
                ];
            }
        }
        
        return $this->json(['results' => $results]);
    }
}