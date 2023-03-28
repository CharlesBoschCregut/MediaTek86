<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{
    
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    private const CHEMIN_PAGE_PLAYLIST = "pages/playlists.html.twig";
    
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name', 'ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    public function getNbFormations($playlists)
    {
        $allPlaylists = $this->playlistRepository->findAllOrderBy('name', 'ASC');
        for ($i = 0; $i <= count($allPlaylists) - 1; $i++) {
            for ($j = 0; $j <= count($playlists) - 1; $j++) {
                if ($allPlaylists[$i]['id'] == $playlists[$j]['id']) {
                    $playlists[$j]['nbformation'] = $allPlaylists[$i]['nbformation'];
                }
            }
        }
        
        return $playlists;
    }

    /**
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    
    /**
     * @Route("/playlists/recherche/{champ}", name="playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->getNbFormations($playlists);
        return $this->render(self::CHEMIN_PAGE_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur
        ]);
    }
    
     /**
     * @Route("/playlists/recherche/{champ}/{table}", name="playlists.findallcontainCategorie")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainCategorie($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->getNbFormations($playlists);

        return $this->render(self::CHEMIN_PAGE_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/playlists/playlist/{id}", name="playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render('/pages/playlist.html.twig', [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
}
