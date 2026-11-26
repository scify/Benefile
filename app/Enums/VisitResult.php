<?php

namespace App\Enums;

/**
 * The result of the last save of a medical visit, shown above the medical history.
 */
enum VisitResult: int
{
    case None = 0;
    case Saved = 1;
    case Failed = 2;
    case Updated = 3;

    /**
     * The result that the last request flashed to the session.
     */
    public static function fromSession(): self
    {
        return self::tryFrom((int) session('visit_result', 0)) ?? self::None;
    }
}
