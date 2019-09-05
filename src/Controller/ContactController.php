<?php


namespace App\Controller;

use App\Form\ContactType;

use App\Entity\Contact;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route ("contact", name="contact", methods={"GET","POST"})
     */
    public function contact(Request $request, Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $firstname = $form['firstname']->getData();
            $email = $form['email']->getData();
            $phone = $form['phone']->getData();
            $message = $form['message']->getData();


            $contact->setName($name);
            $contact->setFirstname($firstname);
            $contact->setEmail($email);
            $contact->setPhone($phone);
            $contact->setMessage($message);

            $message = (new \Swift_Message)
                ->setSubject('message Sortir.com')
                ->setFrom($email)
                ->setTo('sebastien.corbineau2018@campus-eni.fr')
                ->setBody($this->renderView('/email/email.html.twig',array('name' => $name,'firstname' => $firstname,'phone'=>$phone,'email'=>$email,'message'=>$message)));
            $mailer->send($message);

            $this->addFlash("success", "Votre message a été envoyé !");
            return $this->redirectToRoute('home');
        }
        return  $this->render("/contact/contact.html.twig", ['formContact' => $form->createView()]);
    }
}