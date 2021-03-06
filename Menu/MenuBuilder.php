<?php

namespace Tigreboite\FunkylabBundle\Menu;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\PhpFileCache;
use Knp\Menu\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Tigreboite\FunkylabBundle\Annotation\Driver\MenuConverter;
use Tigreboite\FunkylabBundle\Service\FunkylabService;

class MenuBuilder
{
    private $factory;
    private $config;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(PhpFileCache $cache, FunkylabService $funkylabService, RequestStack $requestStack, Reader $reader, AuthorizationChecker $authorizationChecker, Router $router)
    {
        $request = $requestStack->getCurrentRequest();
        $menuConverter = new MenuConverter($reader, $router, $authorizationChecker, $cache);
        $this->config = $menuConverter->getFunkylabConfiguration();
        $list = $menuConverter->getControllersWithAnnotationModules();

        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array('class' => 'sidebar-menu'),
        ));

        $menuData = array();

        foreach ($list as $k => $l) {
            if (isset($l['children'])) {
                if ($l['children'][0]['route'] == 'admin_user' && !$this->config['user']) {
                    continue;
                }

                $i = $menu->addChild($k, array(
                    'route' => $l['children'][0]['route'],
                    'extras' => array('treeview' => true, 'class_icon' => 'fa ' . $l['children'][0]['menu']->getIcon()),
                    'attributes' => array('class' => 'treeview'),
                    'childrenAttributes' => array('class' => 'treeview-menu'),
                ));

                foreach ($l['children'] as $m) {
                    if ($request->get('_route') == $m['route']) {
                        $i->setCurrent(true);
                    }

                    if ($m['route'] == 'admin_user' && !$this->config['user']) {
                        continue;
                    }

                    if ($m['route'] == 'admin_actuality' && !$this->config['actuality']) {
                        continue;
                    }

                    if ($m['route'] == 'admin_page' && !$this->config['page']) {
                        continue;
                    }

                    $i->addChild($m['menu']->getPropertyName(), array(
                        'route' => $m['route'],
                        'extras' => array('class_icon' => 'fa ' . $m['menu']->getIcon()),
                    ));

                    $menuData[] = $k . "/" . $m['menu']->getPropertyName();
                }
            } else {
                $menu->addChild($l['menu']->getName(), array(
                    'route' => $l['route'],
                    'extras' => array('class_icon' => 'fa ' . $l['menu']->getIcon()),
                ));
                $menuData[] = $k . "/" . $l['menu']->getName();
            }
        }
        $funkylabService->set("menu", $menuData);
        return $menu;
    }

    public function createBreadcrumbMenu(PhpFileCache $cache, RequestStack $requestStack, Reader $reader, AuthorizationChecker $authorizationChecker, Router $router)
    {
        $request = $requestStack->getCurrentRequest();
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array('class' => 'breadcrumb'),
        ));

        $mainSection = 'Dashboard';
        $mainSectionIcon = 'fa fa-dashboard';
        $subSection = '';

        $route_root = explode('_', $request->get('_route'));
        $section = $route_root[1];
        $action = (!empty($route_root[2]) ? $route_root[2] : '');

        $menuConverter = new MenuConverter($reader, $router, $authorizationChecker, $cache);

        $list = $menuConverter->getControllersWithAnnotationModules();

        foreach ($list as $k => $l) {
            if (isset($l['children'])) {
                foreach ($l['children'] as $m) {
                    $menu_route_root = explode('_', $m['route']);
                    $menu_section = $menu_route_root[1];

                    if ($section == $menu_section) {
                        $mainSectionIcon = 'fa ' . $m['menu']->getIcon();
                        $mainSection = $m['menu']->getPropertyName();
                        $subSection = 'list';
                        $menu->addChild($k, array(
                            'route' => $m['route'],
                            'extras' => array('class_icon' => 'fa ' . $l['children'][0]['menu']->getIcon()),
                        ));

                        if ($request->get('_route') == $m['route']) {
                            $menu->addChild($mainSection, array(
                                'route' => $m['route'],
                                'extras' => array('class_icon' => 'fa ' . $m['menu']->getIcon()),
                            ));
                        }
                    }
                }
            } else {
                $menu_route_root = explode('_', $m['route']);
                $menu_section = $menu_route_root[1];
                if ($section == $menu_section) {
                    $mainSectionIcon = 'fa ' . $m['menu']->getIcon();
                    $mainSection = $m['menu']->getPropertyName();
                    $subSection = 'list';
                    $menu->addChild($k, array(
                        'route' => $m['route'],
                        'extras' => array('class_icon' => 'fa ' . $l['children'][0]['menu']->getIcon()),
                    ));
                    if ($request->get('_route') == $m['route']) {
                        $menu->addChild($mainSection, array(
                            'route' => $m['route'],
                            'extras' => array('class_icon' => 'fa ' . $m['menu']->getIcon()),
                        ));
                    }
                }
            }
        }

        switch ($action) {
            case 'new':
                $menu->addChild('Add');
                break;
            case 'show':
                $menu->addChild('Show');
                break;
            case 'edit':
                $menu->addChild('Edit');
                break;
        }

        if (!empty($action)) {
            $subSection = $action;
        }

        $menu->setExtras(array(
            'mainSection' => $mainSection,
            'mainSectionIcon' => $mainSectionIcon,
            'subSection' => $subSection,
            'title' => $mainSection . ' - ' . $subSection,
        ));

        return $menu;
    }
}
