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
    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuario;

    /**
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $idInstitucion;

    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $idEstado;


    /**
     * Set codCorrespondenciaEnc
     *
     * @param string $codCorrespondenciaEnc
     *
     * @return TblCorrespondenciaEnc
     */
    public function setCodCorrespondenciaEnc($codCorrespondenciaEnc)
    {
        $this->codCorrespondenciaEnc = $codCorrespondenciaEnc;

        return $this;
    }

    /**
     * Set idUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuario
     *
     * @return TblCorrespondenciaEnc
     */
    public function setIdUsuario(\BackendBundle\Entity\TblUsuarios $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return \BackendBundle\Entity\TblUsuarios
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idInstitucion
     *
     * @param \BackendBundle\Entity\TblInstituciones $idInstitucion
     *
     * @return TblCorrespondenciaEnc
     */
    public function setIdInstitucion(\BackendBundle\Entity\TblInstituciones $idInstitucion = null)
    {
        $this->idInstitucion = $idInstitucion;

        return $this;
    }

    /**
     * Get idInstitucion
     *
     * @return \BackendBundle\Entity\TblInstituciones
     */
    public function getIdInstitucion()
    {
        return $this->idInstitucion;
    }

    /**
     * Set idEstado
     *
     * @param \BackendBundle\Entity\TblEstados $idEstado
     *
     * @return TblCorrespondenciaEnc
     */
    public function setIdEstado(\BackendBundle\Entity\TblEstados $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado
     *
     * @return \BackendBundle\Entity\TblEstados
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
    /**
     * @var \BackendBundle\Entity\TblDireccionesSreci
     */
    private $idDireccionSreci;


    /**
     * Set idDireccionSreci
     *
     * @param \BackendBundle\Entity\TblDireccionesSreci $idDireccionSreci
     *
     * @return TblCorrespondenciaEnc
     */
    public function setIdDireccionSreci(\BackendBundle\Entity\TblDireccionesSreci $idDireccionSreci = null)
    {
        $this->idDireccionSreci = $idDireccionSreci;

        return $this;
    }

    /**
     * Get idDireccionSreci
     *
     * @return \BackendBundle\Entity\TblDireccionesSreci
     */
    public function getIdDireccionSreci()
    {
        return $this->idDireccionSreci;
    }
    /**
     * @var \DateTime
     */
    private $fechaModificacion;


    /**
     * Set fechaModificacion
     *
     * @param \DateTime $fechaModificacion
     *
     * @return TblCorrespondenciaEnc
     */
    public function setFechaModificacion($fechaModificacion)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Get fechaModificacion
     *
     * @return \DateTime
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }
    /**
     * @var string
     */
    private $codReferenciaSreci;


    /**
     * Set codReferenciaSreci
     *
     * @param string $codReferenciaSreci
     *
     * @return TblCorrespondenciaEnc
     */
    public function setCodReferenciaSreci($codReferenciaSreci)
    {
        $this->codReferenciaSreci = $codReferenciaSreci;

        return $this;
    }

    /**
     * Get codReferenciaSreci
     *
     * @return string
     */
    public function getCodReferenciaSreci()
    {
        return $this->codReferenciaSreci;
    }
}
