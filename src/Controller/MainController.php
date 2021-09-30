<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;

class MainController extends AbstractController
{
    // Количество новостей на странице 
    CONST LIMIT = 10;
    /**    
    * @Route("/api/page-{page}", name="get_news", methods={"GET"})
    */
    public function getNewsPage(int $page = 1)
    {
        // С какой новости выводить      
        $start = ($page - 1) * self::LIMIT;       
        $news = $this->getDoctrine()->getRepository(News::class)->limit($start, self::LIMIT, true);       
        return $this->json($news);
    }

    /**
     * @Route("/", name="main")
     * @Route("/page-{page}", name="main_page")
     */
    public function index(int $page = 1): Response
    {        
        setcookie("page", $page, time()+3600); 
        
        // Всего новостей     
        $count = $this->getDoctrine()->getRepository(News::class)->countAll(true);
        // Количество страниц
        $pages = ceil($count/self::LIMIT);      
        $news = json_decode($this->getNewsPage($page)->getContent()); 
         
        return $this->render('main/index.html.twig', ['news' => $news, 'pages' => $pages, 'cur_page' => $page]);
    }

    /**    
    * @Route("/api/news/{id}", name="get_one_news", methods={"GET"})
    */
    public function getNews(int $id)
    {
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);
        return $this->json($news);
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
        $news = json_decode($this->getNews($id)->getContent()); 
                    
        if (is_null($news) ||  !$news->isVisible) 
            throw $this->createNotFoundException();  
        else
            return $this->render('main/news.html.twig', ['news' => $news, 'page' => $page]);
    }
}