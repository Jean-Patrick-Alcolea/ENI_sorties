<?php

namespace App\Repository;

use App\Entity\Filtre;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function getSortiesForHomePage(Filtre $filtre): array
    {
        $warningMessage = null;//en cas de précisions à apporter à l'user

        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.inscriptions', 'i')
            ->leftJoin('i.participant', 'p')
            ->leftJoin('s.campus', 'co')
            ->leftJoin('s.participantOrganisateur', 'po');

        //l'user veut voir les sorties d'un campus en particulier:
        if ($filtre->getChoixIdCampus() !== null){
            $queryBuilder->andWhere('co.id = :choixIdCampus')
                ->setParameter('choixIdCampus', $filtre->getChoixIdCampus());
        }

        //l'user cherche un mot dans le noms de la sortie
        if ($filtre->getStringMotSearch() !== null){
            $queryBuilder->andWhere('s.nom LIKE :stringMotSearch')
                ->setParameter('stringMotSearch', '%'.$filtre->getStringMotSearch().'%');
        }

        //début de l'interval de recherche
        if ($filtre->getDateDebutSearch() !== null){
            $queryBuilder->andWhere('s.dateHeure >= :dateDebutSearch')
                ->setParameter('dateDebutSearch', $filtre->getDateDebutSearch());
        }

        //fin de l'interval de recherche
        if ($filtre->getDateFinSearch() !== null){
            if ($filtre->getDateFinSearch() > $filtre->getDateDebutSearch()){
                $queryBuilder->andWhere('s.dateHeure <= :dateFinSearch')
                    ->setParameter('dateFinSearch', $filtre->getDateFinSearch());
            }else{
                $warningMessage = 'La date de fin n\'est pas ultérieure à la date de début, elle est ignorée.';
            }
        }

        //l'user est l'organisateur?
        if ($filtre->isCheckUserOrganise()){
            $queryBuilder->andWhere('s.participantOrganisateur = :utilisteur')
                ->setParameter('utilisteur', $filtre->getIdUser());
        }

        //l'user est inscrit?
        if ($filtre->isCheckUserInscrit()){
            $queryBuilder->andWhere('p.id = :userId')
                ->setParameter('userId', $filtre->getIdUser());
        }

        //trouver les sorties passées
        if ($filtre->isCheckSortiePassee()){
            $queryBuilder->andWhere('DATE_ADD(s.dateHeure, s.duree, \'HOUR\') < :currentDate')
                ->setParameter('currentDate', new \DateTime());
        }


        //on retourne les sorties mais aussi le warningMessage
        return ['sorties' =>$queryBuilder->getQuery()->getResult(), 'warningMessage' => $warningMessage];
    }



    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


}
