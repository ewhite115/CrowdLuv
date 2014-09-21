<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CrowdLuv\AdminBundle\Entity\TalentLandingPage;
use CrowdLuv\AdminBundle\Form\TalentLandingPageType;

/**
 * TalentLandingPage controller.
 *
 * @Route("/talentlandingpage")
 */
class TalentLandingPageController extends Controller
{

    /**
     * Lists all TalentLandingPage entities.
     *
     * @Route("/", name="talentlandingpage")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CrowdLuvAdminBundle:TalentLandingPage')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new TalentLandingPage entity.
     *
     * @Route("/", name="talentlandingpage_create")
     * @Method("POST")
     * @Template("CrowdLuvAdminBundle:TalentLandingPage:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TalentLandingPage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('talentlandingpage_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a TalentLandingPage entity.
     *
     * @param TalentLandingPage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TalentLandingPage $entity)
    {
        $form = $this->createForm(new TalentLandingPageType(), $entity, array(
            'action' => $this->generateUrl('talentlandingpage_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TalentLandingPage entity.
     *
     * @Route("/new", name="talentlandingpage_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TalentLandingPage();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a TalentLandingPage entity.
     *
     * @Route("/{id}", name="talentlandingpage_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:TalentLandingPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TalentLandingPage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TalentLandingPage entity.
     *
     * @Route("/{id}/edit", name="talentlandingpage_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:TalentLandingPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TalentLandingPage entity.');
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
    * Creates a form to edit a TalentLandingPage entity.
    *
    * @param TalentLandingPage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TalentLandingPage $entity)
    {
        $form = $this->createForm(new TalentLandingPageType(), $entity, array(
            'action' => $this->generateUrl('talentlandingpage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TalentLandingPage entity.
     *
     * @Route("/{id}", name="talentlandingpage_update")
     * @Method("PUT")
     * @Template("CrowdLuvAdminBundle:TalentLandingPage:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrowdLuvAdminBundle:TalentLandingPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TalentLandingPage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('talentlandingpage_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a TalentLandingPage entity.
     *
     * @Route("/{id}", name="talentlandingpage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CrowdLuvAdminBundle:TalentLandingPage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TalentLandingPage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('talentlandingpage'));
    }

    /**
     * Creates a form to delete a TalentLandingPage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('talentlandingpage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
