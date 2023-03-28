<?php
namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
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
class AdminCategoriesController extends AbstractController
{
    
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
    
    
    private const CHEMIN_PAGE_ADMIN_CATEGORIE = "/admin/admin.categories.html.twig";
    private const CHEMIN_ROUTE_ADMIN_CATEGORIE = "admin.categories";
    
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/admin/categories", name="admin.categories")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            
            $this->categorieRepository->add($categorie, true);
            return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_CATEGORIE);
        }
        
        return $this->render(self::CHEMIN_PAGE_ADMIN_CATEGORIE, [
            'formations' => $formations,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/categories/suppr/{id}", name="admin.categories.suppr")
     * @param Categorie categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response
    {
        $this->categorieRepository->remove($categorie, true);
        return $this->redirectToRoute(self::CHEMIN_ROUTE_ADMIN_CATEGORIE);
    }
}
