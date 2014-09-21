<?php

namespace CrowdLuv\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Talent
 *
 * @ORM\Table(name="talent")
 * @ORM\Entity(repositoryClass="CrowdLuv\AdminBundle\Entity\TalentRepository")
 */
class Talent
{
  
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FollowerLuvsTalent", mappedBy="crowdluvTalent")
     */
    private $follower_luvs_talent;

   /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TalentLandingPage", mappedBy="crowdluvTalent")
     */
    private $talentLandingPages;

    /**
     * @var integer
     *
     * @ORM\Column(name="crowdluv_tid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="fb_pid", type="text")
     */
    private $fbPid;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_page_name", type="text")
     */
    private $fbPageName;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="text")
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="text")
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="text")
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="home_city", type="text")
     */
    private $homeCity;

    /**
     * @var integer
     *
     * @ORM\Column(name="waitlisted", type="integer")
     */
    private $waitlisted;

    /**
     * @var string
     *
     * @ORM\Column(name="crowdluv_vurl", type="string", length=45)
     */
    private $crowdluvVurl;



    public function __construct(){
        $this->follower_luvs_talent = new ArrayCollection();
    }

    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set crowdluvTid
     *
     * @param integer $crowdluvTid
     * @return Talent
     */
    public function setCrowdluvTid($crowdluvTid)
    {
        $this->crowdluvTid = $crowdluvTid;

        return $this;
    }

    /**
     * Get crowdluvTid
     *
     * @return integer 
     */
    public function getCrowdluvTid()
    {
        return $this->crowdluvTid;
    }

    /**
     * Set fbPid
     *
     * @param string $fbPid
     * @return Talent
     */
    public function setFbPid($fbPid)
    {
        $this->fbPid = $fbPid;

        return $this;
    }

    /**
     * Get fbPid
     *
     * @return string 
     */
    public function getFbPid()
    {
        return $this->fbPid;
    }

    /**
     * Set fbPageName
     *
     * @param string $fbPageName
     * @return Talent
     */
    public function setFbPageName($fbPageName)
    {
        $this->fbPageName = $fbPageName;

        return $this;
    }

    /**
     * Get fbPageName
     *
     * @return string 
     */
    public function getFbPageName()
    {
        return $this->fbPageName;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Talent
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Talent
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Talent
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set homeCity
     *
     * @param string $homeCity
     * @return Talent
     */
    public function setHomeCity($homeCity)
    {
        $this->homeCity = $homeCity;

        return $this;
    }

    /**
     * Get homeCity
     *
     * @return string 
     */
    public function getHomeCity()
    {
        return $this->homeCity;
    }

    /**
     * Set waitlisted
     *
     * @param integer $waitlisted
     * @return Talent
     */
    public function setWaitlisted($waitlisted)
    {
        $this->waitlisted = $waitlisted;

        return $this;
    }

    /**
     * Get waitlisted
     *
     * @return integer 
     */
    public function getWaitlisted()
    {
        return $this->waitlisted;
    }

    /**
     * Set crowdluvVurl
     *
     * @param string $crowdluvVurl
     * @return Talent
     */
    public function setCrowdluvVurl($crowdluvVurl)
    {
        $this->crowdluvVurl = $crowdluvVurl;

        return $this;
    }

    /**
     * Get crowdluvVurl
     *
     * @return string 
     */
    public function getCrowdluvVurl()
    {
        return $this->crowdluvVurl;
    }

    /**
     * Add follower_luvs_talent
     *
     * @param \CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent $followerLuvsTalent
     * @return Talent
     */
    public function addFollowerLuvsTalent(\CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent $followerLuvsTalent)
    {
        $this->follower_luvs_talent[] = $followerLuvsTalent;

        return $this;
    }

    /**
     * Remove follower_luvs_talent
     *
     * @param \CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent $followerLuvsTalent
     */
    public function removeFollowerLuvsTalent(\CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent $followerLuvsTalent)
    {
        $this->follower_luvs_talent->removeElement($followerLuvsTalent);
    }

    /**
     * Get follower_luvs_talent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowerLuvsTalent()
    {
        return $this->follower_luvs_talent;
    }


    public function __toString(){
        return $this->getFbPageName();

    }



    /**
     * Add talentLandingPages
     *
     * @param \CrowdLuv\AdminBundle\Entity\TalentLandingPage $talentLandingPages
     * @return Talent
     */
    public function addTalentLandingPage(\CrowdLuv\AdminBundle\Entity\TalentLandingPage $talentLandingPages)
    {
        $this->talentLandingPages[] = $talentLandingPages;

        return $this;
    }

    /**
     * Remove talentLandingPages
     *
     * @param \CrowdLuv\AdminBundle\Entity\TalentLandingPage $talentLandingPages
     */
    public function removeTalentLandingPage(\CrowdLuv\AdminBundle\Entity\TalentLandingPage $talentLandingPages)
    {
        $this->talentLandingPages->removeElement($talentLandingPages);
    }

    /**
     * Get talentLandingPages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTalentLandingPages()
    {
        return $this->talentLandingPages;
    }
}
