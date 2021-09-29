<?php

namespace App\Controller;
use App\Services\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     * @Route("/page-{page}", name="main_page")
     */
    public function index(int $page = 1, PaginationService $service): Response
    {
        // Всего новостей     
        $count = $this->getDoctrine()->getRepository(News::class)->count(); 
        // Количество новостей на странице      
        $limit = 1;
        // Количество страниц
        $pages = ceil($count/$limit);
        // С какой новости выводить
        $start = ($page - 1) * $limit;       
        $news = $this->getDoctrine()->getRepository(News::class)->limit($start, $limit);  
        
        return $this->render('main/index.html.twig', ['news' => $news, 'pages' => $pages, 'cur_page' => $page]);
    }

    /**
     * @Route("/news/{id}", name="news")
     */
    public function showNews(int $id): Response
    {
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);      
        return $this->render('main/news.html.twig', ['news' => $news]);
    }
}
