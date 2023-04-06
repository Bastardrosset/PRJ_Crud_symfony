<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserGroupController extends AbstractController
{
    #[Route('/userGroup', name: 'app_userGroup')]
    public function index(): Response
    {
        $userGroup = [
            "id"=> "1",
            "name_groupe"=> "benoit",
            "description"=> "une bande de potes"
        ];
        return $this->render('userGroup/index.html.twig', [
            'controller_name' => 'UserGroupController',
            "userGroup" => $userGroup

        ]);
    }
}
