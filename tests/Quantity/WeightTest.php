<?php

/*
 * This file is part of the Phospr Quantity package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\Tests\Quantity;

use Phospr\Fraction;
use Phospr\Weight;
use Phospr\Uom;
use PHPUnit_Framework_TestCase;

/**
 * WeightTest
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.3.0
 */
class WeightTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __callStatic
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.3.0
     */
    public function testCallStatic()
    {
        $weight = Weight::LB(10, 20);

        $this->assertSame('LB', $weight->getUom()->getName());
        $this->assertSame('1/2', (string) $weight->getAmount());

        $newWeight = $weight->to(Uom::OZ());

        $this->assertSame('8', (string) $newWeight->getAmount());
    }

    /**
     * Test __toString()
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.6.0
     */
    public function testToString()
    {
        $weight = Weight::LB(1, 3);

        $this->assertSame('1/3 LB', (string) $weight);
    }

    /**
     * Test to
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  0.10.0
     *
     * @dataProvider toProvider
     */
    public function testTo($fromWeight, $fromUom, $toUom, $numerator, $denominator)
    {
        $oldWeight = new Weight(new Fraction((int) $fromWeight), new Uom($fromUom));

        $convertedWeight = $oldWeight->to(new Uom($toUom));
        $this->assertSame($numerator, $convertedWeight->getAmount()->getNumerator());
        $this->assertSame($denominator, $convertedWeight->getAmount()->getDenominator());
    }

    /**
     * Test to exception
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  1.2.0
     *
     * @dataProvider toExceptionProvider
     * @expectedException Phospr\Exception\Uom\ConversionNotSetException
     */
    public function testToException($weight, $uom)
    {
        $weight->to($uom);
    }

    /**
     * Test fromString
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.12.0
     *
     * @dataProvider fromStringProvider
     */
    public function testFromString($fromString, $toString)
    {
        $this->assertSame($toString, (string) Weight::fromString($fromString));
    }

    /**
     * Test fromString exception
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.12.0
     *
     * @dataProvider fromStringExceptionProvider
     * @expectedException InvalidArgumentException
     */
    public function testFromStringException($string)
    {
        Weight::fromString($string);
    }

    /**
     * Test add
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  1.2.0
     *
     * @dataProvider addProvider
     */
    public function testAdd($weight1, $weight2, $resultWeight)
    {
        $newWeight = $weight1->add($weight2);

        // Test the new quantities amount
        $this->assertTrue(
            $resultWeight->isSameValueAs($newWeight)
        );
    }

    /**
     * Test isSameValueAs
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since 1.2.0
     *
     * @dataProvider isSameValueAsProvider
     */
    public function testIsSameValueAs($weight1, $weight2, $result)
    {
        $this->assertSame($result, $weight1->isSameValueAs($weight2));
    }

    /**
     * toProvider
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  0.10.0
     *
     * @return array
     */
    public function toProvider()
    {
        return [
            // LBS
            [1, 'LB', 'KG', 50000, 110231],
            [1, 'LB', 'OZ', 16, 1],
            // Ounces
            [16, 'OZ', 'LB', 1, 1],
            [1, 'OZ', 'KG', 10000000, 352739619],
            [16, 'OZ', 'KG', 160000000, 352739619],
            // KiloGrams
            [1, 'KG', 'LB', 110231, 50000],
            [50, 'KG', 'LB', 110231, 1000],
            [1, 'KG', 'OZ', 352739619, 10000000],
            [1, 'G', 'KG', 1, 1000],
            [1, 'KG', 'G', 1000, 1],
        ];
    }

    /**
     * toExceptionProvider
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  1.2.0
     *
     * @return array
     */
    public function toExceptionProvider()
    {
        return [
            // LBS
            [Weight::LB(1), new Uom('G')],
            // Ounces
            [Weight::OZ(16), new Uom('G')],
            // KiloGrams
            // Grams
            [Weight::G(1), new Uom('LB')],
            [Weight::G(1), new Uom('OZ')],
        ];
    }

    /**
     * fromString provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.12.0
     *
     * @return array
     */
    public static function fromStringProvider()
    {
        return [
            ['1LB', '1 LB'],
            ['1LB ', '1 LB'],
            ['1 LB', '1 LB'],
            ['1G', '1 G'],
            ['1 G', '1 G'],
            ['1KG', '1 KG'],
            ['1 KG', '1 KG'],
            ['1OZ', '1 OZ'],
            ['1 OZ', '1 OZ'],
            [' 1LB', '1 LB'],
            [' 1LB ', '1 LB'],
            ['0.5 OZ', '1/2 OZ'],
            ['2/3 OZ', '2/3 OZ'],
            ['1. LB', '1 LB'],
            ['245/3 OZ', '81 2/3 OZ'],
        ];
    }

    /**
     * fromString exception provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.12.0
     *
     * @return array
     */
    public static function fromStringExceptionProvider()
    {
        return [
            ['1 EACH'],
            ['1 L'],
            ['1L'],
            ['1 Lb'],
            ['1 LB 0'],
            ['1 LBLB'],
            ['1 LB 4 OZ'],
            ['1LB0'],
            ['1LB1/2'],
            ['1LB/2LB'],
        ];
    }

    /**
     * add provider
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  1.2.0
     *
     * @return array
     */
    public static function addProvider()
    {
        return [
            [Weight::OZ(10), Weight::LB(2), Weight::OZ(42)],
            [Weight::KG(3), Weight::G(1), Weight::KG(3001, 1000)],
        ];
    }

    /**
     * isSameValueAs provider
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  1.2.0
     *
     * @return array
     */
    public static function isSameValueAsProvider()
    {
        return [
            [Weight::OZ(10), Weight::OZ(2), false],
            [Weight::OZ(3), Weight::OZ(3), true],
            [Weight::OZ(3), Weight::LB(3), false],
            [Weight::OZ(3), Weight::KG(3), false],
            [Weight::OZ(3), Weight::G(3), false],
            [Weight::OZ(16), Weight::LB(1), false],
            [Weight::LB(3), Weight::LB(3), true],
        ];
    }
}
