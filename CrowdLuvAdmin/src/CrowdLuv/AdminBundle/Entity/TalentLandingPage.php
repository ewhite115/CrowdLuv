<?php

namespace CrowdLuv\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TalentLandingPage
 *
 * @ORM\Table(name="talent_landingpage")
 * @ORM\Entity(repositoryClass="CrowdLuv\AdminBundle\Entity\TalentLandingPageRepository")
 */
class TalentLandingPage
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
     * @var Talent
     *
     * @ORM\ManyToOne(targetEntity="Talent", inversedBy="talentLandingPages")
     * @ORM\JoinColumn(name="crowdluv_tid", referencedColumnName="crowdluv_tid")
     */
    private $crowdluvTalent;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="message_timestamp", type="datetime")
     */
    private $messageTimestamp;


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
     * @return TalentLandingPage
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
     * Set message
     *
     * @param string $message
     * @return TalentLandingPage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return TalentLandingPage
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set messageTimestamp
     *
     * @param \DateTime $messageTimestamp
     * @return TalentLandingPage
     */
    public function setMessageTimestamp($messageTimestamp)
    {
        $this->messageTimestamp = $messageTimestamp;

        return $this;
    }

    /**
     * Get messageTimestamp
     *
     * @return \DateTime 
     */
    public function getMessageTimestamp()
    {
        return $this->messageTimestamp;
    }

    /**
     * Set crowdluvTalent
     *
     * @param \CrowdLuv\AdminBundle\Entity\Talent $crowdluvTalent
     * @return TalentLandingPage
     */
    public function setCrowdluvTalent(\CrowdLuv\AdminBundle\Entity\Talent $crowdluvTalent = null)
    {
        $this->crowdluvTalent = $crowdluvTalent;

        return $this;
    }

    /**
     * Get crowdluvTalent
     *
     * @return \CrowdLuv\AdminBundle\Entity\Talent 
     */
    public function getCrowdluvTalent()
    {
        return $this->crowdluvTalent;
    }
}
