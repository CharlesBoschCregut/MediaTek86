<?php
namespace App\tests\Repository;

use DateTime;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of FormationRepositoryTest
 *
 * @author PC-Charles
 */
class FormationRepositoryTest extends KernelTestCase
{
    /**
     * Retourne une instance du repository
     * @return FormationRepository
     */
    public function getRepository(): FormationRepository
    {
        self::bootKernel();
        return self::getContainer()->get(FormationRepository::class);
    }
    
    /**
     * Enregistre une nouvelle formation
     * @return Formation
     */
    public function newFormation(): Formation
    {
        return (new Formation())
            ->setTitle("Test de validation")
            ->setVideoId('-testsV');
    }
    
    /**
     * Enregistre une nouvelle playlist
     * @return Playlist
     */
    public function newPlaylist(): Playlist
    {
        return (new Playlist())
            ->setName("TEST REPO");
    }
    
    /**
     * Teste l'enregistrement de foramtion
     */
    public function testAddFormation()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $nbFormations = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "Erreur lors de l'ajout");
    }
    
    /**
     * Teste la suppression d'une formation
     */
    public function testSupprFormation()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $nbFormations = $repository->count([]);
        $repository->remove($formation, true);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "Erreur lors de la suppression");
    }
    
    /**
     * Teste que la méthode findAllOrderBy retourne les bonnes formations triées en fonction des paramètres
     */
    public function testFindAllOrderBy()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        
        $formation->setTitle("AAAAAAA");
        $repository->add($formation, true);
        $results = $repository->findAllOrderBy('title', 'ASC');
        $this->assertEquals(
            $results[0]->getTitle(),
            $formation->getTitle(),
            "Erreur lors de findAllOrderBy(title,ASC)"
        );
        
        $formation->setTitle("ZZZZZZZZZ");
        $repository->add($formation, true);
        $results = $repository->findAllOrderBy('title', 'DESC');
        $this->assertEquals(
            $results[0]->getTitle(),
            $formation->getTitle(),
            "Erreur lors de findAllOrderBy(title,DESC)"
        );
    }
    
    /**
     *Teste que la méthode findAllOrderByInTable retourne les bonnes données triées en fonction des paramètres
     */
    public function testFindAllOrderByInTable()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $playlistRespository = self::getContainer()->get(PlaylistRepository::class);
        
        $playlist = $playlistRespository->find(13);
        $formation->setPlaylist($playlist);
        $repository->add($formation, true);
        $results = $repository->findAllOrderByInTable('name', 'ASC', 'playlist');
        $this->assertEquals($results[0]->getPlaylist(), $playlist, "Erreur lors de findAllOrderBy");
        
        $playlist = $playlistRespository->find(2);
        $formation->setPlaylist($playlist);
        $repository->add($formation, true);
        $results = $repository->findAllOrderByInTable('name', 'DESC', 'playlist');
        $this->assertEquals($results[0]->getPlaylist(), $playlist, "Erreur lors de findAllOrderBy");
    }
    
    /**
     * Teste que la méthode findByContainValue retourne les formations données
     */
    public function testFindByContainValue()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $results = $repository->findByContainValue('title', $formation->getTitle());
        $this->assertEquals($results[0]->getTitle(), $formation->getTitle(), "Erreur lors de findByContainValue");
    }
    
    /**
     *Teste que la méthode findByContainValueInTable retourne les bonnes playlists
     */
    public function testFindByContainValueInTable()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $playlistRespository = self::getContainer()->get(PlaylistRepository::class);
        $playlist = $this->newPlaylist();
        $playlistRespository->add($playlist, true);
        $formation->setPlaylist($playlist);
        $repository->add($formation, true);
        
        $results = $repository->findByContainValueInTable('name', 'TEST REPO', 'playlist');
        $this->assertCount(
            1,
            $results,
            "Erreur lors de testFindByContainValueInTable nombre de formations trouvées incorrecte"
        );
        $this->assertEquals(
            $results[0]->getTitle(),
            $formation->getTitle(),
            "Erreur lors de testFindByContainValueInTable (1er élement incorrect"
        );
        
    }
    
    /**
     * Teste que la méthode findAllLasted retourne le bon nombre de formations
     */
    public function testFindAllLasted()
    {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $formation->setPublishedAt(new Datetime('2023-03-24 17:20:12'));
        $repository->add($formation, true);
        
        $results = $repository->findAllLasted(2);
        $this->assertCount(
            2,
            $results,
            "Erreur lors de testFindAllLasted nombre de formations trouvées incorrecte"
        );
        $this->assertEquals(
            $results[0]->getPublishedAtString(),
            "24/03/2023",
            "Erreur lors de testFindAllLasted, la formation trouvée n'est pas la dernière en date"
        );
    }
    
    /**
     * Teste que la méthode findAllForOnePlaylist retourne les bonnes formations
     */
    public function testFindAllForOnePlaylist()
    {
        $repository = $this->getRepository();
        $playlistRespository = self::getContainer()->get(PlaylistRepository::class);
        $playlist = $this->newPlaylist();
        $playlistRespository->add($playlist, true);
        $id = $playlist->getId();
        $formations = array(
            $this->newFormation()->setPlaylist($playlist),
            $this->newFormation()->setTitle("Autre Formation")->setPlaylist($playlist));
        $repository->add($formations[0], true);
        $repository->add($formations[1], true);
        
        $results = $repository->findAllForOnePlaylist($id);
        $this->assertCount(
            2,
            $results,
            "Erreur lors de testFindAllForOnePlaylist nombre de formations trouvées incorrecte"
        );
        $this->assertEquals(
            $results[0]->getTitle(),
            $formations[0]->getTitle(),
            "Erreur lors de testFindAllForOnePlaylist la formation trovuée n'est pas la bonne"
        );
        $this->assertEquals(
            $results[1]->getTitle(),
            $formations[1]->getTitle(),
            "Erreur lors de testFindAllForOnePlaylist la deuxieme formation trovuée n'est pas la bonne"
        );
    }
    
}