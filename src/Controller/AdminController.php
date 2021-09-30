<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\News;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AdminController extends AbstractController
{
    /**     
     * @Route("/admin/page-{page}", name="admin_page")
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

        return $this->render('admin/index.html.twig', ['news' => $news, 'pages' => $pages, 'cur_page' => $page]);
    }

    /**
     * @Route("/admin/add_news", name="show_add_news")
     */
    public function showAddNews(): Response
    {            
        return $this->render('admin/add_news.html.twig', ['date' => date("Y-m-d")]);
    }

    /**
     * @Route("/admin/add_news_res", name="add_news")
     */
    public function addNews(Request $request): Response
    { 
        $slugger = new AsciiSlugger();
        $em = $this->getDoctrine()->getManager();
        $news = new News();            
        $news->setTitle($request->get('title'));
        $image = $request->files->get('image');        
        if ($image){
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            try {
                $image->move($this->getParameter('image_directory'), $newFilename);
            } catch (FileException $e) {
                    
            }
            $news->setImage($newFilename);
        }
            
        $news->setAnnotation($request->get('annotation'));
        $news->setContent($request->get('content'));
        $date = new \DateTime($request->get('date'));
        $news->setPublicationDate($date);

        $em->persist($news);
        $em->flush();
            
        return $this->redirectToRoute('admin_page'); 
    }
    
    /**
     * @Route("/admin/news/{id}", name="show_edit_news")
     */
    public function showEditNews(int $id): Response
    {      
        $page = 1;
        if (isset($_COOKIE["page"])) {
            $page = $_COOKIE["page"];
        }
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);
        if ( is_null($news)) 
            throw $this->createNotFoundException();  
        else
        return $this->render('admin/edit_news.html.twig', ['news' => $news, 'page' => $page]);
    }

    /**
     * @Route("/admin/edit_news", name="edit_news")
     */
    public function editNews(Request $request): Response
    {               
        $em = $this->getDoctrine()->getManager();
        $news = $this->getDoctrine()->getRepository(News::class)->find($request->get('id'));            
        $news->setTitle($request->get('title'));
        $news->setAnnotation($request->get('annotation'));
        $news->setContent($request->get('content'));
        $date = new \DateTime($request->get('date'));
        $news->setPublicationDate($date);        
        if($request->get('visible'))
            $isVisible = true;
        else
            $isVisible = false;
        $news->setIsVisible($isVisible);
        $em->flush();

        $page = 1;
        if (isset($_COOKIE["page"])) {
            $page = $_COOKIE["page"];
        }
            
        return $this->redirectToRoute('admin_page',  array('page' => $page)); 
    }
}