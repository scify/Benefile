<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * The dates of the forms (dd-mm-yyyy, or dd/mm/yyyy) and of the database (yyyy-mm-dd).
 *
 * The methods keep the return values that the code expects: an empty string
 * when there is no valid date.
 */
class DatesHelper
{
    /**
     * A date of a form as a Carbon date, or '' when the text is not a valid date.
     *
     * An impossible date, such as 31-02-2020, is not a valid date.
     */
    public function makeDBFriendlyDate($date): Carbon|string
    {
        $text = str_replace('/', '-', trim((string) $date));
        if (! preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $text, $parts) || ! checkdate((int) $parts[2], (int) $parts[1], (int) $parts[3])) {
            return '';
        }

        return Carbon::createFromFormat('!j-n-Y', (int) $parts[1].'-'.(int) $parts[2].'-'.$parts[3]);
    }

    /**
     * A date for a database search (yyyy-mm-dd): the date part of a date and time.
     */
    public function makeDBSearchFriendlyDate($date): string
    {
        $text = $date instanceof DateTimeInterface ? $date->format('Y-m-d H:i:s') : (string) $date;

        return $text === '' ? '' : (string) strtok($text, ' ');
    }

    /**
     * A date of a form as "yyyy-mm-dd hh:mm:ss", or '' when it is not a valid date.
     */
    public function getDateStringFromSimpleString($date): string
    {
        $carbon = $this->makeDBFriendlyDate($date);

        return $carbon instanceof Carbon ? $carbon->toDateTimeString() : '';
    }

    /**
     * A date of the database as dd-mm-yyyy, for the screens and the exports. '' when there is no date.
     */
    public function getFinelyFormattedStringDateFromDBDate($date): string
    {
        if ($date instanceof DateTimeInterface) {
            return $date->format('d-m-Y');
        }
        $text = substr(trim((string) $date), 0, 10);
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $text, $parts) || $text === '0000-00-00') {
            return '';
        }

        return $parts[3].'-'.$parts[2].'-'.$parts[1];
    }

    /**
     * The days from the first date to the second date of a form: negative when the second is earlier.
     */
    public function getDifferenceInDays($date1, $date2): int
    {
        $from = $this->makeDBFriendlyDate($date1);
        $to = $this->makeDBFriendlyDate($date2);
        if (! $from instanceof Carbon || ! $to instanceof Carbon) {
            return 0;
        }

        return (int) $from->diffInDays($to, false);
    }

    /**
     * The current date and time, as "yyyy-mm-dd hh:mm:ss".
     */
    public function getCurrentTimeString(): string
    {
        return Carbon::now()->toDateTimeString();
    }
}
