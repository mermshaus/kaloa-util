<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util;

use Kaloa\Util\ArrayObject;
use PHPUnit_Framework_TestCase;

/**
 *
 */
class ArrayObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testCanInstantiate()
    {
        new ArrayObject(array(1, 2, 3));
    }

    /**
     *
     * @return array
     */
    private function getDemoItems()
    {
        $items = array(
            array('year' => 2009, 'month' =>  9, 'title' => 'Hello World!'),
            array('year' => 2009, 'month' =>  9, 'title' => 'At the museum'),
            array('year' => 2009, 'month' =>  9, 'title' => 'Godspeed'),
            array('year' => 2009, 'month' =>  9, 'title' => '2010 Olympics'),
            array('year' => 2010, 'month' =>  1, 'title' => 'Tornado season'),
            array('year' => 2010, 'month' =>  1, 'title' => 'Bailout'),
            array('year' => 2010, 'month' =>  2, 'title' => 'Cheers, Ladies!'),
            array('year' => 2010, 'month' =>  2, 'title' => 'Neglected'),
            array('year' => 2009, 'month' => 11, 'title' => 'Ethics probe'),
            array('year' => 2010, 'month' =>  3, 'title' => 'Commitment to security'),
            array('year' => 2010, 'month' =>  3, 'title' => 'Election'),
            array('year' => 2009, 'month' => 10, 'title' => 'Same-sex couples'),
            array('year' => 2009, 'month' => 10, 'title' => 'Junkyard'),
        );

        return $items;
    }

    /**
     *
     */
    public function testBasicGrouping()
    {
        $obj = new ArrayObject($this->getDemoItems());

        $obj->groupBy(function ($item) {
            return array($item['year'], $item['month']);
        });

        $expected = <<<EOT
2009-9: Hello World!, At the museum, Godspeed, 2010 Olympics
2009-11: Ethics probe
2009-10: Same-sex couples, Junkyard
2010-1: Tornado season, Bailout
2010-2: Cheers, Ladies!, Neglected
2010-3: Commitment to security, Election
EOT;

        $this->assertEquals($expected, $this->flattenGroupedData($obj));
    }

    /**
     *
     */
    public function testAdvancedGrouping()
    {
        $obj = new ArrayObject($this->getDemoItems());

        $obj->groupBy(function ($item) {
            $ret = array($item['year'], $item['month']);

            unset($item['year']);
            unset($item['month']);
            $item['title'] = strtoupper($item["title"]);

            return $ret;
        });

        $expected = <<<EOT
2009-9: HELLO WORLD!, AT THE MUSEUM, GODSPEED, 2010 OLYMPICS
2009-11: ETHICS PROBE
2009-10: SAME-SEX COUPLES, JUNKYARD
2010-1: TORNADO SEASON, BAILOUT
2010-2: CHEERS, LADIES!, NEGLECTED
2010-3: COMMITMENT TO SECURITY, ELECTION
EOT;

        $this->assertEquals($expected, $this->flattenGroupedData($obj));
    }

    /**
     *
     */
    public function testGroupingWithScalarValues()
    {
        $items = array('Carl', 'Susan', 'Cindy', 'Peter', 'Steve', 'Patricia', 'Sam');

        $obj = new ArrayObject($items);

        $obj->groupBy(function (&$item) {
            $item = strtoupper($item);
            return substr($item, 0, 1);
        });

        $ret = '';

        foreach ($obj as $letter => $entries) {
            $ret .= $letter . ': ';
            $ret .= implode(', ', $entries->getArrayCopyRec());
            $ret .= "\n";
        }
        $ret = rtrim($ret);

        $this->assertEquals(<<<EOT
C: CARL, CINDY
S: SUSAN, STEVE, SAM
P: PETER, PATRICIA
EOT
, $ret);
    }

    /**
     *
     */
    public function testSorting()
    {
        $obj = new ArrayObject($this->getDemoItems());

        $obj->groupBy(
            function ($item) {      // Group by year and month
                return array($item['year'], $item['month']);
            }
        )->uksortm(
            array(
                function ($a, $b) { return ($a < $b) ?  1 : -1; },    // Order first dimension descending
                function ($a, $b) { return ($a < $b) ? -1 :  1; }     // Order second dimension ascending
            )
        )->usortm(
            array(
                null,    // Skip first and second dimensions, only realign third
                null,    //  (descending by length of an entry's title)
                function ($a, $b) {
                    $sa = strlen($a['title']);
                    $sb = strlen($b['title']);

                    if ($sa !== $sb) {
                        return $sb - $sa;
                    }

                    // Tie-breaker
                    return strcmp($a['title'], $b['title']);
                }
            )
        );

        $expected = <<<EOT
2010-1: Tornado season, Bailout
2010-2: Cheers, Ladies!, Neglected
2010-3: Commitment to security, Election
2009-9: 2010 Olympics, At the museum, Hello World!, Godspeed
2009-10: Same-sex couples, Junkyard
2009-11: Ethics probe
EOT;

        $this->assertEquals($expected, $this->flattenGroupedData($obj));
    }

    /**
     *
     * @param ArrayObject $obj
     * @return string
     */
    private function flattenGroupedData(ArrayObject $obj)
    {
        $ret = '';

        foreach ($obj as $year => $yearContent) {
            foreach ($yearContent as $month => $monthContent) {
                $ret .= $year . '-' . $month . ': ';
                foreach ($monthContent as $entryContent) {
                    $ret .= $entryContent['title'] . ', ';
                }
                $ret = rtrim($ret, ', ');
                $ret .= "\n";
            }
        }

        $ret = rtrim($ret);

        return $ret;
    }
}
