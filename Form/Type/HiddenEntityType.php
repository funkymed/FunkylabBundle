<?php

namespace Tigreboite\FunkylabBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HiddenEntityType.
 *
 * Render an entity as a hidden field using the identifier field "id"
 * transformed using the Entity to Int transformer
 */
class HiddenEntityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Add the data transformer to the field setting the entity repository.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityTransformer = new $options['transformer']($this->om);
        $entityTransformer->setEntityRepository($options['class']);
        $builder->addModelTransformer($entityTransformer);
    }

    /**
     * Require the entity repository option to be set on the field.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'transformer' => 'Tigreboite\FunkylabBundle\Form\DataTransformer\EntityToIdentifierTransformer',
        ));
        $resolver->setRequired(
            array(
                'class',
            )
        );
    }

    /**
     * Require the entity repository option to be set on the field.
     *
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getBlockPrefix()
    {
        return 'hidden_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\HiddenType';
    }
}
