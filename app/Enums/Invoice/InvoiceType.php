<?php

namespace App\Enums\Invoice;

enum InvoiceType: string
{
    case NEW_SUBSCRIPTION = 'new_subscription';
    case EXTEND_SUBSCRIPTION = 'extend_subscription';
    case UPDATE_SUBSCRIPTION = 'update_subscription';
    case SWITCH_SUBSCRIPTION = 'switch_subscription';
    case PRACTICUM = 'practicum';

    public function isNewSubscription(): bool
    {
        return $this === self::NEW_SUBSCRIPTION;
    }

    public function isSubscriptionUpdate(): bool
    {
        return $this === self::UPDATE_SUBSCRIPTION;
    }

    public function isSubscriptionExtend(): bool
    {
        return $this === self::EXTEND_SUBSCRIPTION;
    }
}