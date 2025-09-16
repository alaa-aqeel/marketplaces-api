<?php
// app/Contracts/PaymentGateway.php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    public function process(array $data);
    public function refund(string $id, float|null $amount = null);
    public function getStatus(string $id);
}
