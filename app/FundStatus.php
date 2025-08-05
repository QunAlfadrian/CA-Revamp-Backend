<?php

namespace App;

enum FundStatus: string {
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public static function mapMidtransStatus(string $status): self {
        return match ($status) {
            'settlement', 'capture' => self::Paid,
            'deny', 'cancel', 'expire', 'failure' => self::Failed,
            'refund', 'partial_refund' => self::Refunded,
            default => self::Pending,
        };
    }
}
