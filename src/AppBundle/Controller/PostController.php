<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Post controller.
 *
 * @Route("post")
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     * @Route("/", name="post_index")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Post::class);
        $categoryId = $request->get("category_id");

        if ($categoryId) {
            $posts = $repo->findByCategory($categoryId);
        } else {
            $posts = $repo->findAll();
        }

        return $this->render('post/index.html.twig', array(
            'posts' => $posts
        ));
    }

    /**
     * Creates a new post entity.
     *
     * @Route("/new", name="post_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** @var User $user */
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $post->setUser($user);

            $em->persist($post);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The post has been successfully created');

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     * @Route("/{id}", name="post_show")
     * @Method("GET")
     * @param Post $post
     * @return Response
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Post $post)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        // Can edit only my post if i'm not an ADMIN
        if ($post->getUser()->getId() != $currentUser->getId() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The post has been successfully updated');

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a post entity.
     *
     * @Route("/{id}", name="post_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Post $post)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        // Can remove only my post if i'm not an ADMIN
        if ($post->getUser()->getId() != $currentUser->getId() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The post has been successfully deleted');
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
