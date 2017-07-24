<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Security("has_role('ROLE_USER')")
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
     * @Route("/{slug}", name="post_show")
     * @Method("GET")
     * @param Post $post
     * @return Response
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeletePostForm($post);
        $commentForm = $this->createForm(CommentType::class, new Comment());

        $repo = $this->getDoctrine()->getManager()->getRepository(Comment::class);
        $comments = $repo->findByPost($post);

        $deleteCommentForm = $this->createDeleteCommentForm(0);

        return $this->render('post/show.html.twig', array(
            'post'                  => $post,
            'comments'              => $comments,
            'delete_form'           => $deleteForm->createView(),
            'comment_form'          => $commentForm->createView(),
            'delete_comment_form'   => $deleteCommentForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
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
     * Process form to add a comment to an existing post entity.
     *
     * @Route("/{id}/comment", name="post_new_comment")
     * @Method({"POST"})
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse|Response
     */
    public function newCommentAction(Request $request, Post $post)
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($this->container->get('security.authorization_checker')->isGranted("IS_AUTHENTICATED_FULLY")) {
                /** @var User $currentUser */
                $currentUser = $this->get('security.token_storage')->getToken()->getUser();
                $comment->setUser($currentUser);
            }

            $comment->setPost($post);

            $em->persist($comment);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The comment has been successfully added');

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->redirectToRoute('post_show', array('id' => $post->getId()));
    }

    /**
     * Deletes a comment entity.
     *
     * @Route("/{id}/comment", name="post_delete_comment")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function deleteCommentAction(Request $request, Comment $comment)
    {
        $form = $this->createDeleteCommentForm($comment->getId());
        $form->handleRequest($request);
        $post_id = $comment->getPost()->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The comment has been successfully deleted');
        }

        return $this->redirectToRoute('post_show', array('id' => $post_id));
    }

    /**
     * Deletes a post entity.
     *
     * @Route("/{id}", name="post_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
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

        $form = $this->createDeletePostForm($post);
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
    private function createDeletePostForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Creates a form to delete a comment entity.
     *
     * @param int $comment_id
     * @return Form The form
     */
    private function createDeleteCommentForm(int $comment_id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete_comment', array('id' => $comment_id)))
            ->setMethod('DELETE')
            ->getForm();
    }
}
