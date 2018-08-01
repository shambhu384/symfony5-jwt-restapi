<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeetingRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;
use App\Services\Interfaces\MeetingInterface;
use App\Entity\Meeting;

/**
 * Class MeetingService
 *
 * @package App\Serivces
 */
class MeetingService implements MeetingInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MeetingRepository
     */
    private $meetingRepository;

    /**
     * @var User|null
     */
    private $user;

    /**
     * MeetingService constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param MeetingRepository $repository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EntityManagerInterface $entityManager, MeetingRepository $repository, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->meetingRepository = $repository;
///        $this->user = $entityManager->getRepository(User::class)->findOneBy([
//            'username' => $tokenStorage->getToken()->getUser()->getUsername()
//        ]);
    }

    /**
     * @return Meeting
     */
    public function getMeeting(int $id): Meeting
    {
        return new Meeting();
    }

    /**
     * @return Meeting[]
     */
    public function getMeetings(array $params): array
    {
        return $this->meetingRepository->findAll();
    }

    /**
     *
     */
    public function addMeeting(array $data): bool
    {
    }

    /**
     *
     */
    public function updateMeeting(int $id, array $data): Meeting
    {
    }

    /**
     * @param int $id
     */
    public function deleteMeeting(int $id): bool
    {
        return false;
    }
}
