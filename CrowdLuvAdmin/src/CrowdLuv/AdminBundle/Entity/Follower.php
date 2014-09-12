<?php

namespace CrowdLuv\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Follower
 *
 * @ORM\Table(name="follower")
 * @ORM\Entity(repositoryClass="CrowdLuv\AdminBundle\Entity\FollowerRepository")
 */
class Follower
{
    
     /**
     * @var integer
     *
     * @ORM\Column(name="crowdluv_uid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FollowerLuvsTalent", mappedBy="crowdluv_follower")
     */
    private $follower_luvs_talent;


    /**
     * @var integer
     *
     * @ORM\Column(name="fb_uid", type="text")
     */
    private $fbUid;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="text")
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="location_fb_id", type="text")
     */
    private $locationFbId;

    /**
     * @var string
     *
     * @ORM\Column(name="location_fbname", type="text")
     */
    private $locationFbname;

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
     * @ORM\Column(name="email", type="text")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="date")
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_relationship_status", type="text")
     */
    private $fbRelationshipStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signupdate", type="date")
     */
    private $signupdate;

    /**
     * @var string
     *
     * @ORM\Column(name="allow_cl_email", type="string", length=25)
     */
    private $allowClEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="allow_cl_sms", type="string", length=25)
     */
    private $allowClSms;

    /**
     * @var integer
     *
     * @ORM\Column(name="deactivated", type="integer")
     */
    private $deactivated;



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
     * Set crowdluvUid
     *
     * @param integer $crowdluvUid
     * @return Follower
     */
    public function setCrowdluvUid($crowdluvUid)
    {
        $this->crowdluvUid = $crowdluvUid;

        return $this;
    }

    /**
     * Get crowdluvUid
     *
     * @return integer 
     */
    public function getCrowdluvUid()
    {
        return $this->crowdluvUid;
    }

    /**
     * Set fbUid
     *
     * @param integer $fbUid
     * @return Follower
     */
    public function setFbUid($fbUid)
    {
        $this->fbUid = $fbUid;

        return $this;
    }

    /**
     * Get fbUid
     *
     * @return integer 
     */
    public function getFbUid()
    {
        return $this->fbUid;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Follower
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
     * Set locationFbId
     *
     * @param string $locationFbId
     * @return Follower
     */
    public function setLocationFbId($locationFbId)
    {
        $this->locationFbId = $locationFbId;

        return $this;
    }

    /**
     * Get locationFbId
     *
     * @return string 
     */
    public function getLocationFbId()
    {
        return $this->locationFbId;
    }

    /**
     * Set locationFbname
     *
     * @param string $locationFbname
     * @return Follower
     */
    public function setLocationFbname($locationFbname)
    {
        $this->locationFbname = $locationFbname;

        return $this;
    }

    /**
     * Get locationFbname
     *
     * @return string 
     */
    public function getLocationFbname()
    {
        return $this->locationFbname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Follower
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
     * @return Follower
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
     * Set email
     *
     * @param string $email
     * @return Follower
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Follower
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return Follower
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime 
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set fbRelationshipStatus
     *
     * @param string $fbRelationshipStatus
     * @return Follower
     */
    public function setFbRelationshipStatus($fbRelationshipStatus)
    {
        $this->fbRelationshipStatus = $fbRelationshipStatus;

        return $this;
    }

    /**
     * Get fbRelationshipStatus
     *
     * @return string 
     */
    public function getFbRelationshipStatus()
    {
        return $this->fbRelationshipStatus;
    }

    /**
     * Set signupdate
     *
     * @param \DateTime $signupdate
     * @return Follower
     */
    public function setSignupdate($signupdate)
    {
        $this->signupdate = $signupdate;

        return $this;
    }

    /**
     * Get signupdate
     *
     * @return \DateTime 
     */
    public function getSignupdate()
    {
        return $this->signupdate;
    }

    /**
     * Set allowClEmail
     *
     * @param string $allowClEmail
     * @return Follower
     */
    public function setAllowClEmail($allowClEmail)
    {
        $this->allowClEmail = $allowClEmail;

        return $this;
    }

    /**
     * Get allowClEmail
     *
     * @return string 
     */
    public function getAllowClEmail()
    {
        return $this->allowClEmail;
    }

    /**
     * Set allowClSms
     *
     * @param string $allowClSms
     * @return Follower
     */
    public function setAllowClSms($allowClSms)
    {
        $this->allowClSms = $allowClSms;

        return $this;
    }

    /**
     * Get allowClSms
     *
     * @return string 
     */
    public function getAllowClSms()
    {
        return $this->allowClSms;
    }

    /**
     * Set deactivated
     *
     * @param integer $deactivated
     * @return Follower
     */
    public function setDeactivated($deactivated)
    {
        $this->deactivated = $deactivated;

        return $this;
    }

    /**
     * Get deactivated
     *
     * @return integer 
     */
    public function getDeactivated()
    {
        return $this->deactivated;
    }

    /**
     * Add follower_luvs_talent
     *
     * @param \CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent $followerLuvsTalent
     * @return Follower
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
        return $this->getFirstname() . " " . $this->getLastname() . "-" . $this->getEmail();

    }


}
