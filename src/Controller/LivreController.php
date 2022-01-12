<?php

namespace App\Controller;

use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/livre/{livre}', name: 'get_livre', methods: ['GET'])]
    public function get($livre): Response
    {
        $livreObject = $this->entityManager->getRepository(Livre::class)->findOneBy(['id'=>$livre]);

        if($livreObject===null){
            throw $this->createNotFoundException(sprintf(
                'Pas de livre trouvé "%s"',
                $livre
            ));
        }

        return $this->json($livreObject, Response::HTTP_OK);
    }

    #[Route('/api/livres', name: 'get_livres', methods: ['GET'])]
    public function getAll(): Response
    {
        $livres = $this->entityManager->getRepository(Livre::class)->findAll();

        $data = [];
        foreach($livres as $livre){
            $data[] = $livre;
        }

        return $this->json($data,Response::HTTP_OK);
    }

    #[Route('/api/livre', name: 'add_livre', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if(empty($data['titre']) || empty($data['auteur']) || empty($data['nbPage'])){
            return $this->json(['message'=>'Tous les champs doivent être renseignés'],Response::HTTP_BAD_REQUEST);
        }
        $titre = $data['titre'];
        $auteur = $data['auteur'];
        $nbPage = $data['nbPage'];

        $newLivre = new Livre();
        $newLivre->setTitre($titre)->setAuteur($auteur)->setNbPage($nbPage);

        $this->entityManager->persist($newLivre);
        $this->entityManager->flush();

        return $this->json($newLivre,Response::HTTP_CREATED);
    }

    #[Route('/api/livre/{livre}', name: 'put_patch_livre', methods: ['PUT','PATCH'])]
    public function putPatch(Request $request, $livre): Response
    {
        $data = json_decode($request->getContent(), true);

        $livreObject = $this->entityManager->getRepository(Livre::class)->findOneBy(['id'=>$livre]);

        if($livreObject===null){
            throw $this->createNotFoundException(sprintf(
                'Pas de livre trouvé "%s"',
                $livre
            ));
        }

        if($request->getMethod()==='PUT' and (empty($data['titre']) || empty($data['auteur']) || empty($data['nbPage']))){
            return $this->json(['message'=>'Tous les champs doivent être renseignés'],Response::HTTP_BAD_REQUEST);
        }

        if(!empty($data['titre'])) $livreObject->setTitre($data['titre']);
        if(!empty($data['auteur'])) $livreObject->setAuteur($data['auteur']);
        if(!empty($data['nbPage'])) $livreObject->setNbPage($data['nbPage']);

        $this->entityManager->flush();

        return $this->json($livreObject,Response::HTTP_OK);
    }

    #[Route('/api/livre/{livre}', name: 'delete_livre', methods: ['DELETE'])]
    public function delete(Request $request, $livre): Response
    {
        $livreObject = $this->entityManager->getRepository(Livre::class)->findOneBy(['id'=>$livre]);

        if($livreObject===null){
            throw $this->createNotFoundException(sprintf(
                'Pas de livre trouvé "%s"',
                $livre
            ));
        }

        $this->entityManager->remove($livreObject);
        $this->entityManager->flush();

        return $this->json(null,Response::HTTP_NO_CONTENT);
    }

}
