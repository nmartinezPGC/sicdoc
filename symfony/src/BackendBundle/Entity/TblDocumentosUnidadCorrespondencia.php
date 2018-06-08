<?php

namespace BackendBundle\Entity;

/**
 * TblDocumentosUnidadCorrespondencia
 */
class TblDocumentosUnidadCorrespondencia
{
    /**
     * @var integer
     */
    private $idDocumento;

    /**
     * @var string
     */
    private $urlDocumento;

    /**
     * @var string
     */
    private $descDocumento;

    /**
     * @var \DateTime
     */
    private $fechaIngreso;

    /**
     * @var \DateTime
     */
    private $horaIngreso;

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuario;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEntrante
     */
    private $idCorrespondenciaEntrante;


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
     * Set urlDocumento
     *
     * @param string $urlDocumento
     *
     * @return TblDocumentosUnidadCorrespondencia
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
     * Set descDocumento
     *
     * @param string $descDocumento
     *
     * @return TblDocumentosUnidadCorrespondencia
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
     * Set fechaIngreso
     *
     * @param \DateTime $fechaIngreso
     *
     * @return TblDocumentosUnidadCorrespondencia
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
     * @return TblDocumentosUnidadCorrespondencia
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
     * Set idUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuario
     *
     * @return TblDocumentosUnidadCorrespondencia
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
     * Set idCorrespondenciaEntrante
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaEntrante $idCorrespondenciaEntrante
     *
     * @return TblDocumentosUnidadCorrespondencia
     */
    public function setIdCorrespondenciaEntrante(\BackendBundle\Entity\TblCorrespondenciaEntrante $idCorrespondenciaEntrante = null)
    {
        $this->idCorrespondenciaEntrante = $idCorrespondenciaEntrante;

        return $this;
    }

    /**
     * Get idCorrespondenciaEntrante
     *
     * @return \BackendBundle\Entity\TblCorrespondenciaEntrante
     */
    public function getIdCorrespondenciaEntrante()
    {
        return $this->idCorrespondenciaEntrante;
    }
    /**
     * @var string
     */
    private $codDocumento;


    /**
     * Set codDocumento
     *
     * @param string $codDocumento
     *
     * @return TblDocumentosUnidadCorrespondencia
     */
    public function setCodDocumento($codDocumento)
    {
        $this->codDocumento = $codDocumento;

        return $this;
    }

    /**
     * Get codDocumento
     *
     * @return string
     */
    public function getCodDocumento()
    {
        return $this->codDocumento;
    }
}
