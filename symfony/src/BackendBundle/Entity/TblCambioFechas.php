<?php

namespace BackendBundle\Entity;

/**
 * TblCambioFechas
 */
class TblCambioFechas
{
    /**
     * @var integer
     */
    private $idCambioFecha;

    /**
     * @var string
     */
    private $codCorrespondencia;

    /**
     * @var string
     */
    private $justificacionSolicitud;

    /**
     * @var \DateTime
     */
    private $fechaCreacionSolicitud;

    /**
     * @var \DateTime
     */
    private $fechaCambio;

    /**
     * @var string
     */
    private $observacionesSolicitud;

    /**
     * @var \BackendBundle\Entity\TblFuncionarios
     */
    private $idUsuarioCreacion;

    /**
     * @var \BackendBundle\Entity\TblFuncionarios
     */
    private $idUsuarioSolicitud;

    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $idEstadoSolicitud;


    /**
     * Get idCambioFecha
     *
     * @return integer
     */
    public function getIdCambioFecha()
    {
        return $this->idCambioFecha;
    }

    /**
     * Set codCorrespondencia
     *
     * @param string $codCorrespondencia
     *
     * @return TblCambioFechas
     */
    public function setCodCorrespondencia($codCorrespondencia)
    {
        $this->codCorrespondencia = $codCorrespondencia;

        return $this;
    }

    /**
     * Get codCorrespondencia
     *
     * @return string
     */
    public function getCodCorrespondencia()
    {
        return $this->codCorrespondencia;
    }

    /**
     * Set justificacionSolicitud
     *
     * @param string $justificacionSolicitud
     *
     * @return TblCambioFechas
     */
    public function setJustificacionSolicitud($justificacionSolicitud)
    {
        $this->justificacionSolicitud = $justificacionSolicitud;

        return $this;
    }

    /**
     * Get justificacionSolicitud
     *
     * @return string
     */
    public function getJustificacionSolicitud()
    {
        return $this->justificacionSolicitud;
    }

    /**
     * Set fechaCreacionSolicitud
     *
     * @param \DateTime $fechaCreacionSolicitud
     *
     * @return TblCambioFechas
     */
    public function setFechaCreacionSolicitud($fechaCreacionSolicitud)
    {
        $this->fechaCreacionSolicitud = $fechaCreacionSolicitud;

        return $this;
    }

    /**
     * Get fechaCreacionSolicitud
     *
     * @return \DateTime
     */
    public function getFechaCreacionSolicitud()
    {
        return $this->fechaCreacionSolicitud;
    }

    /**
     * Set fechaCambio
     *
     * @param \DateTime $fechaCambio
     *
     * @return TblCambioFechas
     */
    public function setFechaCambio($fechaCambio)
    {
        $this->fechaCambio = $fechaCambio;

        return $this;
    }

    /**
     * Get fechaCambio
     *
     * @return \DateTime
     */
    public function getFechaCambio()
    {
        return $this->fechaCambio;
    }

    /**
     * Set observacionesSolicitud
     *
     * @param string $observacionesSolicitud
     *
     * @return TblCambioFechas
     */
    public function setObservacionesSolicitud($observacionesSolicitud)
    {
        $this->observacionesSolicitud = $observacionesSolicitud;

        return $this;
    }

    /**
     * Get observacionesSolicitud
     *
     * @return string
     */
    public function getObservacionesSolicitud()
    {
        return $this->observacionesSolicitud;
    }

    /**
     * Set idUsuarioCreacion
     *
     * @param \BackendBundle\Entity\TblFuncionarios $idUsuarioCreacion
     *
     * @return TblCambioFechas
     */
    public function setIdUsuarioCreacion(\BackendBundle\Entity\TblFuncionarios $idUsuarioCreacion = null)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion
     *
     * @return \BackendBundle\Entity\TblFuncionarios
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idUsuarioSolicitud
     *
     * @param \BackendBundle\Entity\TblFuncionarios $idUsuarioSolicitud
     *
     * @return TblCambioFechas
     */
    public function setIdUsuarioSolicitud(\BackendBundle\Entity\TblFuncionarios $idUsuarioSolicitud = null)
    {
        $this->idUsuarioSolicitud = $idUsuarioSolicitud;

        return $this;
    }

    /**
     * Get idUsuarioSolicitud
     *
     * @return \BackendBundle\Entity\TblFuncionarios
     */
    public function getIdUsuarioSolicitud()
    {
        return $this->idUsuarioSolicitud;
    }

    /**
     * Set idEstadoSolicitud
     *
     * @param \BackendBundle\Entity\TblEstados $idEstadoSolicitud
     *
     * @return TblCambioFechas
     */
    public function setIdEstadoSolicitud(\BackendBundle\Entity\TblEstados $idEstadoSolicitud = null)
    {
        $this->idEstadoSolicitud = $idEstadoSolicitud;

        return $this;
    }

    /**
     * Get idEstadoSolicitud
     *
     * @return \BackendBundle\Entity\TblEstados
     */
    public function getIdEstadoSolicitud()
    {
        return $this->idEstadoSolicitud;
    }
}

