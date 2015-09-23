<?php

namespace Grossum\ExtendedFormTypeBundle\Form\Type;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Grossum\ExtendedFormTypeBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DependentFilteredEntityType
 * @package Grossum\ExtendedFormTypeBundle\Form\Type
 */
class DependentFilteredEntityType extends AbstractType
{
    /** @var Registry $doctrine */
    private $doctrine;

    private $entities;

    /**
     * @param Registry $doctrine
     * @param array $entities
     */
    public function __construct(Registry $doctrine, array $entities)
    {
        $this->doctrine = $doctrine;
        $this->entities = $entities;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'empty_value'       => '',
            'entity_alias'      => null,
            'parent_field'      => null,
            'compound'          => false
        ));
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

        $builder->setAttribute("parent_field", $options['parent_field']);
        $builder->setAttribute("entity_alias", $options['entity_alias']);
        $builder->setAttribute("no_result_msg", $options['no_result_msg']);
        $builder->setAttribute("empty_value", $options['empty_value']);

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
        return 'form';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'grossum_dependent_filtered_entity';
    }
}