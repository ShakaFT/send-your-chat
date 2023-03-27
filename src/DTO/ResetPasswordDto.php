<?php

namespace App\DTO;

use App\Entity\AbstractEntity;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordDto extends AbstractDto
{
    #[Assert\NotBlank]
    public ?string $newPassword = null;
    #[Assert\NotBlank]
    public ?string $confirmPassword = null;

    /**
     * @param User $user
     */
    public function setFromEntity(AbstractEntity $user): void
    {
        $this->newPassword = $user->getPassword();
    }
}
