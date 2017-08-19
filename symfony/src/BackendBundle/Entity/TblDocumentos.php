<?php

namespace BackendBundle\Entity;

/**
 * TblDocumentos
 */
class TblDocumentos
{
    /**
     * @var string
     */
    private $codDocumento;

    /**
     * @var integer
     */
    private $idDocumento;

    /**
     * @var string
     */
    private $descDocumento;

    /**
     * @var string
     */
    private $urlDocumento;

    /**
     * @var \DateTime
     */
    private $fechaIngreso;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaDet
     */
    private $codCorrespondenciaDet;

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $codUsuario;


    /**
     * Get codDocumento
     *
     * @return string
     */
    public function getCodDocumento()
    {
        return $this->codDocumento;
    }

    /**
     * Set idDocumento
     *
     * @param integer $idDocumento
     *
     * @return TblDocumentos
     */
    public function setIdDocumento($idDocumento)
    {
        $this->idDocumento = $idDocumento;

        return $this;
    }

    /**
     * Get idDocumento
     *
     * @return integer
     */
    public function getIdDocumento()
    {
        return $this->idDocumento;
    }

    /**
     * Set descDocumento
     *
     * @param string $descDocumento
     *
     * @return TblDocumentos
     */
    public function setDescDocumento($descDocumento)
    {
        $this->descDocumento = $descDocumento;

        return $this;
    }

    /**
     * Get descDocumento
     *
     * @return string
     */
    public function getDescDocumento()
    {
        return $this->descDocumento;
    }

    /**
     * Set urlDocumento
     *
     * @param string $urlDocumento
     *
     * @return TblDocumentos
     */
    public function setUrlDocumento($urlDocumento)
    {
        $this->urlDocumento = $urlDocumento;

        return $this;
    }

    /**
     * Get urlDocumento
     *
     * @return string
     */
    public function getUrlDocumento()
    {
        return $this->urlDocumento;
    }

    /**
     * Set fechaIngreso
     *
     * @param \DateTime $fechaIngreso
     *
     * @return TblDocumentos
     */
    public function setFechaIngreso($fechaIngreso)
    {
        $this->fechaIngreso = $fechaIngreso;

        return $this;
    }

    /**
     * Get fechaIngreso
     *
     * @return \DateTime
     */
    public function getFechaIngreso()
    {
        return $this->fechaIngreso;
    }

    /**
     * Set codCorrespondenciaDet
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaDet $codCorrespondenciaDet
     *
     * @return TblDocumentos
     */
    public function setCodCorrespondenciaDet(\BackendBundle\Entity\TblCorrespondenciaDet $codCorrespondenciaDet = null)
    {
        $this->codCorrespondenciaDet = $codCorrespondenciaDet;

        return $this;
    }

    /**
     * Get codCorrespondenciaDet
     *
     * @return \BackendBundle\Entity\TblCorrespondenciaDet
     */
    public function getCodCorrespondenciaDet()
    {
        return $this->codCorrespondenciaDet;
    }

    /**
     * Set codUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $codUsuario
     *
     * @return TblDocumentos
     */
    public function setCodUsuario(\BackendBundle\Entity\TblUsuarios $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \BackendBundle\Entity\TblUsuarios
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }
}

