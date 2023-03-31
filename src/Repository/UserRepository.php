<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByFirstname($firstname, $lastname)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\User u
            WHERE u.firstname = :firstname 
            AND u.lastname = :lastname
            ORDER BY u.firstname ASC'
        )->setParameter('firstname', $firstname)->setParameter('lastname', $lastname);

        // returns an array of Product objects
        return $query->getResult();
    }
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function createQueryTwo(){
        $firstname = "bruno";
        $lastname= "gaccio";
        $qb = $this->createQueryBuilder('u')
        //selectionne les colonnes
            ->select('u.firstname', 'u.lastname', 'u.email')
            //conditions de recherche
            ->where('u.firstname = :firstname')
            ->where('u.lastname = :lastname')
            // ordonne par firstname du plus petit au plus grand
            ->orderBy('u.firstname', 'ASC')
            //rend le parametre firstname disponbile dans la query
            ->setParameter('firstname', $firstname)
            ->setParameter('lastname', $lastname);
        // rajoute la condition lastname =gaccio dans la query
        $qb->andWhere('u.lastname = "gaccio"');
        // donne à query la valeur de $qb->getQuery()
        $query = 
        //créé la query à partir de qb
        $qb->getQuery();
        // exxeucte la query
        return $query->execute();

    }
    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
