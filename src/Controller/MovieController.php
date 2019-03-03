<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Movie;
use App\Entity\User;
use App\Entity\Evaluation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


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
     * @Route("/single/{id}", name="single")
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
     * @Route("/evaluation/{id}", name="evaluation")
     * @Isgranted("ROLE_USER")
     * @var \App\Entity\User $user
     */
    public function rate(Movie $movie, Request $request)
    {
        $evaluation = new Evaluation();
        dump($evaluation);
        $form = $this->createFormBuilder($evaluation)
            ->add('comment', TextType::class)
            //trouver comment changer le label dans la vue
            ->add('grade', IntegerType::class, 
              [
              'attr' => ['min' => 0, 'max' =>10]
              ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
          $evaluation->setMovie($movie);
          $evaluation->setUser($this->getUser());
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($evaluation);
          $entityManager->flush();
          //addflash
          return $this->redirectToRoute('index');
        }

        return $this->render('movie/evaluation.html.twig', [
          "movie" => $movie,
          "form" => $form->createView()
        ]);
    }
}
