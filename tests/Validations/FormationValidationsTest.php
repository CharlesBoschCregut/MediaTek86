<?php
namespace App;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationValidationsTest
 *
 * @author PC-Charles
 */
class FormationValidationsTest extends KernelTestCase
{
    public function getFormation(): Formation
    {
        return (new Formation())
            ->setTitle("Test de validation")
            ->setVideoId('-testsV');
    }
    
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $msg="")
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $msg);
    }
    
    public function testValidDateFormation()
    {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("2021-03-24 17:00:12"));
        $this->assertErrors($formation, 0, "La date testée est 2021-03-24 et ne devrait pas échouer");
    }
    
    public function testNonValidDateFormation()
    {
        //Date postérieur a aujourd'hui (24/03/2023)
        $formation = $this->getFormation()->setPublishedAt(new DateTime("3021-03-24 17:00:12"));
        $this->assertErrors($formation, 1, "La date testée est 3021-03-24 et devrait échouer");
    }
}
