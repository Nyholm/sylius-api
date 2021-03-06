<?php

declare(strict_types=1);

/*
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace FAPI\Sylius\Api;

use FAPI\Sylius\Exception;
use FAPI\Sylius\Exception\Domain as DomainExceptions;
use FAPI\Sylius\Exception\InvalidArgumentException;

/**
 * @author Kasim Taskin <taskinkasim@gmail.com>
 */
final class Checkouts extends HttpApi
{
    const SHIPPING_ADDRESS_FIELDS = [
        'firstName',
        'lastName',
        'city',
        'postcode',
        'street',
        'countryCode',
    ];

    /**
     * @param int $id
     *
     * @throws Exception
     *
     * @return bool
     */
    public function putAddress(int $cartId, array $shippingAddress, bool $differentBillingAddress = false, array $billingAddress = []): bool
    {
        if (empty($cartId)) {
            throw new InvalidArgumentException('Cart id cannot be empty');
        }

        if (empty($shippingAddress)) {
            throw new InvalidArgumentException('Shipping address cannot be empty');
        }

        foreach (self::SHIPPING_ADDRESS_FIELDS as $field) {
            if (empty($shippingAddress[$field])) {
                throw new InvalidArgumentException("Field {$field} missing in shipping address");
            }
        }

        $params = [
            'shippingAddress' => $shippingAddress,
            'differentBillingAddress' => $differentBillingAddress,
            'billingAddress' => $billingAddress,
        ];

        $response = $this->httpPut("/api/v1/checkouts/addressing/{$cartId}", $params);

        // Use any valid status code here
        if (204 !== $response->getStatusCode()) {
            switch ($response->getStatusCode()) {
                case 400:
                    throw new DomainExceptions\ValidationException();

                    break;
                default:
                    $this->handleErrors($response);

                    break;
            }
        }

        return true;
    }
}
