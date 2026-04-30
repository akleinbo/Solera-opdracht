<?php

namespace App\Services;

class VerdeelSchapen2Service
{
    public function verdeelSchapen(int $aantalSchapen, array $stallen): string
    {
        if ($aantalSchapen <= 0 || empty($stallen)) {
            return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
        }

        if (!$this->bestaatMinimaleOplossing($aantalSchapen, $stallen, 2.0)) {
            return 'Het ingevoerde aantal schapen kan niet worden ondergebracht in de stallen!';
        }

        $result = $this->zoekMaxMinOppervlakte($aantalSchapen, $stallen);

        return number_format($result, 1, ',', '.');
    }

    private function zoekMaxMinOppervlakte(int $aantalSchapen, array $stallen): float
    {
        $low = 1.0;
        $high = max($stallen);
        $best = 0.0;

        while ($high - $low > 0.0001) {
            $mid = ($low + $high) / 2;

            if ($this->kanVerdelenZonder3($stallen, $aantalSchapen, $mid)) {
                $best = $mid;
                $low = $mid;
            } else {
                $high = $mid;
            }
        }

        return $best;
    }

    private function bestaatMinimaleOplossing(int $aantalSchapen, array $stallen, float $minimaleOppervlakte): bool
    {
        return $this->kanVerdelenZonder3($stallen, $aantalSchapen, $minimaleOppervlakte);
    }

    private function kanVerdelenZonder3(array $stallen, int $aantalSchapen, float $minimaleOppervlakte): bool
    {
        $capaciteiten = [];

        foreach ($stallen as $stal) {
            $capaciteiten[] = (int) floor($stal / $minimaleOppervlakte);
        }

        if (array_sum($capaciteiten) < $aantalSchapen) {
            return false;
        }

        return $this->kanToewijzingMaken($capaciteiten, $aantalSchapen);
    }

    private function kanToewijzingMaken(array $capaciteiten, int $remaining): bool
    {
        rsort($capaciteiten);

        return $this->backtrack($capaciteiten, $remaining, 0);
    }

    private function backtrack(array $caps, int $remaining, int $index): bool
    {
        if ($remaining === 0) {
            return true;
        }

        if ($index >= count($caps)) {
            return false;
        }

        $max = min($caps[$index], $remaining);

        for ($i = $max; $i >= 0; $i--) {

            if ($i === 3) {
                continue;
            }

            if ($this->backtrack($caps, $remaining - $i, $index + 1)) {
                return true;
            }
        }

        return false;
    }
}
