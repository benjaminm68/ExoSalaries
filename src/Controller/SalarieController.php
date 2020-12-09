<?php

namespace App\Controller;

use App\Entity\Salarie;
use App\Entity\Entreprise;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("salarie")
 */
class SalarieController extends AbstractController
{

    /**
     * @Route("/delete/{id}", name="salarie_delete")
     */
    public function delete(Salarie $salarie){
        $em = $this->getDoctrine()->getManager();

        $em->remove($salarie);
        $em->flush();

        return $this->redirectToRoute('salarie');
    }

    /**
     * @Route("/add", name="salaries_add")
     * @Route("/edit/{id}", name="salaries_edit")
     */
    public function addSalarie(Salarie $salarie = null, Request $request, EntityManagerInterface $manager){
    
       if(!$salarie){
           $salarie = new Salarie();
       }

       $form = $this->createFormBuilder($salarie)
       ->add('nom',TextType::class)
       ->add('prenom',TextType::class)
       ->add('datenaissance',DateType::class, [
           'years' => range(date('Y'),date('Y')-70),
           'label' => 'Date de naissance',
           'format' => 'ddMMMMyyyy'
       ])

       ->add('adresse',TextType::class)
       ->add('cp',TextType::class)
       ->add('ville',TextType::class)
       ->add('dateEmbauche',DateType::class,[
        'years' => range(date('Y'),date('Y')-70),
        'label' => 'Date d\'embauche',
        'format' => 'ddMMMMyyyy'
    ])

    ->add('Entreprise', EntityType::class, [
        'class' => Entreprise::class,
        'choice_label' => 'raisonSociale',
    ])
    ->add('Valider', SubmitType::class)
    ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($salarie);
            $manager->flush();

            return $this->redirectToRoute('salaries');
        }

        return $this->render('salarie/add_edit.html.twig', [
            
            'form' => $form->createView(),
            'editMode' => $salarie->getId() !== null
        ]);
    }

    
    /**
     * @Route("/", name="salaries")
     */
    public function index()
    {
        
        $salaries = $this->getDoctrine()
        ->getRepository(salarie::class)
        ->getAll();
        
        return $this->render('salarie/index.html.twig', [
            'salaries' => $salaries,
            ]);
    }

      /**
     * @Route("/{id}", name="salarie_show")
     */
    public function show(Salarie $salarie = null) {
        
        if($salarie){
            return $this->render('salarie/show.html.twig',[
                 'salarie' => $salarie
             ]);

        } else {
            return $this->redirectToRoute('salaries');
        }
    }


}
