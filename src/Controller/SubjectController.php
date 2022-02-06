<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubjectController extends AbstractController
{
    private SubjectRepository $subjectRepository;

    public function __construct(SubjectRepository $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    #[Route('/admin/subject', name: 'subject')]
    public function index(): Response
    {
        $subjects = $this->subjectRepository->findAll();

        return $this->json($subjects, 200, [], ['groups' => 'list']);
    }
}
