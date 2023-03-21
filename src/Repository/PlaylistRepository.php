<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;


/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    
    private const SELECT_PARAM = 'p.id id';
    private const ADDSELECT_NAME = 'p.name name';
    private const ADDSELECT_CATEGORIE = 'c.name categoriename';
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Retourne toutes les playlists triées sur un champ
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderBy($champ, $ordre): array
    {
        //Récupération des données
        $data = $this->createQueryBuilder('p')
                ->select(self::SELECT_PARAM)
                ->addSelect(self::ADDSELECT_NAME)
                ->addSelect(self::ADDSELECT_CATEGORIE)
                ->addSelect("JSON_ARRAYAGG(f.id) nbformation")
                ->leftjoin('p.formations', 'f')
                ->leftjoin('f.categories', 'c')
                ->groupBy('p.id')
                ->addGroupBy('c.name')
                ->addOrderBy('c.name')
                ->getQuery()
                ->getResult();
        
        //Construit la string categoriename
        $results = array();
        for($i = 0; $i <= count($data) - 1; $i++){
            for($j = 0; $j <= count($data) - 1; $j++){
                if($data[$i]['name'] == $data[$j]['name'] && $i != $j){
                    $data[$i]['categoriename'] = $data[$i]['categoriename'].' '.$data[$j]['categoriename'];
                    $data[$j]['name'] = $j;
                }
            }
            if(!is_int($data[$i]['name'])){
                array_push($results, $data[$i]);
            }
        }
        
        //Construit l'array nbformation
        for($i = 0; $i <= count($results) - 1; $i++){
            $results[$i]['nbformation'] = explode(',', $results[$i]['nbformation']);
            if ($results[$i]['nbformation'][0] == "[null]"){
                $results[$i]['nbformation'] = "";
            } 
        }

        
        //tri
        switch([$champ,$ordre]){
            case ['name', 'ASC']:
                //tri par name ASC
                usort($results, fn($a,$b) => $a['name'] <=> $b['name']);
            break;
            
            case ['name', 'DESC']:
                //tri par name DESC
                usort($results, fn($a,$b) => $b['name'] <=> $a['name']);
            break;
        
            case ['nbformation', 'ASC']:
                //tri par nombre de formation ASC puis par name ASC
                usort($results, function($a, $b) {
                    if(count($a['nbformation']) == count($b['nbformation'])){
                        return $a['name'] <=> $b['name'];
                    } else {
                        return count($a['nbformation']) <=> count($b['nbformation']);
                    }
                    return $b['name'] <=> $a['name'];
                });
            break;
        
            case ['nbformation', 'DESC']:
                //tri par nombre de formation DESC puis par name ASC
                usort($results, function($a, $b) {
                    if(count($a['nbformation']) == count($b['nbformation'])){
                        return $a['name'] <=> $b['name'];
                    } else {
                        return count($b['nbformation']) <=> count($a['nbformation']);
                    }
                    return $b['name'] <=> $a['name'];
                });
            break;
        }
        return $results;
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur): array
    {
        if ($valeur=="") {
            return $this->findAllOrderBy('name', 'ASC');
        }
        return $this->createQueryBuilder('p')
            ->select(self::SELECT_PARAM)
            ->addSelect(self::ADDSELECT_NAME)
            ->addSelect(self::ADDSELECT_CATEGORIE)
            ->leftjoin('p.formations', 'f')
            ->leftjoin('f.categories', 'c')
            ->where('p.'.$champ.' LIKE :valeur')
            ->setParameter('valeur', '%'.$valeur.'%')
            ->groupBy('p.id')
            ->addGroupBy('c.name')
            ->orderBy('p.name', 'ASC')
            ->addOrderBy('c.name')
            ->getQuery()
            ->getResult();
    }
    
    
    /**
    * Enregistrements dont un champ contient une valeur
    * ou tous les enregistrements si la valeur est vide
    * @param type $champ
    * @param type $valeur
    * @param type $table
    * @return Playlist[]
    */
    public function findByContainValueInTable($champ, $valeur, $table=""): array
    {
        if ($valeur=="") {
            return $this->findAllOrderBy('name', 'ASC');
        }
        return $this->createQueryBuilder('p')
            ->select(self::SELECT_PARAM)
            ->addSelect(self::ADDSELECT_NAME)
            ->addSelect(self::ADDSELECT_CATEGORIE)
            ->leftjoin('p.formations', 'f')
            ->leftjoin('f.categories', 'c')
            ->where('c.'.$champ.' LIKE :valeur')
            ->setParameter('valeur', '%'.$valeur.'%')
            ->groupBy('p.id')
            ->addGroupBy('c.name')
            ->orderBy('p.name', 'ASC')
            ->addOrderBy('c.name')
            ->getQuery()
            ->getResult();
    }
}
