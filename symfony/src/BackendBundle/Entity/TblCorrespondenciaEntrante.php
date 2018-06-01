<?php

namespace BackendBundle\Entity;

/**
 * TblCorrespondenciaEntrante
 */
class TblCorrespondenciaEntrante
{
    /**
     * @var integer
     */
    private $idCorrespondenciaEntrante;

    /**
     * @var string
     */
    private $codCorrespondenciaEntrante;

    /**
     * @var string
     */
    private $codReferenciaSreci;

    /**
     * @var \DateTime
     */
    private $fechaIngreso;

    /**
     * @var \DateTime
     */
    private $horaIngreso;

    /**
     * @var string
     */
    private $asuntoCorrespondencia;

    /**
     * @var string
     */
    private $descripcionCorrespondencia;

    /**
     * @var string
     */
    private $observacionesCorrespondencia;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    private $idCorrespondenciaEnc;

    /**
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $idInstitucion;

    /**
     * @var \BackendBundle\Entity\TblDireccionesSreci
     */
    private $idDireccionSreci;


    /**
     * Get idCorrespondenciaEntrante
     *
     * @return integer
     */
    public function getIdCorrespondenciaEntrante()
    {
        return $this->idCorrespondenciaEntrante;
    }

    /**
     * Set codCorrespondenciaEntrante
     *
     * @param string $codCorrespondenciaEntrante
     *
     * @return TblCorrespondenciaEntrante
     */
    public function setCodCorrespondenciaEntrante($codCorrespondenciaEntrante)
    {
        $this->codCorrespondenciaEntrante = $codCorrespondenciaEntrante;

        return $this;
    }

    /**
     * Get codCorrespondenciaEntrante
     *
     * @return string
     */
    public function getCodCorrespondenciaEntrante()
    {
        return $this->codCorrespondenciaEntrante;
    }

    /**
     * Set codReferenciaSreci
     *
     * @param string $codReferenciaSreci
     *
     * @return TblCorrespondenciaEntrante
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

    /**
     * Set fechaIngreso
     *
     * @param \DateTime $fechaIngreso
     *
     * @return TblCorrespondenciaEntrante
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
     * Set horaIngreso
     *
     * @param \DateTime $horaIngreso
     *
     * @return TblCorrespondenciaEntrante
     */
    public function setHoraIngreso($horaIngreso)
    {
        $this->horaIngreso = $horaIngreso;

        return $this;
    }

    /**
     * Get horaIngreso
     *
     * @return \DateTime
     */
    public function getHoraIngreso()
    {
        return $this->horaIngreso;
    }

    /**
     * Set asuntoCorrespondencia
     *
     * @param string $asuntoCorrespondencia
     *
     * @return TblCorrespondenciaEntrante
     */
    public function setAsuntoCorrespondencia($asuntoCorrespondencia)
    {
        $this->asuntoCorrespondencia = $asuntoCorrespondencia;

        return $this;
    }

    /**
     * Get asuntoCorrespondencia
     *
     * @return string
     */
    public function getAsuntoCorrespondencia()
    {
        return $this->asuntoCorrespondencia;
    }

    /**
     * Set descripcionCorrespondencia
     *
     * @param string $descripcionCorrespondencia
     *
     * @return TblCorrespondenciaEntrante
     */
    public function setDescripcionCorrespondencia($descripcionCorrespondencia)
    {
        $this->descripcionCorrespondencia = $descripcionCorrespondencia;

        return $this;
    }

    /**
     * Get descripcionCorrespondencia
     *
     * @return string
     */
    public function getDescripcionCorrespondencia()
    {
        return $this->descripcionCorrespondencia;
    }

    /**
     * Set observacionesCorrespondencia
     *
     * @param string $observacionesCorrespondencia
     *
     * @return TblCorrespondenciaEntrante
     */
    public function setObservacionesCorrespondencia($observacionesCorrespondencia)
    {
        $this->observacionesCorrespondencia = $observacionesCorrespondencia;

        return $this;
    }

    /**
     * Get observacionesCorrespondencia
     *
     * @return string
     */
    public function getObservacionesCorrespondencia()
    {
        return $this->observacionesCorrespondencia;
    }

    /**
     * Set idCorrespondenciaEnc
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaEnc $idCorrespondenciaEnc
     *
     * @return TblCorrespondenciaEntrante
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
     * Set idInstitucion
     *
     * @param \BackendBundle\Entity\TblInstituciones $idInstitucion
     *
     * @return TblCorrespondenciaEntrante
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
     * Set idDireccionSreci
     *
     * @param \BackendBundle\Entity\TblDireccionesSreci $idDireccionSreci
     *
     * @return TblCorrespondenciaEntrante
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
}

