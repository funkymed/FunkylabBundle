<?php

namespace Tigreboite\FunkylabBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Bloc controller.
 *
 * @Route("/bloc")
 */
class BlocController extends SortableController
{
    protected $entityName = 'Tigreboite\FunkylabBundle\Entity\Bloc';
    protected $formType = 'Tigreboite\FunkylabBundle\Form\Type\BlocType';
    protected $route_base = 'admin_bloc';
    protected $repository = 'TigreboiteFunkylabBundle:Bloc';
    protected $dir_path = 'medias/bloc/';

    /**
     * Lists all entities.
     *
     * @Route("/", name="admin_bloc", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_RH', 'ROLE_ETUDE')")
     */
    public function indexAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $data = array(
            'type' => $request->get('type', false),
            'id' => $request->get('id', false),
        );

        if ($data['type'] == 'page') {
            $url_type = 'admin_page';
        } else {
            $url_type = 'admin_'.$data['type'];
        }

        $data['url_type'] = $this->get('router')->generate($url_type);

        return $data;
    }

    /**
     * Creates a new entity.
     *
     * @Route("/", name="admin_bloc_create")
     * @Method("POST")
     * @Template("TigreboiteFunkylabBundle:Bloc:form.html.twig")
     */
    public function createAction()
    {
        return parent::createAction();
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/new", name="admin_bloc_new")
     * @Method("GET")
     * @Template("TigreboiteFunkylabBundle:Bloc:form.html.twig")
     */
    public function newAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $type = $request->get('type', false);
        $id = $request->get('id', false);

        /*
         * @var $entity Bloc
         */
        $bloc = $this->get('funkylab.factory.bloc')->createBloc($type, $id);

        $form = $this->createCreateForm($bloc);

        return array(
          'entity' => $bloc,
          'form' => $form->createView(),
          'ajax' => $request->isXmlHttpRequest(),
        );
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/{id}/edit", name="admin_bloc_edit", options={"expose"=true})
     * @Method("GET")
     * @Template("TigreboiteFunkylabBundle:Bloc:form.html.twig")
     */
    public function editAction($id)
    {
        return parent::editAction($id);
    }

    /**
     * Edits an existing entity.
     *
     * @Route("/update/{id}", name="admin_bloc_update")
     * @Method("PUT")
     * @Template("TigreboiteFunkylabBundle:Bloc:form.html.twig")
     */
    public function updateAction($id)
    {
        return parent::updateAction($id);
    }
    /**
     * Deletes an entity.
     *
     * @Route("/remove/{id}", name="admin_bloc_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        return parent::deleteAction($id);
    }

    /**
     * Upload files.
     *
     * @Route("/upload", name="admin_bloc_upload")
     * @Method({"POST","PUT"})
     */
    public function uploadAction()
    {
        return parent::uploadAction();
    }

    /**
     * Get a list of all entities.
     *
     * @Route("/liste", name="admin_bloc_liste")
     * @Method("GET")
     * @Template()
     */
    public function listeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $type = $request->get('type', false);
        $id = $request->get('id', false);

        if ($type) {
            $entities = $this->get('funkylab.manager.bloc')->findAllByParent($type, $id);
        } else {
            $entities = array();
        }

        return array(
          'entities' => $entities,
        );
    }

    /**
     * Save order.
     *
     * @Route("/save/position", name="admin_bloc_order")
     * @Method("POST")
     */
    public function orderAction()
    {
        return parent::orderAction();
    }
}
