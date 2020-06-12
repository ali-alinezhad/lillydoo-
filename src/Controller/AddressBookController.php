<?php

namespace App\Controller;

use App\Entity\AddressBook;
use App\Form\AddressBookType;
use App\Repository\AddressBookRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AddressBookController extends Controller
{
    /**
     * @Route("/show/address", name="show.address")
     */
    public function index(AddressBookRepository $repository)
    {
        $addresses = $repository->findAll();

        return $this->render('address_book/index.html.twig', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * @Route("/create/address", name="create.address")
     */
    public function create(Request $request, FileUploader $fileUploader)
    {
        $address = new AddressBook();

        $form = $this->createForm(AddressBookType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $picture */
            $picture = $form->get('picture')->getData();

            if ($picture) {
                $fileName = $fileUploader->upload($picture);
                $address->setPicture($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            $this->addFlash('success', 'Record successfully created!');
            return $this->redirectToRoute('show.address');
        }

        return $this->render('address_book/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/address/{id}", name="edit.address")
     */
    public function update($id, Request $request,AddressBookRepository $repository, FileUploader $fileUploader)
    {
        $address = $repository->find($id);

        if (empty($address)) {
            $this->addFlash('errors', 'Record does not found!');
            return $this->redirectToRoute('show.address');
        }

        $form    = $this->createForm(AddressBookType::class, $address);
        $oldFile = $address->getPicture();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $picture */
            $picture = $form->get('picture')->getData();

            if ($picture) {
                if ($oldFile) {
                    $fileUploader->remove($oldFile);
                }

                $fileName = $fileUploader->upload($picture);
                $address->setPicture($fileName);
            } else {
                $address->setPicture($oldFile);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            $this->addFlash('success', 'Record successfully updated!');
            return $this->redirectToRoute('show.address');
        }

        return $this->render('address_book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/address/{id}", name="delete.address")
     */
    public function delete($id, AddressBookRepository $repository, FileUploader $fileUploader)
    {
        $address = $repository->find($id);

        if (!empty($address)) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($address);
            $entityManager->flush();
            $picture       = $address->getPicture();

            if (!empty($picture)) {
                $fileUploader->remove($picture);
            }
            $this->addFlash('success', 'Record successfully removed!');

            return $this->redirectToRoute('show.address');
        }

        $this->addFlash('errors', 'Something went wrong when removed the record!');

        return $this->redirectToRoute('show.address');
    }
}
