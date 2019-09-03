<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
    /**
     * @Route("/admin/user", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
     {
         //récup info barre recherche
         $data = $request->query->get('search');
         if (!$data) {
             $req = $entityManager->getRepository('App:User')->findUsers();
         }
         //si utilisation barre de recherche
         if ($data) {
             $req = $entityManager->getRepository('App:User')->UserNameContain($data);
         }
         return $this->render('user/index.html.twig', ["users" => $req]);
     }




    /**
     * @Route("user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("user/edit/{id}", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($user->getId() == $this->getUser()->getId()) {
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form['imageFilename']->getData();
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImageFilename($newFilename);
            }
            $this->addFlash("success", "La profil a bien été modifié !");
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('home');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }else{
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("admin/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        $entityManager= $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $events=$user->getEvents();
            //suppression de l'utilisateur dans la liste des inscrits à une sortie
            foreach ($events as $event ) {
                $user->removeEvents($event);
                $event->removeSubscribersUsers($user);
            }

            $entityManager->initializeObject($user->getCreatedEvents());
            $createdEvents=$user->getCreatedEvents();
            //suppression des événements organisés par l'utilisateur supprimé
            foreach ($createdEvents as $createdEvent){
                $subscribersUsers=$createdEvent->getSubscribersUsers();
                //suppression de l'événement organisé par l'utilisateur supprimé chez les utilisateurs inscrits à l'événement
                foreach ($subscribersUsers as $subscribersUser) {
                    $createdEvent->removeSubscribersUsers($subscribersUser);
                    $subscribersUser->removeEvents($event);
                }
                $entityManager->remove($createdEvent);
            }
            //suppression du lien utilisateur supprimé - site de rattachement
            $entityManager->initializeObject($user->getSite());
            $site=$user->getSite();
            $entityManager->initializeObject($site->getUsers());
            $user->setSite(null);
            $site->getUsers()->removeElement($user);
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash("success", "Le profil a bien été effacé !");
        }

        return $this->redirectToRoute('user_index');
    }

    // Annotation à mettre
    // Deposer, depose_depose ,get post

    /**
     * @Route("admin/resetting/{id}", name="reset_password", methods={"GET","POST"})
     */
    public function editAction(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $password = $request->request->get('etiquettebundle_user')['password'];

            // Si l'ancien mot de passe est bon
            if ($passwordEncoder->isPasswordValid($user, $password)) {
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $form->get('newPassword')->getData());
                $user->SetPassword($newEncodedPassword);

                $em->persist($user);
                $em->flush();

                $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                return $this->redirectToRoute('app_login');
            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
            }
        }

        return $this->render('resetting/resetpassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }



}
