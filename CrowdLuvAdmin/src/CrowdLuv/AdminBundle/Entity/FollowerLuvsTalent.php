<?php

namespace CrowdLuv\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FollowerLuvsTalent
 *
 * @ORM\Table(name="follower_luvs_talent")
 * @ORM\Entity(repositoryClass="CrowdLuv\AdminBundle\Entity\FollowerLuvsTalentRepository")
 */
class FollowerLuvsTalent
{
      

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var Follower
     * @ORM\ManyToOne(targetEntity="Follower", inversedBy="follower_luvs_talent")
     * @ORM\JoinColumn(name="crowdluv_uid", referencedColumnName="crowdluv_uid")
     */
    private $crowdluv_follower;

    
    /**
     * @var Talent
     *
     * @ORM\ManyToOne(targetEntity="Talent", inversedBy="follower_luvs_talent")
     * @ORM\JoinColumn(name="crowdluv_tid", referencedColumnName="crowdluv_tid")
     */
    private $crowdluv_talent;


    /**
     * @var integer
     *
     * @ORM\Column(name="still_following", type="integer")
     */
    private $stillFollowing;

    /**
     * @var integer
     *
     * @ORM\Column(name="allow_email", type="integer")
     */
    private $allowEmail;

    /**
     * @var integer
     *
     * @ORM\Column(name="allow_sms", type="integer")
     */
    private $allowSms;

    /**
     * @var integer
     *
     * @ORM\Column(name="will_travel_distance", type="integer")
     */
    private $willTravelDistance;

    /**
     * @var integer
     *
     * @ORM\Column(name="will_travel_time", type="integer")
     */
    private $willTravelTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="follow_date", type="datetime")
     */
    private $followDate;


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
     * Set stillFollowing
     *
     * @param integer $stillFollowing
     * @return FollowerLuvsTalent
     */
    public function setStillFollowing($stillFollowing)
    {
        $this->stillFollowing = $stillFollowing;

        return $this;
    }

    /**
     * Get stillFollowing
     *
     * @return integer 
     */
    public function getStillFollowing()
    {
        return $this->stillFollowing;
    }

    /**
     * Set allowEmail
     *
     * @param integer $allowEmail
     * @return FollowerLuvsTalent
     */
    public function setAllowEmail($allowEmail)
    {
        $this->allowEmail = $allowEmail;

        return $this;
    }

    /**
     * Get allowEmail
     *
     * @return integer 
     */
    public function getAllowEmail()
    {
        return $this->allowEmail;
    }

    /**
     * Set allowSms
     *
     * @param integer $allowSms
     * @return FollowerLuvsTalent
     */
    public function setAllowSms($allowSms)
    {
        $this->allowSms = $allowSms;

        return $this;
    }

    /**
     * Get allowSms
     *
     * @return integer 
     */
    public function getAllowSms()
    {
        return $this->allowSms;
    }

    /**
     * Set willTravelDistance
     *
     * @param integer $willTravelDistance
     * @return FollowerLuvsTalent
     */
    public function setWillTravelDistance($willTravelDistance)
    {
        $this->willTravelDistance = $willTravelDistance;

        return $this;
    }

    /**
     * Get willTravelDistance
     *
     * @return integer 
     */
    public function getWillTravelDistance()
    {
        return $this->willTravelDistance;
    }

    /**
     * Set willTravelTime
     *
     * @param integer $willTravelTime
     * @return FollowerLuvsTalent
     */
    public function setWillTravelTime($willTravelTime)
    {
        $this->willTravelTime = $willTravelTime;

        return $this;
    }

    /**
     * Get willTravelTime
     *
     * @return integer 
     */
    public function getWillTravelTime()
    {
        return $this->willTravelTime;
    }

    /**
     * Set followDate
     *
     * @param \DateTime $followDate
     * @return FollowerLuvsTalent
     */
    public function setFollowDate($followDate)
    {
        $this->followDate = $followDate;

        return $this;
    }

    /**
     * Get followDate
     *
     * @return \DateTime 
     */
    public function getFollowDate()
    {
        return $this->followDate;
    }

    /**
     * Set crowdluv_follower
     *
     * @param \CrowdLuv\AdminBundle\Entity\Follower $crowdluvFollower
     * @return FollowerLuvsTalent
     */
    public function setCrowdluvFollower(\CrowdLuv\AdminBundle\Entity\Follower $crowdluvFollower = null)
    {
        $this->crowdluv_follower = $crowdluvFollower;

        return $this;
    }

    /**
     * Get crowdluv_follower
     *
     * @return \CrowdLuv\AdminBundle\Entity\Follower 
     */
    public function getCrowdluvFollower()
    {
        return $this->crowdluv_follower;
    }

    /**
     * Set crowdluv_talent
     *
     * @param \CrowdLuv\AdminBundle\Entity\Talent $crowdluvTalent
     * @return FollowerLuvsTalent
     */
    public function setCrowdluvTalent(\CrowdLuv\AdminBundle\Entity\Talent $crowdluvTalent = null)
    {
        $this->crowdluv_talent = $crowdluvTalent;

        return $this;
    }

    /**
     * Get crowdluv_talent
     *
     * @return \CrowdLuv\AdminBundle\Entity\Talent 
     */
    public function getCrowdluvTalent()
    {
        return $this->crowdluv_talent;
    }

   
}

