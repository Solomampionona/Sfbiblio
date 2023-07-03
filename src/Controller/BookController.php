<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
                    # (CRUD)CREATE
    /**
     * @Route("/book/add", name="book_add")
     */
    public function add(ManagerRegistry $doctrine)
    {   
        #etape1: On appel l'entity manager de doctrine
        $entityManager = $doctrine->getManager();
        
        #étape2: on crée l'objet et on l'alimente
        $book = new Book;
        $book->setTitle("Ravage");
        $book->setDescription("Le futur vu par René Barjavel");
        $book->setPublishedAt(new \DateTime());
        
        #étape3: On indique a doctrine que l'on souhaite préparer l'enregistremment d'un nouvel élément
        $entityManager->persist($book);
        
        #étape4: On valide a doctrine que l'on veut enregistrer/persister en BDD
        $entityManager->flush();
        
        #étape5: On     ffiche ou on redirige vers une autre page
        // return new Response("<h1> Bravo le livre a été ajouté !!</h1>");
        return $this->redirectToRoute('home');


    }
            

                 # (CRUD)UPDATE
    /**
     * @Route("/book/edit/{id}", name="book_edit")
     */
    public function edit($id,ManagerRegistry $doctrine) 
    {
        #étape1: On appelle l'entity manager de doctrine
        $entityManager = $doctrine->getManager();
        
        #étape2: On récupère (grace au Repository de doctrine) l'objet que l'on souhaite modifier
        $book = $doctrine->getRepository(book::class)->find($id);

        #étape3: On modifier les valeur de l'objet que l'on souhaite modifier
        $book->setDescription("");
        $book->setTitle("");

        #étape4: On valide les modifications
        $entityManager->flush();

        #étape5: on affiche ou on redirige vers un autre page
        return new Response("<h1>Bravo le livre a été modifié</h1>");
    }
                      #(CRUD)DELETE
     /**
     * @Route("/book/delete/{id}", name="book_delete")
     */
    public function delete($id,ManagerRegistry $doctrine) 
    {
        #étape1: On appelle l'entity manager de doctrine
        $entityManager = $doctrine->getManager();
        
        #étape2: On récupère (grace au Repository de doctrine) l'objet que l'on souhaite supprimer
        $book = $doctrine->getRepository(book::class)->find($id);

        #étape3: On supprime à l'aide de l'entity manager de doctrine
        $entityManager->remove($book);
       
        #étape4: On valide les modifications
        $entityManager->flush();

        #étape5: on affiche ou on redirige vers un autre page
        return new Response("<h1>Bravo le livre a été suprimé</h1>");
    }


                #(CRUD)READ : ALL
    /**
     * @Route("/book/list", name="book_list")
     */

     public function readAll(ManagerRegistry $doctrine)
     {  
        $this->denyAccessUnlessGranted('ROLE_USER');
        #étape1: Récuperer tous les livres
        $books = $doctrine->getRepository(Book::class)->findAll();
        
        
        return $this->render("book/list.html.twig",[
            "books" => $books
        ]);
     }
     
      
     /**
     * @Route("/book/detail{id}", name="book_detail")
     */
    public function item(ManagerRegistry $doctrine, $id)
    {   
        $book =$doctrine->getRepository(Book::class)->find($id);

        return $this->render('book/item.html.twig',[
            "book" => $book
        ]);
    }

    /**
     * @Route("/books/new", name="book_new")
     */

     public function new(Request $request,ManagerRegistry $doctrine)
     {
        #étape1: Créer un objet vide
        $book = new Book;
        $book->setPublishedAt(new \DateTimeImmutable()); #mettre une valeur par default
        

        // #étape2: Création du formulaire (Méthode 1)
        // $formBook = $this->createFormBuilder($book)
        //     ->add('title', TextType::class)
        //     ->add('description',TextareaType::class)
        //     ->add('sauvegarder', SubmitType::class)
        //     ->getForm();


        #2tape2: création du formulaire (methode 2)
        $formBook = $this->createForm(BookType::class, $book);

        $formBook->handleRequest($request);
        if($formBook->isSubmitted() && $formBook->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($book);
            $entityManager->flush();
            #on créé une message flash
            $this->addFlash('book_new_success', "Bravo le livre a bien été ajouté !");

            return $this->redirectToRoute('book_list');
        }

        #étape3: On envoie le formulaire dans une vue
        return $this->render('book/form-new.html.twig', [
            "formBook" => $formBook->createView()
        ]);
     }

     /**
      * @Route("/book/modif/{id}", name="book_modif")
      */
      public function modif($id, Request $request,ManagerRegistry $doctrine)
      {
        $book = $doctrine->getRepository(Book::class)->find($id);

        $modifBook = $this->createForm(BookType::class, $book);
        $modifBook->handleRequest($request);
        if($modifBook->isSubmitted() && $modifBook->isValid())
        {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash('book_modif',"Bravo le livre ".$book->getTitle()." a bien été modifié !");

            return $this->redirectToRoute('book_list');
        }

      
        return $this->render('book/modif.html.twig', [
            "formBook" => $modifBook->createView()
        ]);
      }

      
     /**
      * @Route("/book/suprim/{id}", name="book_suprim")
      */
      public function suprim($id,ManagerRegistry $doctrine)
      {
        
        $entityManager = $doctrine->getManager();

        $book = $doctrine->getRepository(book::class)->find($id);

        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash('book_suprim',"Le livre ".$book->getTitle()." a bien été suprimé!");

        return $this->redirectToRoute('book_list');


    }
    /**
     * @Route("/orm", name="orm")
     */
    public function orm(ManagerRegistry $doctrine)
    {
        #findAll()
        $bookAll = $doctrine->getRepository(Book::class)->findAll();

        #find()
        $bookById = $doctrine->getRepository(Book::class)->find(8);

        #findBy()
        $bookBy = $doctrine->getRepository(Book::class)->findBy(['Price' => 4 ]);

        #FindOneBy()
        $bookOneby = $doctrine->getRepository(Book::class)->findOneBy(['Price' => 4 , 'author' => 3]);
        #
        $bookGreater=$doctrine->getRepository(Book::class)->findByGreaterPrice(10);

        return $this->render('book/orm.html.twig', [
            'bookAll' => $bookAll,
            'bookById' => $bookById,
            'bookBy' => $bookBy,
            'bookOneBy' => $bookOneby,
            'bookGreater'=> $bookGreater

        ]);
    }
      
    

}
