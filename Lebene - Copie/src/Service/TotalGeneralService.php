<?php namespace App\Service;

use App\Repository\FactureRepository;

class TotalGeneralService
{
    //private $factureRepo;
    private $totalGeneral = 0;
    private $tvaGeneral = 0;
    private $ttcGeneral = 0;
    private $nombreReduction = 0;
    private $nombreRecuperation = 0;
    private $gainPerdu = 0;

    public function __construct(FactureRepository $factureRepo)
    {
        $this->factureRepo = $factureRepo;
    }
    
    public function totalGeneral()
    {
        
        $facture = $this->factureRepo->findFact();
        //dd($facture);
        foreach ($facture as $item => $value) {
            $reduction = $value->getTauxReduction();

            $factureEtat = $value->getEtat();
            $tva = $value->getTotalTva();
            $ttc = $value->getTotalTtc();
            if ($reduction > 0 && $factureEtat == 'LIVRAISON') {
                $totalGeneral = $this->totalGeneral + $tva;
                $nombreReduction = $nombreReduction + 1;
            } else if ($reduction == 0 && $factureEtat == 'LIVRAISON') {
                $totalGeneral = $totalGeneral + $ttc;
            } else if ($factureEtat == 'ATTENTE') {
                $nombreRecuperation = $nombreRecuperation + 1;
            }

            setTvaGeneral($tvaGeneral + $tva);
            $ttcGeneral = $ttcGeneral + $ttc;
            $gainPerdu = $ttcGeneral - $tvaGeneral;
        }

        $prixDepense = 0;
        $prixPaiement = 0;
        $totalGeneral = $totalGeneral - ($prixDepense + $prixPaiement);
        dd($this->getTvaGeneral);
        return $totalGeneral;
    }

    

    public function getTotalGeneral()
    {
        return $this->totalGeneral;
    }

    public function getTvaGeneral()
    {
        return $this->tvaGeneral;
    }

    public function setTvaGeneral($tvaGeneral)
    {
        $this->tvaGeneral = $tvaGeneral;
    }

    public function getTtcGeneral()
    {
        return $this->ttcGeneral;
    }

    public function getNombreReduction()
    {
        return $this->nombreReduction;
    }

    public function getNombreRecuperation()
    {
        return $this->nombreRecuperation;
    }

    public function getGainPerdu()
    {
        return $this->gainPerdu;
    }

    
}
    ?>
