<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Navigation\Navigation;
use Sulu\Bundle\AdminBundle\Navigation\NavigationItem;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Localization\Manager\LocalizationManagerInterface;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class CategoryAdmin extends Admin
{
    const SECURITY_CONTEXT = 'sulu.settings.categories';

    const LIST_ROUTE = 'sulu_category.list';

    const ADD_FORM_ROUTE = 'sulu_category.add_form';

    const EDIT_FORM_ROUTE = 'sulu_category.edit_form';

    /**
     * @var RouteBuilderFactoryInterface
     */
    private $routeBuilderFactory;

    /**
     * @var SecurityCheckerInterface
     */
    private $securityChecker;

    /**
     * @var LocalizationManagerInterface
     */
    private $localizationManager;

    public function __construct(
        RouteBuilderFactoryInterface $routeBuilderFactory,
        SecurityCheckerInterface $securityChecker,
        LocalizationManagerInterface $localizationManager
    ) {
        $this->routeBuilderFactory = $routeBuilderFactory;
        $this->securityChecker = $securityChecker;
        $this->localizationManager = $localizationManager;
    }

    public function getNavigation(): Navigation
    {
        $rootNavigationItem = $this->getNavigationItemRoot();
        $settings = $this->getNavigationItemSettings();

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $categoryItem = new NavigationItem('sulu_category.categories', $settings);
            $categoryItem->setPosition(20);
            $categoryItem->setMainRoute(static::LIST_ROUTE);
        }

        if ($settings->hasChildren()) {
            $rootNavigationItem->addChild($settings);
        }

        return new Navigation($rootNavigationItem);
    }

    public function getRoutes(): array
    {
        $locales = array_values(
            array_map(
                function(Localization $localization) {
                    return $localization->getLocale();
                },
                $this->localizationManager->getLocalizations()
            )
        );

        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = 'sulu_admin.add';
        }

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = 'sulu_admin.save';
            $listToolbarActions[] = 'sulu_admin.move';
        }

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = 'sulu_admin.delete';
            $listToolbarActions[] = 'sulu_admin.delete';
        }

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = 'sulu_admin.export';
        }

        $routes = [];

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $routes[] = $this->routeBuilderFactory
                ->createListRouteBuilder(static::LIST_ROUTE, '/categories/:locale')
                ->setResourceKey('categories')
                ->setListKey('categories')
                ->setTitle('sulu_category.categories')
                ->addListAdapters(['tree_table'])
                ->addLocales($locales)
                ->setDefaultLocale($locales[0])
                ->setAddRoute(static::ADD_FORM_ROUTE)
                ->setEditRoute(static::EDIT_FORM_ROUTE)
                ->enableSearching()
                ->addToolbarActions($listToolbarActions)
                ->getRoute();
            $routes[] = $this->routeBuilderFactory
                ->createResourceTabRouteBuilder(static::ADD_FORM_ROUTE, '/categories/:locale/add')
                ->setResourceKey('categories')
                ->addLocales($locales)
                ->setBackRoute(static::LIST_ROUTE)
                ->getRoute();
            $routes[] = $this->routeBuilderFactory
                ->createFormRouteBuilder('sulu_category.add_form.details', '/details')
                ->setResourceKey('categories')
                ->setFormKey('category_details')
                ->setTabTitle('sulu_admin.details')
                ->addToolbarActions($formToolbarActions)
                ->addRouterAttributesToFormStore(['parentId'])
                ->setEditRoute(static::EDIT_FORM_ROUTE)
                ->setParent(static::ADD_FORM_ROUTE)
                ->getRoute();
            $routes[] = $this->routeBuilderFactory
                ->createResourceTabRouteBuilder(static::EDIT_FORM_ROUTE, '/categories/:locale/:id')
                ->setResourceKey('categories')
                ->addLocales($locales)
                ->setBackRoute(static::LIST_ROUTE)
                ->addRouterAttributesToBackRoute(['id' => 'active'])
                ->setTitleProperty('name')
                ->getRoute();
            $routes[] = $this->routeBuilderFactory
                ->createFormRouteBuilder('sulu_category.edit_form.details', '/details')
                ->setResourceKey('categories')
                ->setFormKey('category_details')
                ->setTabTitle('sulu_admin.details')
                ->addToolbarActions($formToolbarActions)
                ->setParent(static::EDIT_FORM_ROUTE)
                ->getRoute();
            $routes[] = $this->routeBuilderFactory
                ->createFormOverlayListRouteBuilder('sulu_category.edit_form.keywords', '/keywords')
                ->setResourceKey('category_keywords')
                ->setListKey('category_keywords')
                ->addListAdapters(['table'])
                ->addRouterAttributesToListStore(['id' => 'categoryId'])
                ->setFormKey('category_keywords')
                ->addRouterAttributesToFormStore(['id' => 'categoryId'])
                ->setTabTitle('sulu_category.keywords')
                ->addToolbarActions(['sulu_admin.add', 'sulu_admin.delete'])
                ->setParent(static::EDIT_FORM_ROUTE)
                ->getRoute();
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityContexts()
    {
        return [
            'Sulu' => [
                'Settings' => [
                    static::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
