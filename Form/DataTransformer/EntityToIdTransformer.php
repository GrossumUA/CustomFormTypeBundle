<?php

namespace Grossum\ExtendedFormTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\InvalidArgumentException;

use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;

class EntityToIdTransformer implements DataTransformerInterface
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $class;

    /** @var UnitOfWork */
    protected $unitOfWork;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->class = $class;
    }

    /**
     * @param Object $entity
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity || '' === $entity) {
            return 'null';
        }
        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }
        if (!$this->unitOfWork->isInIdentityMap($entity)) {
            throw new InvalidArgumentException('Entities passed to the choice field must be managed');
        }

        return $entity->getId();
    }

    /**
     * @param int $id
     * @return null|object
     */
    public function reverseTransform($id)
    {
        if ('' === $id || null === $id) {
            return null;
        }

        if (!is_numeric($id)) {
            throw new UnexpectedTypeException($id, 'numeric' . $id);
        }

        $entity = $this->em->getRepository($this->class)->find($id);

        if ($entity === null) {
            throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $id));
        }

        return $entity;
    }
}
