<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{   
    /**
     * @Route("/", name="home")
     */
   
    public function index(ManagerRegistry $doctrine ): Response
    {   
        #Etape 1 : récuperer tous les livres
        $books = $doctrine->getRepository(Book::class)->findAll();
        return $this->render('home/index.html.twig',[
            "books" => $books
        ]);
    }
    
   

}
