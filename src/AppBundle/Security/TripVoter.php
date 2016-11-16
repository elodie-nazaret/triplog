<?php

namespace AppBundle\Security;

use AppBundle\Entity\Trip;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class TripVoter
 */
class TripVoter extends Voter
{
    const EDIT = 'edit';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        if (!$subject instanceof Trip) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Trip object, thanks to supports
        /** @var Trip $trip */
        $trip = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($trip, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Trip $trip
     * @param User $user
     *
     * @return bool
     */
    private function canEdit(Trip $trip, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $trip->getUser();
    }
}