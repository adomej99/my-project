<?php

namespace App\Controller;

use App\Entity\BookHistory;
use App\Entity\BookRequest;
use App\Entity\ReturnReport;
use App\Entity\User;
use App\Entity\UserReview;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/users/{id}', name: 'get_user')]
    public function getUserInfo(User $user): JsonResponse
    {
        $reviews = $this->entityManager->getRepository(UserReview::class)->findBy(['user' => $user->getId()]);

        $reviewsData = [];

        foreach ($reviews as $review)
        {
            $reviewsData[]= [
                'rating' => $review->getRating(),
                'review' => $review->getReview(),
                'reviewedBy' => $review->getUser()->getUsername()
                ];
        }

        $data= [
            'username' => $user->getUsername(),
            'rating' =>$user->getRating(),
            'email' => $user->getEmail(),
            'number' => $user->getNumber(),
            'region' => $user->getRegion(),
            'municipality' => $user->getCity(),
            'other' => $user->getOtherContacts(),
            'review' => $reviewsData
            ];

        return new JsonResponse($data);
    }

    #[Route('/user/role', name: 'get_role')]
    public function getUserRole(): JsonResponse
    {
        return new JsonResponse($this->getUser()->getRoles());
    }

    #[Route('/users', name: 'get_users')]
    public function getUsers(): JsonResponse
    {
        // Todo: check if role admin

        $data = [];

        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user)
        {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'active' => $user->getIsActive()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/users/{id}/toggle-status', name: 'set_user_status')]
    public function setUserStatus(User $user): JsonResponse
    {
        if($user->getIsActive() == 1)
            $user->setIsActive(0);
        else
            $user->setIsActive(1);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/users/report', name: 'report_person')]
    public function reportPerson(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Todo: user session
        $reportedBy = $this->entityManager->getRepository(User::class)->find((int)$data['reportedBy']);
        $reportedPerson = $this->entityManager->getRepository(User::class)->find((int)$data['reportedPerson']);
        $bookRequest = $this->entityManager->getRepository(BookRequest::class)->find((int)$data['requestId']);

        $report = new ReturnReport();

        $report->setUser($reportedBy);
        $report->setReturnedBy($reportedPerson);
        $report->setRequest($bookRequest);
        $report->setReport($data['report']);
        $report->setDate(date("Y-m-d h:i:sa"));

        $bookRequest->getBook()->setAvailable(0);

        $bookHistory = new BookHistory();

        $bookHistory->setBook($bookRequest->getBook());
        $bookHistory->setDateCreated(date("Y-m-d h:i:sa"));
        // Todo: Unavailable due to report
        $bookHistory->setAction(7);
        $bookHistory->setIsRequest(false);
        // Todo: UserSession
        $bookHistory->setPerformedBy($this->entityManager->getRepository(User::class)->find(10));

        $this->entityManager->persist($report);
        $this->entityManager->persist($bookHistory);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'OK'], Response::HTTP_OK);
    }

    #[Route('/reports', name: 'get_reports')]
    public function getAllReports(): JsonResponse
    {
        $data = [];

        $reports = $this->entityManager->getRepository(ReturnReport::class)->findAll();

        foreach ($reports as $report) {
            $data[] = [
                'reportId' => $report->getId(),
                'user' => $report->getUser()->getUsername(),
                'userId' => $report->getUser()->getId(),
                'returnedBy' => $report->getReturnedBy()->getUsername(),
                'returnedById' => $report->getReturnedBy()->getId(),
                'report' => $report->getReport(),
                'date' => $report->getDate()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/reports/{id}', name: 'get_report')]
    public function getReports(User $user): JsonResponse
    {
        $data = [];

        $reports = $this->entityManager->getRepository(ReturnReport::class)->findBy(['returnedBy' => $user->getId()]);

        foreach ($reports as $report) {
            $data[] = [
                'user' => $report->getUser()->getUsername(),
                'userId' => $report->getUser()->getId(),
                'returnedBy' => $report->getReturnedBy()->getUsername(),
                'returnedById' => $report->getReturnedBy()->getId(),
                'report' => $report->getReport(),
                'date' => $report->getDate()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/reports/{id}/accept', name: 'accept_report')]
    public function acceptReport(ReturnReport $report): JsonResponse
    {
        $report->getUser()->setIsActive(0);
        $this->entityManager->remove($report);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'OK'], Response::HTTP_OK);
    }

    #[Route('/reports/{id}/decline', name: 'decline_report')]
    public function declineReport(ReturnReport $report): JsonResponse
    {
        $this->entityManager->remove($report);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'OK'], Response::HTTP_OK);
    }
}
