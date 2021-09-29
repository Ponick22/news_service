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
     * @Route("/page-{page}", name="main_page")
     */
    public function index(int $page = 1): Response
    {
        setcookie("page", $page, time()+3600);
        // Всего новостей     
        $count = $this->getDoctrine()->getRepository(News::class)->countAll(); 
        // Количество новостей на странице      
        $limit = 2;
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
        $page = 1;
        if (isset($_COOKIE["page"])) {
            $page = $_COOKIE["page"];
        }
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);      
        return $this->render('main/news.html.twig', ['news' => $news, 'page' => $page]);
    }
}
