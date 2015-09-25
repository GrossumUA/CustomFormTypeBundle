<?php

namespace Grossum\ExtendedFormTypeBundle\Controller;

use Grossum\ExtendedFormTypeBundle\Services\DependentEntityLoader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DependentFilteredEntityController
 * @package Grossum\ExtendedFormTypeBundle\Controller
 */
class DependentFilteredEntityController extends Controller
{
    public function getOptionsAction(Request $request)
    {
        $entities = $this->container->getParameter('grossum.dependent_filtered_entities');
        $entityInf = $entities[$request->get('entity_alias')];

        if ($entityInf['role'] !== AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY) {
            if (false === $this->get('security.authorization_checker')->isGranted($entityInf['role'])) {
                throw new AccessDeniedException();
            }
        }

        /** @var DependentEntityLoader $dependentEntityLoader */
        $dependentEntityLoader = $this->container->get('grossum_extended_dependent_entity_loader');
        $results = $dependentEntityLoader->getEntities(
            $entityInf,
            $request->get('parent_id')
        );

        return $this->render(
            'GrossumExtendedFormTypeBundle:blocks:options_block.html.twig',
            [
                'results'     => $results,
                'emptyValue'  => $request->get('empty_value'),
                'property'    => $entityInf['property'],
                'noResultMsg' => $entityInf['no_result_msg']
            ]
        );
    }
}
