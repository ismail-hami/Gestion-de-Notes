<?php

namespace App\Controller;
use App\Entity\Module;


use Doctrine\ORM\EntityManagerInterface;     // ✅ REQUIRED
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListeNController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function show_home(): Response
    {
        return $this->render('liste_n/index.html.twig');
    }

#[Route('/liste/n', name: 'app_liste_n')]
public function showListeN(EntityManagerInterface $entityManager): Response
{
$repository=$entityManager->getRepository(Module::class);
$modules=$repository->findAll();
return $this->render('liste_n/ListeN.html.twig',['data'=>$modules]);

}

}