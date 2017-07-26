<?php

use Punic\Calendar;

class CalendarBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchEraName()
    {
        Calendar::getEraName(1);
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchMonthName()
    {
        $dt = Calendar::toDateTime('2010-03-07');
        Calendar::getMonthName($dt, 'wide', 'it');
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchWeekdayName()
    {
        Calendar::getWeekdayName(1);
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchQuarterName()
    {
        Calendar::getQuarterName(1);
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchFormat()
    {
        $dt = Calendar::toDateTime('2010-03-07');
        Calendar::formatDate($dt, 'full', 'it');
    }
}
