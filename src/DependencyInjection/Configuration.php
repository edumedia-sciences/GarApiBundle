<?php

namespace eduMedia\GarApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('edumedia_gar_api');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('distributor_id')
                    ->info('XML <idDistributeurCom> value.')
                    ->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('ssl_cert')
                    ->info('Path to the SSL certificate file (probably a .pem file).')
                    ->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('ssl_key')
                    ->info('Path to the SSL key file (probably a .key file).')
                    ->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('remote_env')
                    ->info('Remote GAR environment.')
                    ->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('cache_directory')
                    ->info('Directory where GAR data is cached.')
                    ->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('report_ssl_cert')
                    ->info('Path to the report SSL certificate file (probably a .pem file).')
                    ->cannotBeEmpty()->end()
                ->scalarNode('report_ssl_key')
                    ->info('Path to the report SSL key file (probably a .key file).')
                    ->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}