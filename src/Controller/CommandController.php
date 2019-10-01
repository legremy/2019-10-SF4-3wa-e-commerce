<?php

namespace App\Controller;

use DateTime;
use App\Entity\Command;
use App\Cart\CartService;
use App\Form\AddressType;
use App\Entity\CommandProduct;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

/** 
 * @IsGranted("ROLE_USER")
 */
class CommandController extends AbstractController
{
    /**
     * @Route("/command", name="command_index")
     * 
     */
    public function index(Request $request, SessionInterface $session)
    {

        //$this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(AddressType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('command-address', $form->getData()["address"]);
            return $this->redirectToRoute('command_payment');
        }

        return $this->render('command/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/payment", name="command_payment")
     *
     * @return void
     */
    public function payment(CartService $cartService, SessionInterface $session, Request $request)
    {

        $address = $session->get("command-address");
        if ($request->request->get('stripeToken')) {
            //création du paiement
            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey('sk_test_pdwCzoUMCe5zZVGbQljunwbD00gTaI3cbq');

            // Token is created using Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token = $request->request->get('stripeToken');
            $charge = \Stripe\Charge::create([
                'amount' => $cartService->getGrandTotal() * 100,
                'currency' => 'eur',
                'description' => 'Example charge',
                'source' => $token,
            ]);
            if ($charge->status === "succeeded") {
                return $this->redirectToRoute("command_process");
            }
        }
        return $this->render("command/payment.html.twig", [
            'total' => $cartService->getGrandTotal(),
            'address' => $address
        ]);
    }

    /**
     * @Route("/process", name="command_process")
     *
     * @param ObjectManager $manager
     * @param SessionInterface $session
     * @param CartService $cartService
     * @return void
     */
    public function process(
        ObjectManager $manager,
        SessionInterface $session,
        CartService $cartService,
        ProductRepository $repo,
        Security $security
    ) {
        $commande = new Command();
        $commande
            ->setCreatedAt(new DateTime())
            ->setAddress($session->get('command-address'))
            ->setUser($security->getUser());
        $manager->persist($commande);

        foreach ($cartService->getItems() as $item) {

            $product = $repo->find($item->getProduct()->getId());
            $commandProduct = new CommandProduct();

            $commandProduct->setProduct($product)
                ->setQuantity($item->getQuantity())
                ->setCommand($commande);
            $manager->persist($commandProduct);
        }

        $manager->flush();

        $this->addFlash("success", "Vous serez livré à " . $commande->getAddress());
        $cartService->empty();

        return $this->render("command/success.html.twig");
    }
}
