<?php

namespace Tigreboite\FunkylabBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tigreboite\FunkylabBundle\Annotation\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Tigreboite\FunkylabBundle\Event\EntityEvent;
use Tigreboite\FunkylabBundle\TigreboiteFunkylabEvent;

/**
 * Page controller.
 *
 * @Route("/page")
 */
class PageController extends DatagridController
{
    protected $entityName = 'Tigreboite\FunkylabBundle\Entity\Page';
    protected $formType = 'Tigreboite\FunkylabBundle\Form\Type\PageType';
    protected $route_base = 'admin_page';
    protected $repository = 'TigreboiteFunkylabBundle:Page';
    protected $dir_path = 'medias/page/';

    /**
     * Lists all entities.
     *
     * @Route("/", name="admin_page")
     * @Method("GET")
     * @Template()
     * @Menu("Pages", icon="fa-flag",groupe="CMS")
     * @Security("has_role('ROLE_SUPER_ADMIN') || has_role('ROLE_MODERATOR')")
     */
    public function indexAction()
    {
        return parent::indexAction();
    }

    /**
     * Get all entities.
     *
     * @Route("/list", name="admin_page_list", options={"expose"=true})
     * @Method("GET")
     */
    public function listAction()
    {
        return parent::listAction();
    }

    /**
     * Create entity.
     *
     * @Route("/", name="admin_page_create")
     * @Method("POST")
     * @Template("TigreboiteFunkylabBundle:Page:form.html.twig")
     */
    public function createAction()
    {
        return parent::createAction();
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/new", name="admin_page_new")
     * @Method("GET")
     * @Template("TigreboiteFunkylabBundle:Page:form.html.twig")
     */
    public function newAction()
    {
        return parent::newAction();
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/{id}/edit", name="admin_page_edit", options={"expose"=true})
     * @Method("GET")
     * @Template("TigreboiteFunkylabBundle:Page:form.html.twig")
     */
    public function editAction($id)
    {
        return parent::editAction($id);
    }

    /**
     * Edits an existing entity.
     *
     * @Route("/update/{id}", name="admin_page_update")
     * @Method("PUT")
     * @Template("TigreboiteFunkylabBundle:Page:form.html.twig")
     */
    public function updateAction($id)
    {
        return parent::updateAction($id);
    }

    /**
     * Delete an entity.
     *
     * @Route("/{id}", name="admin_page_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->repository)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        //Removed blocs
        $blocs = $entity->getBlocs();
        foreach ($blocs as $bloc) {
            $em->remove($bloc);
        }

        $event = new EntityEvent($entity);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(TigreboiteFunkylabEvent::ENTITY_DELETED, $event);

        $em->remove($entity);
        $em->flush();

        return new Response('Deleted');
    }

    /**
     * Upload files.
     *
     * @Route("/upload", name="admin_page_upload")
     * @Method({"POST","PUT"})
     */
    public function uploadAction()
    {
        return parent::uploadAction();
    }

    /**
     * @Route("/autocomplete/request", name="admin_page_autocomplete_request", options={"expose"=true})
     * @Method("GET")
     */
    public function ajaxAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $query = $request->get('q', '');
        $orderby = $request->get('orderyby', 'title');
        $order = $request->get('order', 'ASC');
        $limit = $request->get('limit', '10');
        $page = $request->get('page', '1') - 1;
        if ($page < 0) {
            $page = 1;
        }

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->repository);
        $entities = $repo->findDataQuery($query, $orderby, $order, $limit, $page);
        $items = array();

        foreach ($entities as $entity) {
            $items[] = array(
              'id' => $entity->getId(),
              'title' => $entity->getTitle(),
              'summary' => $entity->getSummary(),
            );
        }

        $data = array(
          'q' => $query,
          'term' => $query,
          'page' => $page + 1,
          'items' => $items,
          'total_count' => $repo->countDataQuery($query),
        );

        return new JsonResponse($data);
    }
}
