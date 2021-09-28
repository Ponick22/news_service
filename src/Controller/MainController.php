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
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/news/{id}", name="news")
     */
    public function showNews(int $id): Response
    {
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);
        //dd($news);
        return $this->render('main/news.html.twig', ['news' => $news]);
    }
}
