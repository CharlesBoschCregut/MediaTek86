<?php
namespace App;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Description of FormationsTest
 *
 * @author PC-Charles
 */
class FormationsTest extends TestCase
{
    public function testGetPublishedAtString()
    {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2021-01-04 17:00:12"));
        $this->assertEquals("04/01/2021", $formation->getPublishedAtString());
    }
}
