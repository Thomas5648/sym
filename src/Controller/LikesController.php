<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Jednostka;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/likes")
 */
class LikesController extends Controller
{
    /**
     * @Route("/like/{id}", name="likes_like")
     */
    public function like(Jednostka $jednostka)
    {
        /**@var User $curentUser */
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $jednostka->like($curentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $jednostka->getLikedBy()->count()
        ]);
    }
    /**
     * @Route("/unlike/{id}", name="likes_unlike")
     */
    public function unlike(Jednostka $jednostka)
    {
        /**@var User $curentUser */
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $jednostka->getlikeBy()->removeElement($curentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $jednostka->getLikedBy()->count()
        ]);
    }
}