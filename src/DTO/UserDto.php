<?php

namespace App\DTO;

use App\Entity\AbstractEntity;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto extends AbstractDto
{

    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank(groups: ["add"])]
    public ?string $password = null;

    #[Assert\NotBlank(groups: ["add"])]
    public ?string $passwordConfirm = null;

    /**
     * @param User $user
     */
    public function setFromEntity(AbstractEntity $user): void
    {
        $this->username = $user->getUsername();
        $this->email    = $user->getEmail();
        $this->password    = $user->getPassword();
    }
}
