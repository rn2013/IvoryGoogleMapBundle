<?php

/*
 * This file is part of the Ivory Google Map bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\Form\Type;

use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Place\Autocomplete;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PlaceAutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $autocomplete = new Autocomplete();

        if ($options['variable'] !== null) {
            $autocomplete->setVariable($options['variable']);
        }

        if (!empty($options['components'])) {
            $autocomplete->setComponents($options['components']);
        }

        if ($options['bound'] !== null) {
            $autocomplete->setBound($options['bound']);
        }

        $autocomplete->getEventManager()->setDomEvents($options['dom_events']);
        $autocomplete->getEventManager()->setDomEventsOnce($options['dom_events_once']);
        $autocomplete->getEventManager()->setEvents($options['events']);
        $autocomplete->getEventManager()->setEventsOnce($options['events_once']);

        if (!empty($options['types'])) {
            $autocomplete->setTypes($options['types']);
        }

        if (!empty($options['libraries'])) {
            $autocomplete->setLibraries($options['libraries']);
        }

        $builder->setAttribute('autocomplete', $autocomplete);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $autocomplete = $form->getConfig()->getAttribute('autocomplete');
        $autocomplete->setInputId($view->vars['id']);
        $autocomplete->setValue(!empty($view->vars['value']) ? $view->vars['value'] : null);
        $autocomplete->setInputAttribute('name', $view->vars['full_name']);

        $view->vars['api'] = $options['api'];
        $view->vars['autocomplete'] = $autocomplete;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'variable'        => null,
            'components'      => [],
            'bound'           => null,
            'dom_events'      => [],
            'dom_events_once' => [],
            'events'          => [],
            'events_once'     => [],
            'types'           => [],
            'libraries'       => [],
            'api'             => true,
        ]);

        $allowedTypesMap = [
            'variable'        => ['string', 'null'],
            'bound'           => [Bound::class, 'null'],
            'components'      => 'array',
            'dom_events'      => 'array',
            'dom_events_once' => 'array',
            'events'          => 'array',
            'events_once'     => 'array',
            'types'           => 'array',
            'libraries'       => 'array',
            'api'             => 'bool',
        ];

        if (Kernel::VERSION_ID >= 20600) {
            foreach ($allowedTypesMap as $option => $allowedTypes) {
                $resolver->addAllowedTypes($option, $allowedTypes);
            }
        } else {
            $resolver->addAllowedTypes($allowedTypesMap);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        if (method_exists(AbstractType::class, 'getBlockPrefix')) {
            return TextType::class;
        }

        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'place_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
