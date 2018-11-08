<?php

namespace Punic\Test\Data;

use Punic\Calendar;
use Punic\Data;
use Punic\Exception;
use Punic\Territory;
use Punic\Test\TestCase;
use Punic\Unit;
use stdClass;

class DataTest extends TestCase
{
    protected function tearDown()
    {
        Data::setOverrides(array());
        Data::setOverridesGeneric(array());
    }

    /**
     * @return array
     */
    public function provideInvalidLocales()
    {
        return array(
            array('setFallbackLocale', array(''), 'Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(null), 'Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(true), 'Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(false), 'Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(new stdClass()), 'Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array('invalid'), 'Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(''), 'Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(null), 'Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(true), 'Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(false), 'Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(new stdClass()), 'Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array('invalid'), 'Punic\\Exception\\InvalidLocale'),
        );
    }

    /**
     * @dataProvider provideInvalidLocales
     *
     * @param string $method
     * @param array $parameters
     * @param string $exception
     */
    public function testInvalidLocales($method, $parameters, $exception)
    {
        $this->setExpectedException($exception);
        call_user_func_array(array('Punic\\Data', $method), $parameters);
    }

    public function testInvalidLocaleGet()
    {
        try {
            Data::setFallbackLocale('invalid');
        } catch (Exception\InvalidLocale $ex) {
            $this->assertSame('invalid', $ex->getLocale());
        }
    }

    public function testDefaultLocale()
    {
        Data::setDefaultLocale('it');
        $this->assertSame('it', Data::getDefaultLocale());
    }

    public function testDefaultLanguage()
    {
        Data::setDefaultLocale('de_DE');
        $this->assertSame('de', Data::getDefaultLanguage());
    }

    public function testFallbackLocale()
    {
        Data::setFallbackLocale('it');
        $this->assertSame('it', Data::getFallbackLocale());
    }

    public function testFallbackLanguage()
    {
        Data::setFallbackLocale('de_DE');
        $this->assertSame('de', Data::getFallbackLanguage());
    }

    /**
     * @return array
     */
    public function provideInvalidDataFile()
    {
        return array(
            array('Punic\Exception\InvalidDataFile', 'get', true),
            array('Punic\Exception\InvalidDataFile', 'get', array()),
            array('Punic\Exception\InvalidDataFile', 'get', ''),
            array('Punic\Exception\InvalidDataFile', 'get', '../x'),
            array('Punic\Exception\InvalidDataFile', 'get', '*'),
            array('Punic\Exception\DataFileNotFound', 'get', 'invalid-data-file'),
            array('Punic\Exception\InvalidDataFile', 'getGeneric', true),
            array('Punic\Exception\InvalidDataFile', 'getGeneric', array()),
            array('Punic\Exception\InvalidDataFile', 'getGeneric', ''),
            array('Punic\Exception\InvalidDataFile', 'getGeneric', '../x'),
            array('Punic\Exception\InvalidDataFile', 'getGeneric', '*'),
            array('Punic\Exception\DataFileNotFound', 'getGeneric', 'invalid-data-file'),
        );
    }

    /**
     * @dataProvider provideInvalidDataFile
     *
     * @param string $exception
     * @param string $method
     * @param string|mixed $dataFileID
     */
    public function testInvalidDataFile($exception, $method, $dataFileID)
    {
        $this->setExpectedException($exception);
        call_user_func(array('Punic\\Data', $method), $dataFileID);
    }

    public function testGetAvailableLocales()
    {
        $locales = Data::getAvailableLocales();
        // this list isn't static, we assume that something between 1 and 320 locales is okay
        $this->assertLessThan(4000, count($locales));
        $this->assertGreaterThan(1, count($locales));
    }

    /**
     * @return array
     */
    public function provideGuessFullLocale()
    {
        return array(
            array('en-Latn-US', array('en')),
            array('it-Latn-IT', array('it')),
            array('de-Latn-DE', array('de')),
            array('de-Cyrl-DE', array('de', 'Cyrl')),
            array('az-Cyrl-AZ', array('az', 'Cyrl')),
        );
    }

    /**
     * @dataProvider provideGuessFullLocale
     *
     * @param string $result
     * @param array $parameters
     */
    public function testGuessFullLocale($result, $parameters)
    {
        $locale = call_user_func_array(array('Punic\Data', 'guessFullLocale'), $parameters);
        $this->assertSame($result, $locale);
    }

    public function testGuessFullDefaultLocale()
    {
        Data::setDefaultLocale('de_DE');
        $locale = Data::guessFullLocale();
        $this->assertSame('de-Latn-DE', $locale);
    }

    /**
     * @return array
     */
    public function provideGetTerritory()
    {
        return array(
            array('US', array('en_US')),
            array('IT', array('it_IT')),
            array('DE', array('de_DE')),
            array('CH', array('de_CH')),
            array('AZ', array('az_Latn_AZ')),
            array('AZ', array('az')),
        );
    }

    /**
     * @dataProvider provideGetTerritory
     *
     * @param string $result
     * @param array $parameters
     */
    public function testGetTerritory($result, $parameters)
    {
        $locale = call_user_func_array(array('Punic\Data', 'getTerritory'), $parameters);
        $this->assertSame($result, $locale);
    }

    public function testOverrides()
    {
        Data::setOverrides(array(
            'calendar' => array(
                'days' => array(
                    'format' => array(
                        'wide' => array(
                            'mon' => 'Moonday',
                        ),
                        'enormous' => array(
                            'mon' => 'Moooonday',
                        ),
                    ),
                ),
            ),
        ), 'en');
        $this->assertSame('Moonday', Calendar::getWeekdayName(1, 'wide', 'en'));
        $this->assertSame('Moooonday', Calendar::getWeekdayName(1, 'enormous', 'en'));
        $this->assertSame('Monday', Calendar::getWeekdayName(1, 'wide', 'en-GB'));

        Data::setOverrides(array(), 'en');
        $this->assertSame('Monday', Calendar::getWeekdayName(1, 'wide', 'en'));

        $overrides = array(
            'en' => array(
                'territories' => array(
                    '001' => 'Whole world',
                ),
            ),
            'de' => array(
                'territories' => array(
                    '001' => 'Ganze Welt',
                ),
            ),
        );
        Data::setOverrides($overrides);
        $this->assertSame($overrides, Data::getOverrides());
        $this->assertSame($overrides['en'], Data::getOverrides('en'));
        $this->assertSame(array(), Data::getOverrides('it'));
        $this->assertSame('Whole world', Territory::getName('001', 'en'));
        $this->assertSame('Ganze Welt', Territory::getName('001', 'de'));

        $overrides = array(
            'measurementData' => array(
                'measurementSystem' => array(
                    'GB' => 'metric',
                    'IT' => 'US',
                ),
            ),
        );
        Data::setOverridesGeneric($overrides);
        $this->assertSame($overrides, Data::getOverridesGeneric());
        $this->assertSame('metric', Unit::getMeasurementSystemFor('GB'));
        $this->assertSame('US', Unit::getMeasurementSystemFor('IT'));
    }

    public function provideInvalidOverrides()
    {
        return array(
            array(
                array(
                    'calendar' => array(
                        'days' => array(
                            'format' => array(
                                'wide' => array(
                                    'mon' => 1,
                                ),
                            ),
                        ),
                    ),
                ),
                'Cannot override string value Monday with integer value 1',
            ),
            array(
                array(
                    'calendar' => array(
                        'days' => array(
                            'format' => array(
                                'wide' => array(
                                    'mon' => array(1, 2, 3),
                                ),
                            ),
                        ),
                    ),
                ),
                'Cannot override string value Monday with array with keys 0, 1, 2',
            ),
            array(
                array(
                    'calendar' => array(
                        'days' => 'foo',
                    ),
                ),
                'Cannot override array with keys format, stand-alone with string value foo',
            ),
        );
    }

    /**
     * @dataProvider provideInvalidOverrides
     *
     * @param array $overrides
     * @param string $message
     */
    public function testInvalidOverrides(array $overrides, $message)
    {
        Data::setOverrides($overrides, 'en');

        $this->setExpectedException('Punic\Exception\InvalidOverride', $message);
        Data::get('calendar', 'en');
    }
}
