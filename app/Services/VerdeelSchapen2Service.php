<?php

namespace App\Services;

class VerdeelSchapen2Service
{
    public function verdeelSchapen(int $aantalSchapen, array $stallen): string
    {
        // basale check
        if ($aantalSchapen <= 0 || empty($stallen)) {
            return $this->fail();
        }

        // minimale check
        if (!$this->kunnenWeVerdelen($stallen, $aantalSchapen, 2.0)) {
            return $this->fail();
        }

        // minimale vereiste oppervlakte
        $low = 2.0;

        // kies de grootste stal
        $high = max($stallen);

        // meest ideale oppervlakte
        $best = 2.0;

        // best haalbare m2 per schaap
        while ($high - $low > 0.001) {
            $oppervlakte = ($low + $high) / 2;

            if ($this->kunnenWeVerdelen($stallen, $aantalSchapen, $oppervlakte)) {
                $best = $oppervlakte;
                $low = $oppervlakte;
            } else {
                $high = $oppervlakte;
            }
        }

        return number_format($best, 1, ',', '.');
    }

    private function kunnenWeVerdelen(array $stallen, int $aantalSchapen, float $oppervlakte): bool
    {
        // maak een array
        $results = array_fill(0, $aantalSchapen + 1, false);
        $results[0] = true;

        foreach ($stallen as $stal) {

            // bereken hoeveel schapen maximaal in deze stal passen gegeven de gewenste oppervlakte per schaap
            $capaciteit = (int) floor($stal / $oppervlakte);

            // bepaal alle toegestane aantallen schapen voor deze stal
            $mogelijk = [];

            for ($i = 0; $i <= $capaciteit; $i++) {
                if ($i === 3) continue;
                $mogelijk[] = $i;
            }

            // nieuwe array
            $next = array_fill(0, $aantalSchapen + 1, false);

            for ($i = 0; $i <= $aantalSchapen; $i++) {

                // als dit aantal nog niet haalbaar was, skippen
                if (!$results[$i]) continue;

                // probeer alle mogelijke aantallen voor deze stal
                foreach ($mogelijk as $m) {

                    // als we binnen de grens blijven, markeer als haalbaar
                    if ($i + $m <= $aantalSchapen) {
                        $next[$i + $m] = true;
                    }
                }
            }

            // update met de nieuwe mogelijkheden na deze stal
            $results = $next;
        }

        // Uiteindelijk checken we of we exact het aantal schapen kunnen plaatsen
        return $results[$aantalSchapen];
    }

    private function fail(): string
    {
        return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
    }
}
