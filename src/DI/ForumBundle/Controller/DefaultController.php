<?php

namespace DI\ForumBundle\Controller;

use DI\ForumBundle\Entity\Answer;
use DI\ForumBundle\Entity\Subject;
use DI\ForumBundle\Form\AnswerType;
use DI\ForumBundle\Form\SubjectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listsubjects = $em->getRepository('DIForumBundle:Subject')->findAll();
        return $this->render('DIForumBundle:Default:index.html.twig', array('listsubjects' => $listsubjects));
    }

    public function addsubjectAction(Request $request) {
        $subject = new Subject();
        $form = $this->get('form.factory')->create(SubjectType::class, $subject);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($subject);
                $em->flush();

                $this->addFlash('success', 'Sujet créé avec succès');

                return $this->redirectToRoute('di_forum_homepage');
            }
        }

        return $this->render('DIForumBundle:Default:add.html.twig',
            array('formulaire' => $form->createView()));

    }

    public function viewsubjectAction(Subject $subject, Request $request)
    {
        $answer = new Answer();
        $form = $this->get('form.factory')->create(AnswerType::class, $answer);
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {


                $answer->setSubject($subject);
                $em->persist($answer);
                $em->flush();

                $this->addFlash('success', 'Réponse créé avec succès');

                return $this->redirectToRoute('di_forum_viewsubject', array('id' => $subject->getId()));
            }
        }
        $answers_list = $em->getRepository('DIForumBundle:Answer')->findBy(
            array('subject' => $subject), null, null, null);

        return $this->render('DIForumBundle:Default:view.html.twig',
            array('answers_list' => $answers_list, 'subject' => $subject, 'answer_form' => $form->createView()));
    }

}
