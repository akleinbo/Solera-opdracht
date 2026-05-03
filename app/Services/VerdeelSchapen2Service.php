<?php

namespace App\Services;

class VerdeelSchapen2Service
{
    /** Minimaal aantal vierkante meters per schaap */
    private const float MIN_M2_PER_SCHAAP = 2.0;

    /** Precisie van de binary search (stopt als interval kleiner is dan dit) */
    private const float PRECISIE = 0.001;

    /**
     * Bepaal het maximale aantal m2 per schaap bij een optimale verdeling.
     *
     * We zoeken de hoogst mogelijke oppervlakte per schaap waarvoor nog een geldige verdeling mogelijk is
     */
    public function verdeelSchapen(int $aantalSchapen, array $stallen): string
    {
        // Basale check
        if ($aantalSchapen <= 0 || empty($stallen)) {
            return $this->foutmelding();
        }

        // Controleer of het mogelijk is met het minimum van 2 m2 per schaap
        if (!$this->isVerdelingMogelijk($stallen, $aantalSchapen, self::MIN_M2_PER_SCHAAP)) {
            return $this->foutmelding();
        }

        $laag  = self::MIN_M2_PER_SCHAAP; // ondergrens
        $hoog  = (float) max($stallen);   // bovengrens
        $beste = $laag;                   // beste gevonden waarde

        while ($hoog - $laag > self::PRECISIE) {
            $midden = ($laag + $hoog) / 2;

            if ($this->isVerdelingMogelijk($stallen, $aantalSchapen, $midden)) {
                $beste = $midden;
                $laag  = $midden;
            } else {
                $hoog = $midden;
            }
        }

        return number_format($beste, 1, ',', '.');
    }

    /**
     * Controleer of het mogelijk is om $aantalSchapen schapen te verdelen over de gegeven stallen met minimaal $m2PerSchaap m2 per schaap.
     */
    private function isVerdelingMogelijk(array $stallen, int $aantalSchapen, float $m2PerSchaap): bool
    {
        // $bereikbaar[$n] = true betekent: we kunnen exact $n schapen plaatsen met de stallen die tot nu toe verwerkt zijn
        $bereikbaar    = array_fill(0, $aantalSchapen + 1, false);
        $bereikbaar[0] = true; // 0 schapen plaatsen is altijd mogelijk (beginpunt)

        foreach ($stallen as $stalOppervlakte) {
            // Hoeveel schapen passen er maximaal in deze stal?
            $capaciteit = (int) floor($stalOppervlakte / $m2PerSchaap);

            // Precies 3 schapen in één stal is verboden; alle andere aantallen zijn ok.
            $toegestaneAantallen = [];
            for ($schapenInStal = 0; $schapenInStal <= $capaciteit; $schapenInStal++) {
                if ($schapenInStal === 3) {
                    continue;
                }
                $toegestaneAantallen[] = $schapenInStal;
            }

            $nieuwBereikbaar = $bereikbaar;

            for ($huidigTotaal = 0; $huidigTotaal <= $aantalSchapen; $huidigTotaal++) {

                if (!$bereikbaar[$huidigTotaal]) {
                    continue;
                }

                foreach ($toegestaneAantallen as $extraSchapen) {
                    $nieuwTotaal = $huidigTotaal + $extraSchapen;

                    if ($nieuwTotaal <= $aantalSchapen) {
                        $nieuwBereikbaar[$nieuwTotaal] = true;
                    }
                }
            }

            $bereikbaar = $nieuwBereikbaar;
        }

        return $bereikbaar[$aantalSchapen];
    }

    private function foutmelding(): string
    {
        return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
    }
}
