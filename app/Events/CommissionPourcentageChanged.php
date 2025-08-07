<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommissionPourcentageChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ancienPourcentage;
    public $nouveauPourcentage;

    public function __construct($ancienPourcentage, $nouveauPourcentage)
    {
        $this->ancienPourcentage = $ancienPourcentage;
        $this->nouveauPourcentage = $nouveauPourcentage;
    }
}