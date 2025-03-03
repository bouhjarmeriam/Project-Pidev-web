<?php

namespace MyPdfBundle\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use App\Entity\Commande;

class PdfGenerator
{
    private string $defaultFont;
    private Environment $twig;
    private LoggerInterface $logger;

    public function __construct(
        string $defaultFont,
        Environment $twig,
        LoggerInterface $logger
    ) {
        $this->defaultFont = $defaultFont;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function generateCommandePdf(Commande $commande): string
    {
        $this->logger->info('Début génération PDF pour commande ID: '.$commande->getId());
        
        try {
            $options = new Options();
            $options->set('defaultFont', $this->defaultFont);
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('tempDir', sys_get_temp_dir());

            $dompdf = new Dompdf($options);
            
            // Configuration du contexte SSL si nécessaire
            $dompdf->setHttpContext([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $html = $this->twig->render('commande/pdf.html.twig', [
                'commande' => $commande
            ]);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $this->logger->info('PDF généré avec succès pour commande ID: '.$commande->getId());
            
            return $dompdf->output();
            
        } catch (\Throwable $e) {
            $this->logger->error('Erreur génération PDF: '.$e->getMessage());
            throw new \RuntimeException('Échec de la génération du PDF: '.$e->getMessage());
        }
    }
}