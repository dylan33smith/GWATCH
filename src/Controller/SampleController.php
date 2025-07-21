<?php
namespace App\Controller;

use App\Entity\Sample;
use App\Form\SampleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SampleController extends AbstractController
{
    #[Route('/sample/new', name: 'sample_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sample = new Sample();
        $sample->setCreatedAt(new \DateTime());

        $form = $this->createForm(SampleType::class, $sample);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the sample
            $em->persist($sample);
            $em->flush();
            
            // Create a new sample for the form (reset)
            $sample = new Sample();
            $sample->setCreatedAt(new \DateTime());
            $form = $this->createForm(SampleType::class, $sample);
            
            $this->addFlash('success', 'Sample created successfully!');
        }

        return $this->render('sample/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/samples', name: 'sample_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $samples = $em->getRepository(Sample::class)->findAll();
        return $this->render('sample/list.html.twig', ['samples' => $samples]);
    }
}