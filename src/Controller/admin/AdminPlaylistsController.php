<?php
namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de la partie back office des playlists
 *
 * @author Charles Bosch-Crégut
 */
class AdminPlaylistsController extends AbstractController
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
    
    private const CHEMIN_PAGE_ADMIN_PLAYLIST = "/admin/admin.playlists.html.twig";
    private const CHEMIN_ROUTE_ADMIN_PLAYLIST = "admin.playlists";
    private const CHEMIN_PAGE_ADMIN_PLAYLIST_EDIT = "admin/admin.playlists.edit.html.twig";
    private const CHEMIN_PAGE_ADMIN_PLAYLIST_AJOUT = "admin/admin.playlists.ajout.html.twig";
    
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * @Route("/admin/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name', 'ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/playlists/suppr/{id}", name="admin.playlists.suppr")
     * @param Playlist playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response
    {
        $this->playlistRepository->remove($playlist, true);
        return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_PLAYLIST);
    }
    
    /**
     * @Route("/admin/playlists/edit/{id}", name="admin.playlists.edit")
     * @param Playlist playlist
     * @param Request $request
     * @return Response
     */
    public function edit(Playlist $playlist, Request $request): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_PLAYLIST);
        }

        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST_EDIT, [
            'playlist' => $playlist,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/playlist/ajout", name="admin.playlists.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_PLAYLIST);
        }

        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST_AJOUT, [
            'playlist' => $playlist,
            'form' => $form->createView()
        ]);
    }
    
    

    /**
     * @Route("admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    
    /**
     * @Route("admin/playlists/recherche/{champ}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur
        ]);
    }
    
     /**
     * @Route("admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontainCategorie")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainCategorie($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $allPlaylists = $this->playlistRepository->findAllOrderBy('name', 'ASC');
        $playlists = $this->playlistRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        for ($i = 0; $i <= count($allPlaylists) - 1; $i++) {
            for ($j = 0; $j <= count($playlists) - 1; $j++) {
                if ($allPlaylists[$i]['id'] == $playlists[$j]['id']) {
                    $playlists[$j]['nbformation'] = $allPlaylists[$i]['nbformation'];
                }
            }
        }

        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("admin/playlists/playlist/{id}", name="admin.playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render(self::CHEMIN_PAGE_ADMIN_PLAYLIST, [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
}

