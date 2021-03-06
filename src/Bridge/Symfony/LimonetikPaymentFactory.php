<?php

/*
 * This file is part of the PayumLimonetik package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\PayumLimonetik\Bridge\Symfony;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class LimonetikPaymentFactory extends AbstractPaymentFactory
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'limonetik';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder
            ->children()
                ->scalarNode('merchant_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('sandbox')->defaultTrue()->end()
            ->end()
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function addApis(Definition $paymentDefinition, ContainerBuilder $container, $contextName, array $config)
    {
        $apiDefinition = new DefinitionDecorator('payum.limonetik.api.prototype');
        $apiDefinition->replaceArgument(0, array(
            'merchantId' => $config['merchant_id'],
            'key'        => $config['key'],
            'sandbox'    => $config['sandbox'],
        ));
        $apiDefinition->setPublic(true);

        $apiId = 'payum.context.'.$contextName.'.api';
        $container->setDefinition($apiId, $apiDefinition);

        $paymentDefinition->addMethodCall('addApi', array(new Reference($apiId)));
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayumPaymentFactoryClass()
    {
        return 'winzou\PayumLimonetik\LimonetikPaymentFactory';
    }

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackage()
    {
        return 'winzou/payum-limonetik';
    }
}
