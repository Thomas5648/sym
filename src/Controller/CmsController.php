<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Jednostka;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JednostkaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\CmsType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use App\Repository\UserRepository;


    /**
    * @Route("/cms")
    */
class CmsController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     *  @var JednostkaRepository
     */
    private $jednostkaRepository;
    /**
     *  @var FormFactoryInterface
     */
    private $formFactory;
     /**
     *  @var EntityManagerInterface
     */
    private $entityManager;
     /**
     *  @var RouterInterface;
     */
    private $router;
     /**
     *  @var FlashBagInterface
     */
    private $flashBag;
     /**
     *  @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(\Twig_Environment $twig, JednostkaRepository $jednostkaRepository, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, RouterInterface $router,FlashBagInterface $flashBag, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->twig = $twig;
        $this->jednostkaRepository = $jednostkaRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }
    /**
     * @Route("/", name="cms_index")
     */
    public function index(TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {

        $currentUser = $tokenStorage->getToken()->getUser();

        $userToFollow = [];

        if($currentUser instanceof User){
            $posts = $this->jednostkaRepository->findAllByUsers($currentUser->getFollowing());

            $userToFollow = count($posts) === 0 ? 
            $userRepository->findAllWithMoreThan5PostsExceptUser(
                $currentUser
            ): [];

        }else{
            $posts = $this->jednostkaRepository->findBy([], ['time' =>'DESC']);
        }

        $html = $this->twig->render('index.html.twig', [
            'posts' => $posts,
            'userToFollow' => $userToFollow
        ]);

        return new Response($html);
    }
    //edytowanie postów
    /**
     * @Route("/edit/{id}", name="cms_edit")
     * @Security("is_granted('edit', jednostka)", message="Brak dostępu")
     */
    public function edit(Jednostka $jednostka, Request $request)
    {
        $form = $this->formFactory->create(CmsType::class, $jednostka);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('cms_index'));
        }

        return new Response(
            $this->twig->render('add.html.twig',
            ['form'=> $form->createView()]
            )
        );   
    }
    //usuwanie 
    /**
    * @Route("/delete/{id}", name="cms_delete")
    * @Security("is_granted('delete', jednostka)", message="Brak dostępu")
    */
    public function delete(Jednostka $jednostka)
    {
        $this->entityManager->remove($jednostka);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Usunięto');

        return new RedirectResponse($this->router->generate('cms_index'));

    }   
    // dodawanie postów
    /**
     * @Route("/add", name="cms_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();

        $cms = new Jednostka();
        $cms->setUser($user);

        $form = $this->formFactory->create(CmsType::class, $cms);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($cms);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('cms_index'));
        }

        return new Response(
            $this->twig->render('add.html.twig',
            ['form'=> $form->createView()]
            )
        );
    }
    // Wyświetla wszystkie posty użytkownika 

    /**
     * @Route("/user/{username}", name="cms_post_user")
     */
    public function userPost(User $userWithPosts)
    {
        $html = $this->twig->render('user-post.html.twig', [
            'posts' => $this->jednostkaRepository->findBy(
                ['user'=> $userWithPosts], 
                ['time' =>'DESC']), 
            'user' => $userWithPosts,
            ]);
        return new Response($html);
    }


    /**
    * @Route("/{id}", name="cms_post_post")
    */
    public function post(Jednostka $post)
    {

        return new Response(
            $this->twig->render('post.html.twig',
            [
                'post' => $post
            ])
            );
    }
}