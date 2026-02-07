<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = "pending";
    case PROCESSING = "processing";
    case QUEUED = "queued";
    case FAILED = "failed";
    case SENT = "sent";
    case DONE = "done";

    /**
     * Retrieve campaign statuses
     * 
     * @return Status[]
     */
    public static function campaignStatuses(): array
    {
        return [
            self::QUEUED,
            self::PROCESSING,
            self::FAILED,
            self::DONE,
        ];
    }

    /**
     * Retrieve email job statuses
     * 
     * @return Status[]
     */
    public static function emailJobStatuses(): array
    {
        return [
            self::PENDING,
            self::SENT,
            self::FAILED,
        ];
    }
}