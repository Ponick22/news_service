<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        $news = $this->getDoctrine()->getRepository(News::class)->findAll();
        return $this->render('main/index.html.twig', ['news' => $news]);
    }

    /**
     * @Route("/news/{id}", name="news")
     */
    public function showNews(int $id): Response
    {
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);        
        //dd($news->id);
        return $this->render('main/news.html.twig', ['news' => $news]);
    }
}
