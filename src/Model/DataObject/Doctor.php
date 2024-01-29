<?php

// src/Model/DataObject/Doctor.php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\ClassDefinition\Data\Selection;
use Pimcore\Model\DataObject\ClassDefinition\Data\Password;
use Pimcore\Model\DataObject\Doctor as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Custom user class for doctors, implementing Symfony's UserInterface.
 */
class Doctor extends BaseUser implements UserInterface
{
    /**
     * Trigger the hash calculation to remove the plain text password from the instance.
     *
     * This is necessary to make sure no plain text passwords are serialized.
     * @throws \Exception
     */
    public function eraseCredentials(): void
    {
        /** @var Password $field */
        $field = $this->getClass()->getFieldDefinition('password');
        $field->getDataForResource($this->getPassword(), $this);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        return 'username';
    }
}
