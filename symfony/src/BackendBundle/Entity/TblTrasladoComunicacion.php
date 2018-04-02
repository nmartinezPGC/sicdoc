<?php

namespace BackendBundle\Entity;

/**
 * TblTrasladoComunicacion
 */
class TblTrasladoComunicacion
{
    /**
     * @var integer
     */
    private $idTraslado;

    /**
     * @var \DateTime
     */
    private $fechaTraslado;

    /**
     * @var \DateTime
     */
    private $horaTraslado;

    /**
     * @var string
     */
    private $justificacionTraslado;

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuario;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    private $idCorrespondenciaEnc;

    /**
     * @var \BackendBundle\Entity\TblFuncionarios
     */
    private $idUsuarioAsignado;


    /**
     * Get idTraslado
     *
     * @return integer
     */
    public function getIdTraslado()
    {
        return $this->idTraslado;
    }

    /**
     * Set fechaTraslado
     *
     * @param \DateTime $fechaTraslado
     *
     * @return TblTrasladoComunicacion
     */
    public function setFechaTraslado($fechaTraslado)
    {
        $this->fechaTraslado = $fechaTraslado;

        return $this;
    }

    /**
     * Get fechaTraslado
     *
     * @return \DateTime
     */
    public function getFechaTraslado()
    {
        return $this->fechaTraslado;
    }

    /**
     * Set horaTraslado
     *
     * @param \DateTime $horaTraslado
     *
     * @return TblTrasladoComunicacion
     */
    public function setHoraTraslado($horaTraslado)
    {
        $this->horaTraslado = $horaTraslado;

        return $this;
    }

    /**
     * Get horaTraslado
     *
     * @return \DateTime
     */
    public function getHoraTraslado()
    {
        return $this->horaTraslado;
    }

    /**
     * Set justificacionTraslado
     *
     * @param string $justificacionTraslado
     *
     * @return TblTrasladoComunicacion
     */
    public function setJustificacionTraslado($justificacionTraslado)
    {
        $this->justificacionTraslado = $justificacionTraslado;

        return $this;
    }

    /**
     * Get justificacionTraslado
     *
     * @return string
     */
    public function getJustificacionTraslado()
    {
        return $this->justificacionTraslado;
    }

    /**
     * Set idUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuario
     *
     * @return TblTrasladoComunicacion
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
     * Set idCorrespondenciaEnc
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaEnc $idCorrespondenciaEnc
     *
     * @return TblTrasladoComunicacion
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
     * Set idUsuarioAsignado
     *
     * @param \BackendBundle\Entity\TblFuncionarios $idUsuarioAsignado
     *
     * @return TblTrasladoComunicacion
     */
    public function setIdUsuarioAsignado(\BackendBundle\Entity\TblFuncionarios $idUsuarioAsignado = null)
    {
        $this->idUsuarioAsignado = $idUsuarioAsignado;

        return $this;
    }

    /**
     * Get idUsuarioAsignado
     *
     * @return \BackendBundle\Entity\TblFuncionarios
     */
    public function getIdUsuarioAsignado()
    {
        return $this->idUsuarioAsignado;
    }
}

