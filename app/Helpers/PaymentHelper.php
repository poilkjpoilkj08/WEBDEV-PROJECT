<?php

if (!function_exists('formatPaymentMethod')) {
    /**
     * Format payment method for display
     */
    function formatPaymentMethod($method)
    {
        $paymentMethods = [
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'other_va' => 'Other Bank VA',
            'credit_card' => 'Credit Card',
            'qris' => 'QRIS',
            'gopay' => 'GoPay',
            'ovo' => 'OVO',
            'linkaja' => 'LinkAja',
            'dana' => 'DANA',
            'bank_transfer' => 'Bank Transfer',
            'akulaku' => 'Akulaku',
            'kredivo' => 'Kredivo',
            'shopeepay' => 'ShopeePay',
        ];

        return $paymentMethods[$method] ?? ucfirst(str_replace('_', ' ', $method));
    }
}

if (!function_exists('maskCreditCard')) {
    /**
     * Mask credit card number
     */
    function maskCreditCard($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        if (strlen($cardNumber) < 4) {
            return str_repeat('*', strlen($cardNumber));
        }
        return str_repeat('*', strlen($cardNumber) - 4) . substr($cardNumber, -4);
    }
}
