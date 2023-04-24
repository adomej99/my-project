<?php

namespace App\Controller;

use App\Entity\BookHistory;
use App\Entity\BookRequest;
use App\Entity\ReturnReport;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users/{id}', name: 'get_user')]
    public function getUserInfo(User $user): JsonResponse
    {
        $data= [
            'username' => $user->getUsername(),
            'rating' =>$user->getRating(),
            'email' => $user->getEmail(),
            'number' => $user->getNumber(),
            'region' => $user->getRegion(),
            'municipality' => $user->getCity(),
            'other' => $user->getOtherContacts()
            ];

        return new JsonResponse($data);
    }

    #[Route('/users', name: 'get_users')]
    public function getUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        // Todo: check if role admin

        $data = [];

        $users = $entityManager->getRepository(User::class)->findAll();

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
    public function setUserStatus(EntityManagerInterface $entityManager, User $user): JsonResponse
    {
        if($user->getIsActive() == 1)
            $user->setIsActive(0);
        else
            $user->setIsActive(1);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Book request accepted.'], Response::HTTP_OK);
    }

    #[Route('/users/report', name: 'report_person')]
    public function reportPerson(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Todo: user session
        $reportedBy = $entityManager->getRepository(User::class)->find((int)$data['reportedBy']);
        $reportedPerson = $entityManager->getRepository(User::class)->find((int)$data['reportedPerson']);
        $bookRequest = $entityManager->getRepository(BookRequest::class)->find((int)$data['requestId']);

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
        $bookHistory->setPerformedBy($entityManager->getRepository(User::class)->find(10));

        $entityManager->persist($report);
        $entityManager->persist($bookHistory);
        $entityManager->flush();

        return new JsonResponse(['message' => 'OK'], Response::HTTP_OK);
    }

    #[Route('/reports', name: 'get_reports')]
    public function getAllReports(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = [];

        $reports = $entityManager->getRepository(ReturnReport::class)->findAll();

        foreach ($reports as $report) {
            $data[] = [
                'reportId' => $report->getId(),
                'user' => $report->getUser()->getUsername(),
                'userId' => $report->getUser()->getId(),
                'returnedBy' => $report->getUser()->getUsername(),
                'returnedById' => $report->getUser()->getId(),
                'report' => $report->getReport(),
                'date' => $report->getDate()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/reports/{id}', name: 'get_report')]
    public function getReports(EntityManagerInterface $entityManager, User $user): JsonResponse
    {
        $data = [];

        $reports = $entityManager->getRepository(ReturnReport::class)->findBy(['returnedBy' => $user->getId()]);

        foreach ($reports as $report) {
            $data[] = [
                'user' => $report->getUser()->getUsername(),
                'userId' => $report->getUser()->getId(),
                'returnedBy' => $report->getUser()->getUsername(),
                'returnedById' => $report->getUser()->getId(),
                'report' => $report->getReport(),
                'date' => $report->getDate()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/reports/{id}/accept', name: 'accept_report')]
    public function acceptReport(EntityManagerInterface $entityManager, ReturnReport $report): JsonResponse
    {
        $report->getUser()->setIsActive(0);
        $entityManager->remove($report);
        $entityManager->flush();

        return new JsonResponse(['message' => 'OK'], Response::HTTP_OK);
    }

    #[Route('/reports/{id}/decline', name: 'decline_report')]
    public function declineReport(EntityManagerInterface $entityManager, ReturnReport $report): JsonResponse
    {
        $entityManager->remove($report);
        $entityManager->flush();

        return new JsonResponse(['message' => 'OK'], Response::HTTP_OK);
    }
}