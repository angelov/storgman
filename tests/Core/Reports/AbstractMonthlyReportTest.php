<?php

namespace Angelov\Storgman\Tests\Core\Reports;

use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Core\Reports\AbstractMonthlyReport;
use Angelov\Storgman\Tests\TestCase;
use Mockery;

class AbstractMonthlyReportTest extends TestCase
{
    /** @var AbstractMonthlyReport $report */
    protected $report;

    public function setUp()
    {
        parent::setUp();

        $from = DateTime::createFromFormat('Y-m-d', '2015-01-01');
        $to = DateTime::createFromFormat('Y-m-d', '2015-03-01');

        $this->report = $this->getMockForAbstractClass(AbstractMonthlyReport::class, [$from, $to]);
    }

    public function testHasMonthsArrayInitializedByDefault()
    {
        $combined = [
            '2015-01' => 0,
            '2015-02' => 0,
            '2015-03' => 0
        ];

        $this->assertEquals($combined, $this->report->getMonths());
    }

    public function testCanGenerateMonthTitles()
    {
        $months = ['Jan 2015', 'Feb 2015', 'Mar 2015'];

        $this->assertEquals($months, $this->report->getMonthsTitles());
    }

    public function testCanReturnTheValues()
    {
        $values = [0, 0 , 0];

        $this->assertEquals($values, $this->report->getMonthsValues());
    }
}
