<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent;
use CrowdLuv\AdminBundle\Form\FollowerLuvsTalentType;

/**
 * FollowerLuvsTalent controller.
 *
 * @Route("/followerluvstalent")
 */
class FollowerLuvsTalentController extends Controller
{

    /**
     * Lists all FollowerLuvsTalent entities.
     *
     * @Route("/", name="followerluvstalent")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CrowdLuvAdminBundle:FollowerLuvsTalent')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new FollowerLuvsTalent entity.
     *
     * @Route("/", name="followerluvstalent_create")
     * @Method("POST")
     * @Template("CrowdLuvAdminBundle:FollowerLuvsTalent:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new FollowerLuvsTalent();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('followerluvstalent_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a FollowerLuvsTalent entity.
     *
     * @param FollowerLuvsTalent $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FollowerLuvsTalent $entity)
    {
        $form = $this->createForm(new FollowerLuvsTalentType(), $entity, array(
            'action' => $this->generateUrl('followerluvstalent_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FollowerLuvsTalent entity.
     *
     * @Route("/new", name="followerluvstalent_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new FollowerLuvsTalent();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a FollowerLuvsTalent entity.
     *
     * @Route("/{id}", name="followerluvstalent_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:FollowerLuvsTalent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FollowerLuvsTalent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FollowerLuvsTalent entity.
     *
     * @Route("/{id}/edit", name="followerluvstalent_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:FollowerLuvsTalent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FollowerLuvsTalent entity.');
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
    * Creates a form to edit a FollowerLuvsTalent entity.
    *
    * @param FollowerLuvsTalent $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FollowerLuvsTalent $entity)
    {
        $form = $this->createForm(new FollowerLuvsTalentType(), $entity, array(
            'action' => $this->generateUrl('followerluvstalent_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FollowerLuvsTalent entity.
     *
     * @Route("/{id}", name="followerluvstalent_update")
     * @Method("PUT")
     * @Template("CrowdLuvAdminBundle:FollowerLuvsTalent:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:FollowerLuvsTalent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FollowerLuvsTalent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('followerluvstalent_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a FollowerLuvsTalent entity.
     *
     * @Route("/{id}", name="followerluvstalent_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CrowdLuvAdminBundle:FollowerLuvsTalent')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FollowerLuvsTalent entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('followerluvstalent'));
    }

    /**
     * Creates a form to delete a FollowerLuvsTalent entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('followerluvstalent_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
