<?php

namespace Helpers;

class BnpPayHelper
{
    public function getMethods()
    {
        return [
            [
                'method' => 1,
                'name' => 'IGFS'
            ],
            [
                'method' => 2,
                'name' => 'Computop'
            ]
        ];
    }

    public function getTrTypes()
    {
        return [
            [
                'type' => 'PURCHASE',
                'name' => 'Acquisto'
            ],
            [
                'type' => 'AUTH',
                'name' => 'Preautorizzazione'
            ],
            [
                'type' => 'VERIFY',
                'name' => 'Verifica'
            ]
        ];
    }

    public function getCheckoutTypes()
    {
        return [
            [
                'type' => 1,
                'name' => 'Checkout BNLP'
            ],
            [
                'type' => 2,
                'name' => 'Checkout BNLP con sintesi in web store'
            ],
            [
                'type' => 3,
                'name' => 'Checkout BNLP con selezione strumento di pagamento su web store'
            ]
        ];
    }

    public function getStatus()
    {
        return [

            'EN' => [
                'pending' => 'Pending Payment',
                'processing' => 'Processing',
                'on_hold' => 'On Hold',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded',
                'failed' => 'Failed',
            ],
            'IT' => [
                'pending' => 'In attesa di pagamento',
                'processing' => 'In lavorazione',
                'on_hold' => 'In sospeso',
                'completed' => 'Completato',
                'cancelled' => 'Cancellato',
                'refunded' => 'Rimborsato',
                'failed' => 'Fallito',
            ]
        ];
    }

    public function isTokenEnabled($value)
    {
        if ($value === 1)
            return true;

        return false;

    }



}