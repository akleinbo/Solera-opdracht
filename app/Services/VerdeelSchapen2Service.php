<?php

namespace App\Services;

class VerdeelSchapen2Service
{

    public function verdeelSchapen(int $aantalSchapen, array $stallen): string
    {
        // controleer of alle schapen een minimale oppervlakte van 2 m2 hebben
        if (!$this->kunnenWeVerdelen($stallen, $aantalSchapen, 2)) {
            return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
        }

        // 3 schapen regel, let op, 1 stal
        // bij een situatie van 3 schapen en slechts 1 stal kunnen we dit goedkoop controleren
        if ($aantalSchapen === 3 && count($stallen) === 1) {
            return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
        }

        // controleer welke verdeling mogelijk is
        $result = $this->controleerWelkeVerdelingMogelijkIs($aantalSchapen, $stallen);

        return number_format($result, 1, ',', '.');
    }

    public function controleerWelkeVerdelingMogelijkIs(int $aantalSchapen, array $stallen): float
    {
        // minimale oppervlakte per schaap
        $low = 1.9;

        // maximale oppervlakte per schaap mogelijk
        $high = max($stallen);

        $result = 0.0;
        while ($high - $low > 0.0001) {
            $mid = ($low + $high) / 2;

            if ($this->kunnenWeVerdelen($stallen, $aantalSchapen, $mid)) {
                $result = $mid;
                $low = $mid;
            } else {
                $high = $mid;
            }
        }

        return $result;
    }

    function kunnenWeVerdelen(array $stallen, int $aantalSchapen, float $minOppervlakte): bool
    {
        rsort($stallen);

        $remaining = $aantalSchapen;

        foreach ($stallen as $stal) {

            $capacity = (int) floor($stal / $minOppervlakte);

            if ($capacity === 3) {
                $capacity = 2;
            }

            $use = min($capacity, $remaining);

            $remaining -= $use;

            if ($remaining <= 0) {
                return true;
            }
        }

        return false;
    }
}
