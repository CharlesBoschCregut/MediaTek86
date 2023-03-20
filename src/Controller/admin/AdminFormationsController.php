<?php
namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Controleur de la partie back office des formations
 *
 * @author Charles Bosch-Crégut
 */
class AdminFormationsController extends AbstractController{
    
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
    
    
    private const CHEMIN_PAGE_ADMIN_FORMATION = "/admin/admin.formations.html.twig";
    private const CHEMIN_ROUTE_ADMIN_FORMATION = "admin.formations";
    private const CHEMIN_PAGE_ADMIN_FORMATION_EDIT = "admin/admin.formations.edit.html.twig";
    private const CHEMIN_PAGE_ADMIN_FORMATION_AJOUT = "admin/admin.formations.ajout.html.twig";
    
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/admin/formations", name="admin.formations")
     * @return Response
     */
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/formations/suppr/{id}", name="admin.formations.suppr")
     * @param Formation formation
     * @return Response
     */
    public function suppr(Formation $formation): Response{
        $this->formationRepository->remove($formation, true);
        return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_FORMATION);
    }
    
    /**
     * @Route("/admin/formations/edit/{id}", name="admin.formations.edit")
     * @param Formation $visite
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation, Request $request): Response{
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_FORMATION);
        }

        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION_EDIT, [
            'formation' => $formation,
            'form' => $form->createView()
        ]);        
    }
    
    /**
     * @Route("/admin/formation/ajout", name="admin.formations.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_FORMATION);
        }     

        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION_AJOUT, [
            'formation' => $formation,
            'form' => $form->createView()
        ]);      
    } 
    
    /**
     * @Route("admin/formations/tri/{champ}/{ordre}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sortByTable")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortByTable($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderByInTable($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    
    /**
     * @Route("admin/formations/recherche/{champ}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);  
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur
        ]);
    }
    
    /**
     * @Route("admin/formations/recherche/{champ}/{table}", name="admin.formations.findAllContainJoin")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainJoin($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("admin/formations/formation/{id}", name="admin.formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render(self::CHEMIN_PAGE_ADMIN_FORMATION, [
            'formation' => $formation
        ]);
    }
}
