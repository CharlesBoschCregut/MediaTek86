<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of PlaylistRepositoryTest
 *
 * @author PC-Charles
 */
class PlaylistRepositoryTest extends KernelTestCase
{
    public function getRepository(): PlaylistRepository
    {
        self::bootKernel();
        return self::getContainer()->get(PlaylistRepository::class);
    }
    
    public function newPlaylist(): Playlist
    {
        return (new Playlist())
            ->setName("TEST REPO");
    }
    
    public function testAddPlaylist()
    {
        $repository = $this->getRepository();
        $playlist = $this->newplaylist();
        $nbplaylists = $repository->count([]);
        $repository->add($playlist, true);
        $this->assertEquals($nbplaylists + 1, $repository->count([]), "Erreur lors de l'ajout");
    }
    
    public function testSupprPlaylist()
    {
        $repository = $this->getRepository();
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        $nbplaylists = $repository->count([]);
        $repository->remove($playlist, true);
        $this->assertEquals($nbplaylists - 1, $repository->count([]), "Erreur lors de la suppression");
    }
    
    public function testFindAllOrderBy()
    {
        $repository = $this->getRepository();
        $playlist = $this->newPlaylist();
        $formationRepository = self::getContainer()->get(FormationRepository::class);
        
        $playlist->setName('AAAA');
        $repository->add($playlist, true);
        $results = $repository->findAllOrderBy('name', 'ASC');
        $this->assertEquals($results[0]['name'], 'AAAA', "Erreur lors de findAllOrderBy('name',ASC)");
        
        $playlist->setName('ZZZZ');
        $repository->add($playlist, true);
        $results = $repository->findAllOrderBy('name', 'DESC');
        $this->assertEquals($results[0]['name'], 'ZZZZ', "Erreur lors de findAllOrderBy('name',DESC)");
        
        $results = $repository->findAllOrderBy('nbformation', 'ASC');
        $this->assertEquals(count($results[0]['nbformation']), 0, "Erreur lors de findAllOrderBy('nbformation', ASC)");
        
        $attendus = array();
        $nbformations = count($formationRepository->findall()) + 1;
        for ($i = 0; $i < $nbformations; $i++) {
            $formation = new Formation();
            $formation->setTitle((string)$i);
            $playlist->addFormation($formation);
            $formationRepository->add($formation, true);
            $attendus[$i] = $formation->getId();
        }
        $repository->add($playlist, true);
        $results = $repository->findAllOrderBy('nbformation', 'DESC');
        $this->assertEquals(
            count($results[0]['nbformation']),
            $nbformations,
            "Erreur lors de findAllOrderBy('nbformation', DESC)"
        );
    }
    
    public function testFindByContainValue()
    {
        $repository = $this->getRepository();
        $playlist = $this->newPlaylist();
        
        $repository->add($playlist, true);
        $results = $repository->findByContainValue('name', $playlist->getName());
        $this->assertEquals(
            $results[0]['name'],
            $playlist->getName(),
            "Erreur lors de findByContainValue"
        );
    }
    
    public function testFindByContainValueInTable()
    {
        $repository = $this->getRepository();
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        $categorieRespository = self::getContainer()->get(CategorieRepository::class);
        $formationRepository = self::getContainer()->get(FormationRepository::class);
        
        $categorie = new Categorie();
        $categorie->setName('LES TESTS');
        $categorieRespository->add($categorie, true);
        
        $formation = new Formation();
        $formation->setTitle("Test de validation");
        $formation->addCategory($categorie);
        $playlist->addFormation($formation);
        $formationRepository->add($formation, true);
        
        $repository->add($playlist, true);
        $results = $repository->findByContainValueInTable('name', 'LES TESTS', 'categorie');
        $this->assertEquals(
            $results[0]['name'],
            $playlist->getName(),
            "Erreur lors de testFindByContainValueInTable (1er élement incorrect"
        );
    }
}
