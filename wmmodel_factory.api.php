<?php

function hook_wmmodel_factory_factory_info_alter(array &$definitions)
{
    $definitions['node.page']['class'] = \Drupal\my_module\Entity\ModelFactory\Factory\Node\PageFactory::class;
}

function hook_wmmodel_factory_state_info_alter(array &$definitions)
{
    $definitions['node.published']['class'] = \Drupal\my_module\Entity\ModelFactory\State\Node\PublishedState::class;
}
