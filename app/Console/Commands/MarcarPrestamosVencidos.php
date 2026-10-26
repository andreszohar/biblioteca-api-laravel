<?php

namespace App\Console\Commands;

use App\Services\PrestamoService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('prestamos:marcar-vencidos')]
#[Description('Marca como vencidos los préstamos activos que superaron su fecha de devolución estimada')]
class MarcarPrestamosVencidos extends Command
{
    public function handle(PrestamoService $prestamos): int
    {
        $total = $prestamos->marcarVencidos();

        $this->info("Préstamos marcados como vencidos: {$total}");

        return self::SUCCESS;
    }
}