<?php

namespace Grossum\ExtendedFormTypeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('grossum_extended_form_type');

        $rootNode
            ->children()
                ->arrayNode('dependent_filtered_entities')
                ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('parent_property')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('role')
                                ->defaultValue(AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY)
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('no_result_msg')
                                ->defaultValue('No results were found')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('order_property')
                                ->defaultValue('id')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('order_direction')
                                ->defaultValue('ASC')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('property')
                                ->defaultValue(null)
                                ->cannotBeEmpty()
                            ->end()
                            ->booleanNode('property_complicated')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('case_insensitive')
                                ->defaultTrue()
                            ->end()
                            ->scalarNode('search')
                                ->defaultValue('begins_with')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('callback')
                                ->defaultValue(null)
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
