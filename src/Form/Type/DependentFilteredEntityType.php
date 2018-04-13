<?php

namespace Grossum\ExtendedFormTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

use Grossum\ExtendedFormTypeBundle\Form\DataTransformer\EntityToIdTransformer;

class DependentFilteredEntityType extends AbstractType
{
    /** @var Registry */
    private $doctrine;

    /** @var array */
    private $entities;

    /**
     * @param Registry $doctrine
     * @param array    $entities
     */
    public function __construct(Registry $doctrine, array $entities)
    {
        $this->doctrine = $doctrine;
        $this->entities = $entities;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'empty_value' => '',
                'entity_alias' => null,
                'parent_field' => null,
                'compound' => false
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entities = $this->entities;
        $options['class'] = $entities[$options['entity_alias']]['class'];
        $options['property'] = $entities[$options['entity_alias']]['property'];
        $options['no_result_msg'] = $entities[$options['entity_alias']]['no_result_msg'];

        /** @var EntityManager $manager */
        $manager = $this->doctrine->getManager();

        $builder->addViewTransformer(
            new EntityToIdTransformer(
                $manager,
                $options['class']
            ),
            true
        );

        $builder->setAttribute('parent_field', $options['parent_field']);
        $builder->setAttribute('entity_alias', $options['entity_alias']);
        $builder->setAttribute('no_result_msg', $options['no_result_msg']);
        $builder->setAttribute('empty_value', $options['empty_value']);

    }

    /**
     * {@InheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['parent_field'] = $form->getConfig()->getAttribute('parent_field');
        $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
        $view->vars['no_result_msg'] = $form->getConfig()->getAttribute('no_result_msg');
        $view->vars['empty_value'] = $form->getConfig()->getAttribute('empty_value');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return FormType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'grossum_dependent_filtered_entity';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
