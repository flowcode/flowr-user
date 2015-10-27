<?php

namespace Flower\CoreBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Flower\ModelBundle\Entity\User\Invitation;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Description of InvitationToCodeTransformer
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'Flower\ModelBundle\Entity\User\Invitation');
        }

        return $value->getCode();
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $dql = <<<DQL
SELECT i
FROM FlowerModelBundle:User\Invitation i
WHERE i.code = :code
AND NOT EXISTS(SELECT 1 FROM FlowerModelBundle:User\User u WHERE u.invitation = i)
DQL;

        return $this->entityManager
                        ->createQuery($dql)
                        ->setParameter('code', $value)
                        ->setMaxResults(1)
                        ->getOneOrNullResult();
    }

}
