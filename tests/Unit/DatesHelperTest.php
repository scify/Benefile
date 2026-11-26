<?php

namespace Tests\Unit;

use App\Services\DatesHelper;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DatesHelperTest extends TestCase
{
    private DatesHelper $dates;

    protected function setUp(): void
    {
        $this->dates = new DatesHelper();
    }

    public function test_a_form_date_becomes_a_carbon_date(): void
    {
        $this->assertSame('1990-02-01', $this->dates->makeDBFriendlyDate('01-02-1990')->toDateString());
        $this->assertSame('1990-02-01', $this->dates->makeDBFriendlyDate('1/2/1990')->toDateString());
    }

    public function test_an_invalid_form_date_is_empty(): void
    {
        foreach (['', null, '31-02-2020', 'dd-mm-yyyy', '2020-02-01', '01-02-90'] as $text) {
            $this->assertSame('', $this->dates->makeDBFriendlyDate($text), var_export($text, true));
        }
    }

    public function test_a_database_date_is_shown_as_day_month_year(): void
    {
        $this->assertSame('20-11-2026', $this->dates->getFinelyFormattedStringDateFromDBDate('2026-11-20'));
        $this->assertSame('20-11-2026', $this->dates->getFinelyFormattedStringDateFromDBDate('2026-11-20 14:30:00'));
        $this->assertSame('20-11-2026', $this->dates->getFinelyFormattedStringDateFromDBDate(Carbon::create(2026, 11, 20)));
        $this->assertSame('', $this->dates->getFinelyFormattedStringDateFromDBDate('0000-00-00'));
        $this->assertSame('', $this->dates->getFinelyFormattedStringDateFromDBDate(null));
    }

    public function test_the_difference_in_days_has_a_sign(): void
    {
        $this->assertSame(9, $this->dates->getDifferenceInDays('01-01-2026', '10-01-2026'));
        $this->assertSame(-9, $this->dates->getDifferenceInDays('10-01-2026', '01-01-2026'));
    }

    public function test_a_search_date_is_the_date_part(): void
    {
        $this->assertSame('2026-11-20', $this->dates->makeDBSearchFriendlyDate('2026-11-20 00:00:00'));
        $this->assertSame('2026-11-20', $this->dates->makeDBSearchFriendlyDate(Carbon::create(2026, 11, 20)));
        $this->assertSame('', $this->dates->makeDBSearchFriendlyDate(''));
    }
}
