<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CrowdLuv\AdminBundle\Entity\Talent;
use CrowdLuv\AdminBundle\Form\TalentType;

/**
 * Talent controller.
 *
 * @Route("/talent")
 */
class TalentController extends Controller
{

    /**
     * Lists all Talent entities.
     *
     * @Route("/", name="talent")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CrowdLuvAdminBundle:Talent')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Talent entity.
     *
     * @Route("/", name="talent_create")
     * @Method("POST")
     * @Template("CrowdLuvAdminBundle:Talent:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Talent();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('talent_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Talent entity.
     *
     * @param Talent $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Talent $entity)
    {
        $form = $this->createForm(new TalentType(), $entity, array(
            'action' => $this->generateUrl('talent_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Talent entity.
     *
     * @Route("/new", name="talent_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Talent();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Talent entity.
     *
     * @Route("/{id}", name="talent_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:Talent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Talent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Talent entity.
     *
     * @Route("/{id}/edit", name="talent_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:Talent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Talent entity.');
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
    * Creates a form to edit a Talent entity.
    *
    * @param Talent $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Talent $entity)
    {
        $form = $this->createForm(new TalentType(), $entity, array(
            'action' => $this->generateUrl('talent_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Talent entity.
     *
     * @Route("/{id}", name="talent_update")
     * @Method("PUT")
     * @Template("CrowdLuvAdminBundle:Talent:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:Talent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Talent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('talent_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Talent entity.
     *
     * @Route("/{id}", name="talent_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CrowdLuvAdminBundle:Talent')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Talent entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('talent'));
    }

    /**
     * Creates a form to delete a Talent entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('talent_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
