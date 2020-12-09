<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Salarie;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/entreprises")
 */
class EntrepriseController extends AbstractController
{
    /**
     * @Route("/delete/{id}", name="entreprise_delete")
     */
    public function delete(Entreprise $entreprise){
        $em = $this->getDoctrine()->getManager();

        $em->remove($entreprise);
        $em->flush();

        return $this->redirectToRoute('entreprises');
    }
    
    /**
     * @Route("/add", name="entreprise_add")
     * @Route("/edit/{id}", name="entreprise_edit")
     */
    public function addEdit(Entreprise $entreprise = null, Request $request, EntityManagerInterface $em){
        
        if(!$entreprise){
            $entreprise = new Entreprise();
        }

        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $em->persist($entreprise);
            $em->flush();
            
            return $this->redirectToRoute('entreprises');
        }
        
        return $this->render('entreprise/add_edit.html.twig',[
            
            'formEntreprise' => $form->createView(),
            'editMode' => $entreprise->getId() !== null,
            'entreprise' => $entreprise->getRaisonSociale()
            ]);
            
        }
        
        /**
         * @Route("/{id}", name="entreprise_show")
         */
        public function show(Entreprise $entreprise = null): Response {

            return $this->render('entreprise/show.html.twig', [
                'entreprise' => $entreprise
                 ]);
        }


        /**
         * @Route("/", name="entreprises")
     */
    public function index()
    {
        $entreprises = $this->getDoctrine()
                ->getRepository(Entreprise::class)
                ->getAll();

        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

}

