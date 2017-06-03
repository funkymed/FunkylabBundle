<?php
/**
 * Code by Cyril Pereira, Julien Hay
 * Extreme-Sensio 2015.
 */

namespace Tigreboite\FunkylabBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tigreboite\FunkylabBundle\Entity\Actuality;

class ActualityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $actuality = new Actuality();

        $builder->add('title');
        $builder->add('summary');
        $builder->add('metaTitle');
        $builder->add('metaSummary');
        $builder->add('metaKeywords');
        $builder->add('tags');
        $builder->add('published', ChoiceType::class, [
          'choices' => [
              'Non',
              'Oui',
          ],
          'expanded' => false,
          'multiple' => false,
        ]);
        $builder->add('mea', ChoiceType::class, [
          'choices' => [
              'Non',
              'Oui',
          ],
          'expanded' => false,
          'multiple' => false,
        ]);
        $builder->add('dateStart', DateType::class, array(
          'required' => false,
          'widget' => 'single_text',
          'input' => 'datetime',
          'format' => 'dd/MM/yyyy', )
        );
        $builder->add('dateEnd', DateType::class, array(
          'required' => false,
          'widget' => 'single_text',
          'input' => 'datetime',
          'format' => 'dd/MM/yyyy', )
        );
        $builder->add('cta');
        $builder->add('image', HiddenType::class);

        $builder->add('category', ChoiceType::class, [
          'choices' => $actuality->categories,
          'expanded' => false,
          'multiple' => false,
        ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'data_class' => 'Tigreboite\FunkylabBundle\Entity\Actuality',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tigreboite_funkylabbundle_actuality';
    }
}