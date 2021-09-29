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
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/add_news", name="add_news")
     */
    public function addNews(): Response
    {            
        return $this->render('admin/add_news.html.twig', ['date' => date("Y-m-d")]);
    }

    /**
     * @Route("/admin/add_news_content", name="add_news_content")
     */
    public function addNewsContent(Request $request): Response
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
            
            return $this->redirectToRoute('admin'); 
    }   
}
