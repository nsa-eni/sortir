<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\RegistrationByImportFileType;
use App\Form\RegistrationFormType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/admin/user/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setActif(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/admin/user/import_file", name="import_file_and_register", methods={"GET", "POST"})
     * @return Response
     */
    public function registerByImportingFile(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(RegistrationByImportFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $file = (object) $form->get('file')->getData();
            $this->parseCsvFile($file, $form->get('site')->getData(), $passwordEncoder);
        }

        return $this->render('registration/register_by_import_file.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param UploadedFile $file
     * @param Site $site
     * @param UserPasswordEncoderInterface $encoder
     */
    private function parseCsvFile(UploadedFile $file, Site $site, UserPasswordEncoderInterface $encoder)
    {
        $tmp_filename = $file->getBasename();
        try{
            $content = IOFactory::load("/tmp/$tmp_filename");
            $data = $content->getActiveSheet()->toArray(null, true, true, true);
            $this->createUsers($site, $data, $encoder);
        }catch (Exception $exception){
            print $exception->getMessage();
        }catch (\PhpOffice\PhpSpreadsheet\Exception $exception){
            print  $exception->getMessage();
        }
    }

    /**
     * @param Site $site
     * @param array $data
     * @param UserPasswordEncoderInterface $encoder
     */
    private function createUsers(Site $site, array $data, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        foreach ($data as $datum){
            if (is_null($userRepo->findOneBy(['email'=> $datum['A']]))){
                $user = new User();
                $user->setPseudo('user.'.$datum['A']);
                $user->setEmail($datum['A']);
                $user->setPassword($encoder->encodePassword($user, 'password'));
                $user->setSite($site);
                $em->persist($user);
            }

        }
        $em->flush();
    }
}
