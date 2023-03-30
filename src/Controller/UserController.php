<?php

namespace App\Controller;

use App\Form\UserCreationFormType;
use App\Form\UserUpdateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
class UserController extends AbstractController
{
    #[Route('/user/list', name: 'user_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $repository = $entityManager->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }
    
    #[Route('/user/create_test}', name:'user_create_test')]
    public function test_create():Response{
        return $this->render('user/create_test.html.twig', [
        ]);
    }
    #[Route('/user/create_test_post}', name:'create_user_post')]
    public function create_user(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher):Response{
        $user = new User();
        $user->setFirstname($request->get('firstname'));
        $user->setLastname($request->get('lastname'));
        $user->setEmail($request->get('email'));
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $request->get('password')
            )
        );
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('user_list');
    }
    //create

    #[Route('/user/create', name: 'user_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        $user = new User();
        $form = $this->createForm(UserCreationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            var_dump($form->get('password')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'userCreationForm' => $form->createView(),
        ]);
    }
    //update
    #[Route('/user/edit/{id}', name: 'user_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id, UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }
        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if($password)
            {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_list');

        }
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('user/update.html.twig', [
            'userUpdateForm' => $form->createView(),
            'user'=> $user
        ]);
    }

    //delete
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete(EntityManagerInterface $entityManager, $id ): Response
    {
        $user =  $entityManager->getRepository(User::class)->find($id);
        if(!$user)
        {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirect('user_list');
    }
    //index
    #[Route('/user/{id}', name:'user_profile')]
    public function show(User $user): Response {
        if(!$user)
        {
            throw $this->createNotFoundException(
                'No user found for id '.$user->id
            );
        }
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
