<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Entity\Meeting;

interface MeetingInterface
{
    public function getMeeting(int $id): Meeting;
    public function getMeetings(array $params): array;
    public function addMeeting(array $params): bool;
    public function updateMeeting(int $id, array $data): Meeting;
    public function deleteMeeting(int $id): bool;
}
