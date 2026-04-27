<?php

namespace App\Console\Commands;

use App\Services\VerdeelSchapenService;
use Exception;
use Illuminate\Console\Command;

class VerdeelSchapen extends Command
{
    public const int MIN_AANTAL_M2_PER_SCHAAP = 2;
    protected $signature = 'app:sheep-plan
                            {aantalSchapen : Aantal schapen (int)}
                            {stallen : Komma-gescheiden lijst m2, bv 10,15,8}';

    protected $description = 'Kijk of je de schapen kan verdelen';

    public function __construct(VerdeelSchapenService $verdeelSchapenService) {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(VerdeelSchapenService $verdeelSchapen)
    {
        $aantalSchapen = (int) $this->argument('aantalSchapen');
        $stallen = $this->argument('stallen');

        $stallenArray = explode(',', $stallen);

        return $this->info($verdeelSchapen->verdeelSchapen($aantalSchapen, $stallenArray));
    }

}
