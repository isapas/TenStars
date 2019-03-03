<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Movie;
use App\Entity\User;
use App\Entity\Evaluation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\EvaluationType;




class MovieController extends AbstractController
{

   /**
     * @Route("/", name="index")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        $movies = $this->getDoctrine()->getRepository(Movie::class)->findAll();
        return $this->render('movie/index.html.twig', [
          "movies" => $movies
        ]);
    }

   /**
     * @Route("/single/{id}", name="single", requirements={"id"="\d+"}))
     */
    public function show(Movie $movie)
    {        
  
      $evaluations = $movie->getEvaluations();
      $average = "Soyez le premier à poster un évaluation pour ce film";
      foreach($evaluations as $key => $evaluation) 
        {
        $grade = $evaluation->getGrade();
        if(!empty($grade))   {
          $average = $movie->getAverage();
        }
     }
      return $this->render('movie/single.html.twig', 
      [
       "average" => $average, 
        "movie" => $movie,
     ]);
    }

    /**
     * @Route("/evaluation/{id}", name="evaluation",requirements={"id"="\d+"})
     * @Isgranted("ROLE_USER")
     * @var \App\Entity\User $user
     */
    public function rate(Movie $movie, Request $request)
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
          //accorde l'accès aux users connectés
          $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
          $evaluation->setMovie($movie);
          $evaluation->setUser($this->getUser());
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($evaluation);
          $entityManager->flush();
          //addflash
          return $this->redirectToRoute('single',['id'=>$movie->getId()]);
        }
        

        return $this->render('movie/evaluation.html.twig', [
          "movie" => $movie,
          'evaluationForm' => $form->createView()
          ]);
    }
}
