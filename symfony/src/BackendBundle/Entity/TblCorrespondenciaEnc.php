<?php

namespace BackendBundle\Entity;

/**
 * TblCorrespondenciaEnc
 */
class TblCorrespondenciaEnc
{
    /**
     * @var string
     */
    private $codCorrespondenciaEnc;

    /**
     * @var integer
     */
    private $idCorrespondenciaEnc;

    /**
     * @var string
     */
    private $descCorrespondenciaEnc;

    /**
     * @var \DateTime
     */
    private $fechaIngreso;

    /**
     * @var \DateTime
     */
    private $fechaMaxEntrega;

    /**
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $codInstitucion;

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $codUsuario;

    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $codEstado;

    /**
     * @var \BackendBundle\Entity\TblDireccionesSreci
     */
    private $codDireccionSreci;


    /**
     * Get codCorrespondenciaEnc
     *
     * @return string
     */
    public function getCodCorrespondenciaEnc()
    {
        return $this->codCorrespondenciaEnc;
    }

    /**
     * Set idCorrespondenciaEnc
     *
     * @param integer $idCorrespondenciaEnc
     *
     * @return TblCorrespondenciaEnc
     */
    public function setIdCorrespondenciaEnc($idCorrespondenciaEnc)
    {
        $this->idCorrespondenciaEnc = $idCorrespondenciaEnc;

        return $this;
    }

    /**
     * Get idCorrespondenciaEnc
     *
     * @return integer
     */
    public function getIdCorrespondenciaEnc()
    {
        return $this->idCorrespondenciaEnc;
    }

    /**
     * Set descCorrespondenciaEnc
     *
     * @param string $descCorrespondenciaEnc
     *
     * @return TblCorrespondenciaEnc
     */
    public function setDescCorrespondenciaEnc($descCorrespondenciaEnc)
    {
        $this->descCorrespondenciaEnc = $descCorrespondenciaEnc;

        return $this;
    }

    /**
     * Get descCorrespondenciaEnc
     *
     * @return string
     */
    public function getDescCorrespondenciaEnc()
    {
        return $this->descCorrespondenciaEnc;
    }

    /**
     * Set fechaIngreso
     *
     * @param \DateTime $fechaIngreso
     *
     * @return TblCorrespondenciaEnc
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
     * Set fechaMaxEntrega
     *
     * @param \DateTime $fechaMaxEntrega
     *
     * @return TblCorrespondenciaEnc
     */
    public function setFechaMaxEntrega($fechaMaxEntrega)
    {
        $this->fechaMaxEntrega = $fechaMaxEntrega;

        return $this;
    }

    /**
     * Get fechaMaxEntrega
     *
     * @return \DateTime
     */
    public function getFechaMaxEntrega()
    {
        return $this->fechaMaxEntrega;
    }

    /**
     * Set codInstitucion
     *
     * @param \BackendBundle\Entity\TblInstituciones $codInstitucion
     *
     * @return TblCorrespondenciaEnc
     */
    public function setCodInstitucion(\BackendBundle\Entity\TblInstituciones $codInstitucion = null)
    {
        $this->codInstitucion = $codInstitucion;

        return $this;
    }

    /**
     * Get codInstitucion
     *
     * @return \BackendBundle\Entity\TblInstituciones
     */
    public function getCodInstitucion()
    {
        return $this->codInstitucion;
    }

    /**
     * Set codUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $codUsuario
     *
     * @return TblCorrespondenciaEnc
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

    /**
     * Set codEstado
     *
     * @param \BackendBundle\Entity\TblEstados $codEstado
     *
     * @return TblCorrespondenciaEnc
     */
    public function setCodEstado(\BackendBundle\Entity\TblEstados $codEstado = null)
    {
        $this->codEstado = $codEstado;

        return $this;
    }

    /**
     * Get codEstado
     *
     * @return \BackendBundle\Entity\TblEstados
     */
    public function getCodEstado()
    {
        return $this->codEstado;
    }

    /**
     * Set codDireccionSreci
     *
     * @param \BackendBundle\Entity\TblDireccionesSreci $codDireccionSreci
     *
     * @return TblCorrespondenciaEnc
     */
    public function setCodDireccionSreci(\BackendBundle\Entity\TblDireccionesSreci $codDireccionSreci = null)
    {
        $this->codDireccionSreci = $codDireccionSreci;

        return $this;
    }

    /**
     * Get codDireccionSreci
     *
     * @return \BackendBundle\Entity\TblDireccionesSreci
     */
    public function getCodDireccionSreci()
    {
        return $this->codDireccionSreci;
    }
}

