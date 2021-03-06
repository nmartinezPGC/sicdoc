<?php

namespace BackendBundle\Entity;

/**
 * TblBitacoraCorrespondenciaEdit
 */
class TblBitacoraCorrespondenciaEdit
{
    /**
     * @var integer
     */
    private $idCorrespondenciaEdit;

    /**
     * @var string
     */
    private $descCorrespondenciaActual;

    /**
     * @var string
     */
    private $descCorrespondenciaAnterior;

    /**
     * @var string
     */
    private $temaComunicacionAnterior;

    /**
     * @var string
     */
    private $temaComunicacionActual;

    /**
     * @var \DateTime
     */
    private $fechaEdicion;

    /**
     * @var \DateTime
     */
    private $horaEdicion;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    private $idCorrespondenciaEnc;

    /**
     * @var \BackendBundle\Entity\TblFuncionarios
     */
    private $idFuncionarioAsignado;

    /**
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $idInstitucionAnteior;

    /**
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $idInstitucionActual;


    /**
     * Get idCorrespondenciaEdit
     *
     * @return integer
     */
    public function getIdCorrespondenciaEdit()
    {
        return $this->idCorrespondenciaEdit;
    }

    /**
     * Set descCorrespondenciaActual
     *
     * @param string $descCorrespondenciaActual
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setDescCorrespondenciaActual($descCorrespondenciaActual)
    {
        $this->descCorrespondenciaActual = $descCorrespondenciaActual;

        return $this;
    }

    /**
     * Get descCorrespondenciaActual
     *
     * @return string
     */
    public function getDescCorrespondenciaActual()
    {
        return $this->descCorrespondenciaActual;
    }

    /**
     * Set descCorrespondenciaAnterior
     *
     * @param string $descCorrespondenciaAnterior
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setDescCorrespondenciaAnterior($descCorrespondenciaAnterior)
    {
        $this->descCorrespondenciaAnterior = $descCorrespondenciaAnterior;

        return $this;
    }

    /**
     * Get descCorrespondenciaAnterior
     *
     * @return string
     */
    public function getDescCorrespondenciaAnterior()
    {
        return $this->descCorrespondenciaAnterior;
    }

    /**
     * Set temaComunicacionAnterior
     *
     * @param string $temaComunicacionAnterior
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setTemaComunicacionAnterior($temaComunicacionAnterior)
    {
        $this->temaComunicacionAnterior = $temaComunicacionAnterior;

        return $this;
    }

    /**
     * Get temaComunicacionAnterior
     *
     * @return string
     */
    public function getTemaComunicacionAnterior()
    {
        return $this->temaComunicacionAnterior;
    }

    /**
     * Set temaComunicacionActual
     *
     * @param string $temaComunicacionActual
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setTemaComunicacionActual($temaComunicacionActual)
    {
        $this->temaComunicacionActual = $temaComunicacionActual;

        return $this;
    }

    /**
     * Get temaComunicacionActual
     *
     * @return string
     */
    public function getTemaComunicacionActual()
    {
        return $this->temaComunicacionActual;
    }

    /**
     * Set fechaEdicion
     *
     * @param \DateTime $fechaEdicion
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setFechaEdicion($fechaEdicion)
    {
        $this->fechaEdicion = $fechaEdicion;

        return $this;
    }

    /**
     * Get fechaEdicion
     *
     * @return \DateTime
     */
    public function getFechaEdicion()
    {
        return $this->fechaEdicion;
    }

    /**
     * Set horaEdicion
     *
     * @param \DateTime $horaEdicion
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setHoraEdicion($horaEdicion)
    {
        $this->horaEdicion = $horaEdicion;

        return $this;
    }

    /**
     * Get horaEdicion
     *
     * @return \DateTime
     */
    public function getHoraEdicion()
    {
        return $this->horaEdicion;
    }

    /**
     * Set idCorrespondenciaEnc
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaEnc $idCorrespondenciaEnc
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setIdCorrespondenciaEnc(\BackendBundle\Entity\TblCorrespondenciaEnc $idCorrespondenciaEnc = null)
    {
        $this->idCorrespondenciaEnc = $idCorrespondenciaEnc;

        return $this;
    }

    /**
     * Get idCorrespondenciaEnc
     *
     * @return \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    public function getIdCorrespondenciaEnc()
    {
        return $this->idCorrespondenciaEnc;
    }

    /**
     * Set idFuncionarioAsignado
     *
     * @param \BackendBundle\Entity\TblFuncionarios $idFuncionarioAsignado
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setIdFuncionarioAsignado(\BackendBundle\Entity\TblFuncionarios $idFuncionarioAsignado = null)
    {
        $this->idFuncionarioAsignado = $idFuncionarioAsignado;

        return $this;
    }

    /**
     * Get idFuncionarioAsignado
     *
     * @return \BackendBundle\Entity\TblFuncionarios
     */
    public function getIdFuncionarioAsignado()
    {
        return $this->idFuncionarioAsignado;
    }

    /**
     * Set idInstitucionAnteior
     *
     * @param \BackendBundle\Entity\TblInstituciones $idInstitucionAnteior
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setIdInstitucionAnteior(\BackendBundle\Entity\TblInstituciones $idInstitucionAnteior = null)
    {
        $this->idInstitucionAnteior = $idInstitucionAnteior;

        return $this;
    }

    /**
     * Get idInstitucionAnteior
     *
     * @return \BackendBundle\Entity\TblInstituciones
     */
    public function getIdInstitucionAnteior()
    {
        return $this->idInstitucionAnteior;
    }

    /**
     * Set idInstitucionActual
     *
     * @param \BackendBundle\Entity\TblInstituciones $idInstitucionActual
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setIdInstitucionActual(\BackendBundle\Entity\TblInstituciones $idInstitucionActual = null)
    {
        $this->idInstitucionActual = $idInstitucionActual;

        return $this;
    }

    /**
     * Get idInstitucionActual
     *
     * @return \BackendBundle\Entity\TblInstituciones
     */
    public function getIdInstitucionActual()
    {
        return $this->idInstitucionActual;
    }
    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $idEstado;


    /**
     * Set idEstado
     *
     * @param \BackendBundle\Entity\TblEstados $idEstado
     *
     * @return TblBitacoraCorrespondenciaEdit
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
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $idInstitucionAnterior;


    /**
     * Set idInstitucionAnterior
     *
     * @param \BackendBundle\Entity\TblInstituciones $idInstitucionAnterior
     *
     * @return TblBitacoraCorrespondenciaEdit
     */
    public function setIdInstitucionAnterior(\BackendBundle\Entity\TblInstituciones $idInstitucionAnterior = null)
    {
        $this->idInstitucionAnterior = $idInstitucionAnterior;

        return $this;
    }

    /**
     * Get idInstitucionAnterior
     *
     * @return \BackendBundle\Entity\TblInstituciones
     */
    public function getIdInstitucionAnterior()
    {
        return $this->idInstitucionAnterior;
    }
}
