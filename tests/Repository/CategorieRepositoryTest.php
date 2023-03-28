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
 * Description of CategorieRepositoryTest
 *
 * @author PC-Charles
 */
class CategorieRepositoryTest extends KernelTestCase
{
    /**
     * Retourne une instance du repository
     * @return CategorieRepository
     */
    public function getRepository(): CategorieRepository
    {
        self::bootKernel();
        return self::getContainer()->get(CategorieRepository::class);
    }
    
    /**
     * Enregiste une nouvelle catégorie
     * @return Categorie
     */
    public function newCategorie(): Categorie
    {
        return (new Categorie())
            ->setName("LES TESTS");
    }
    
    /**
     * Teste l'enregistrement de nouvelle catégorie
     */
    public function testAddCategorie()
    {
        $repository = $this->getRepository();
        $categorie = $this->newCategorie();
        $nbcategories = $repository->count([]);
        $repository->add($categorie, true);
        $this->assertEquals($nbcategories + 1, $repository->count([]), "Erreur lors de l'ajout");
    }
    
    /**
     * Teste la suppression d'une Catégorie
     */
    public function testSupprCategorie()
    {
        $repository = $this->getRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $nbcategories = $repository->count([]);
        $repository->remove($categorie, true);
        $this->assertEquals($nbcategories - 1, $repository->count([]), "Erreur lors de la suppression");
    }
    
    /**
     * Teste que la méthode findAllForOnePlaylist retourne les bonnes données
     */
    public function testFindAllForOnePlaylist()
    {
        $repository = $this->getRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $formationRepository = self::getContainer()->get(FormationRepository::class);
        $playlistRepository = self::getContainer()->get(PlaylistRepository::class);
        
        
        $playlist = new Playlist();
        $playlist->setName('TEST REPO');
        $playlistRepository->add($playlist, true);
        
        $formation = new Formation();
        $formation->setTitle("test de validation");
        $formation->addCategory($categorie);
        $playlist->addFormation($formation);
        $formationRepository->add($formation, true);
        $playlistRepository->add($playlist, true);
        
        $results = $repository->findAllForOnePlaylist($playlist->getId());
        $this->assertEquals(
            1,
            count($results),
            "Erreur lors de findAllForOnePlaylist ".count($results)." playlists ont été trouvé au lieu de 1"
        );
        $this->assertEquals(
            "LES TESTS",
            $results[0]->getName(),
            "Erreur lors de findAllForOnePlaylist la mauvaise catégorie a été trouvée"
        );
    }
}
