<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CrowdLuv\AdminBundle\Entity\Follower;
use CrowdLuv\AdminBundle\Form\FollowerType;

/**
 * Follower controller.
 *
 * @Route("/follower")
 */
class FollowerController extends Controller
{

    /**
     * Lists all Follower entities.
     *
     * @Route("/", name="follower")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CrowdLuvAdminBundle:Follower')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Follower entity.
     *
     * @Route("/", name="follower_create")
     * @Method("POST")
     * @Template("CrowdLuvAdminBundle:Follower:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Follower();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('follower_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Follower entity.
     *
     * @param Follower $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Follower $entity)
    {
        $form = $this->createForm(new FollowerType(), $entity, array(
            'action' => $this->generateUrl('follower_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Follower entity.
     *
     * @Route("/new", name="follower_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Follower();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Follower entity.
     *
     * @Route("/{id}", name="follower_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:Follower')->find($id);
        $followedTalents = $em->getRepository('CrowdLuvAdminBundle:FollowerLuvsTalent')->findBycrowdluv_follower($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Follower entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'followedTalents' => $followedTalents,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Follower entity.
     *
     * @Route("/{id}/edit", name="follower_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:Follower')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Follower entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Follower entity.
    *
    * @param Follower $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Follower $entity)
    {
        $form = $this->createForm(new FollowerType(), $entity, array(
            'action' => $this->generateUrl('follower_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Follower entity.
     *
     * @Route("/{id}", name="follower_update")
     * @Method("PUT")
     * @Template("CrowdLuvAdminBundle:Follower:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:Follower')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Follower entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('follower_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Follower entity.
     *
     * @Route("/{id}", name="follower_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CrowdLuvAdminBundle:Follower')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Follower entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('follower'));
    }

    /**
     * Creates a form to delete a Follower entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('follower_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
