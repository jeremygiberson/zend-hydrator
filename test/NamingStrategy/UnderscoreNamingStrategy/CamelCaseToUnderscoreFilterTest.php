<?php


namespace ZendTest\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use ReflectionClass;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy\CamelCaseToUnderscoreFilter;

/**
 * Tests for {@see CamelCaseToUnderscoreFilter}
 *
 * @covers Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy\CamelCaseToUnderscoreFilter
 */
class CamelCaseToUnderscoreFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider nonUnicodeProvider
     * @param string $string
     * @param string $expected
     */
    public function testFilterUnderscoresNonUnicodeStrings($string, $expected)
    {
        $filter   = new CamelCaseToUnderscoreFilter();

        $reflectionClass = new ReflectionClass($filter);
        $property = $reflectionClass->getProperty('pcreUnicodeSupport');
        $property->setAccessible(true);
        $property->setValue($filter, false);

        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals($expected, $filtered);
    }

    /**
     * @dataProvider unicodeProvider
     * @param string $string
     * @param string $expected
     */
    public function testFilterUnderscoresUnicodeStrings($string, $expected)
    {
        if (!extension_loaded('mbstring')) {
            $this->markTestSkipped('Extension mbstring not available');
        }

        $filter   = new CamelCaseToUnderscoreFilter();

        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals($expected, $filtered);
    }

    /**
     * @dataProvider unicodeProviderWithoutMbStrings
     * @param string $string
     * @param string $expected
     */
    public function testFilterUnderscoresUnicodeStringsWithoutMbStrings($string, $expected)
    {
        $filter   = new CamelCaseToUnderscoreFilter();

        $reflectionClass = new ReflectionClass($filter);
        $property = $reflectionClass->getProperty('mbStringSupport');
        $property->setAccessible(true);
        $property->setValue($filter, false);

        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals($expected, $filtered);
    }

    public function nonUnicodeProvider()
    {
        return [
            'upcased first letter' => [
                'Camel',
                'camel'
            ],
            'multiple words' => [
                'underscoresMe',
                'underscores_me'
            ],
            'alphanumeric' => [
                'one2Three',
                'one_2_three'
            ],
            'multiple uppercased letters and underscores' => [
                'TheseAre_SOME_CamelCASEDWords',
                'these_are_some_camel_cased_words'
            ],
            'alphanumeric multiple up cases' => [
                'one2THR23ree',
                'one_2_thr_23_ree'
            ],
        ];
    }

    public function unicodeProvider()
    {
        return [
            'upcased first letter' => [
                'Camel',
                'camel'
            ],
            'multiple words' => [
                'underscoresMe',
                'underscores_me'
            ],
            'alphanumeric' => [
                'one2Three',
                'one_2_three'
            ],
            'multiple uppercased letters and underscores' => [
                'TheseAre_SOME_CamelCASEDWords',
                'these_are_some_camel_cased_words'
            ],
            'alphanumeric multiple up cases' => [
                'one2THR23ree',
                'one_2_thr_23_ree'
            ],
            'unicode' => [
                'testŠuma',
                'test_šuma'
            ]
        ];
    }

    public function unicodeProviderWithoutMbStrings()
    {
        return [
            'upcased first letter' => [
                'Camel',
                'camel'
            ],
            'multiple words' => [
                'underscoresMe',
                'underscores_me'
            ],
            'alphanumeric' => [
                'one2Three',
                'one_2_three'
            ],
            'multiple uppercased letters and underscores' => [
                'TheseAre_SOME_CamelCASEDWords',
                'these_are_some_camel_cased_words'
            ],
            'alphanumeric multiple up cases' => [
                'one2THR23ree',
                'one_2_thr_23_ree'
            ],
            'unicode uppercase character' => [
                'testŠuma',
                'test_Šuma'
            ],
        ];
    }

    public function returnUnfilteredDataProvider()
    {
        return [
            ['underscore'],
            [null],
            [new \stdClass()]
        ];
    }

    /**
     * @dataProvider returnUnfilteredDataProvider
     * @return void
     */
    public function testReturnUnfiltered($input)
    {
        $filter = new CamelCaseToUnderscoreFilter();

        $this->assertEquals($input, $filter->filter($input));
    }
}
