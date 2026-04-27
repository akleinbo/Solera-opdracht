<?php

namespace App\Services;

class VerdeelSchapenService
{
    public function verdeelSchapen(int $aantalSchapen, array $stallen): string
    {
        $minimaleAantalVierkanteMetersPerSchaap = 2;
        $totaalAantalVierkanteMetersStallen = array_sum($stallen);
        $totaalAantalVierkanteMetersPerSchaap = ($totaalAantalVierkanteMetersStallen / $aantalSchapen);

        // dit is een globale check of het aantal schapen is onder te verdelen in het totale aantal vierkante meters die beschikbaar zijn in alle stallen opgeteld
        if ($totaalAantalVierkanteMetersPerSchaap < $minimaleAantalVierkanteMetersPerSchaap) {
            return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
        }

        // nu de globale check positief is moeten we kijken hoe we de schapen kunnen onder verdelen over de stallen
        $restSchapen = $aantalSchapen;
        $results = [];

        foreach ($stallen as $key => $stal) {

            if ($restSchapen <= 0) {
                break;
            }

            // max capaciteit aantal schapen per stal
            $capaciteit = (int) floor($stal / $minimaleAantalVierkanteMetersPerSchaap);

            if ($capaciteit <= 0) {
                continue;
            }

            // Plaats nooit meer schapen dan nodig
            $schapen = min($restSchapen, $capaciteit);

            // Exact 3 schapen verboden
            // Dit is eigenlijk niet een mooie oplossing, beter zou zijn een recursieve methode waarbij we een mogelijke verdeling zoeken over de stallen
            if ($schapen === 3) {
                $schapen = ($capaciteit >= 2) ? 2 : 1;
            }

            // maak de verdeling
            $results[] = [
                'key' => $key,
                'oppervlakteStal' => $stal,
                'maximaalAantalSchapenInStal' => $schapen,
                'oppervlaktePerSchaap' => $stal / $schapen,
            ];

            // schapen in de stal aftrekken van huidige aantal schapen
            $restSchapen -= $schapen;
        }

        // controleren of alle schapen in de stallen zijn onder gebracht
        if ($restSchapen > 0) {
            return 'Niet alle schapen passen in de stallen!';
    	}

        $gemiddeldeOppervlaktePerSchaap = array_sum(array_column($results, 'oppervlakteStal')) / $aantalSchapen;

        return number_format($gemiddeldeOppervlaktePerSchaap, 1, ',', '.');
    }
}
