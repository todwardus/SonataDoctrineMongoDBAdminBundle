<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrineMongoDBAdminBundle\Guesser;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

class TypeGuesser extends AbstractTypeGuesser
{
    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property, ModelManagerInterface $modelManager)
    {
        if (!$ret = $this->getParentMetadataForProperty($class, $property, $modelManager)) {
            return new TypeGuess('text', [], Guess::LOW_CONFIDENCE);
        }

        list($metadata, $propertyName, $parentAssociationMappings) = $ret;

        if ($metadata->hasAssociation($propertyName)) {
            $mapping = $metadata->fieldMappings[$propertyName];

            switch ($mapping['type']) {
                case ClassMetadataInfo::ONE:
                    return new TypeGuess('mongo_one', [], Guess::HIGH_CONFIDENCE);

                case ClassMetadataInfo::MANY:
                    return new TypeGuess('mongo_many', [], Guess::HIGH_CONFIDENCE);
            }
        }

        switch ($metadata->getTypeOfField($propertyName)) {
            case 'collection':
            case 'hash':
            case 'array':
              return new TypeGuess('array', [], Guess::HIGH_CONFIDENCE);
            case 'boolean':
                return new TypeGuess('boolean', [], Guess::HIGH_CONFIDENCE);
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
            case 'timestamp':
                return new TypeGuess('datetime', [], Guess::HIGH_CONFIDENCE);
            case 'date':
                return new TypeGuess('date', [], Guess::HIGH_CONFIDENCE);
            case 'decimal':
            case 'float':
                return new TypeGuess('number', [], Guess::MEDIUM_CONFIDENCE);
            case 'integer':
            case 'bigint':
            case 'smallint':
                return new TypeGuess('integer', [], Guess::MEDIUM_CONFIDENCE);
            case 'string':
                return new TypeGuess('text', [], Guess::MEDIUM_CONFIDENCE);
            case 'text':
                return new TypeGuess('textarea', [], Guess::MEDIUM_CONFIDENCE);
            case 'time':
                return new TypeGuess('time', [], Guess::HIGH_CONFIDENCE);
            default:
                return new TypeGuess('text', [], Guess::LOW_CONFIDENCE);
        }
    }
}
