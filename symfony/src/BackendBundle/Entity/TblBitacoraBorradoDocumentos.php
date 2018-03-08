<?php

namespace BackendBundle\Entity;

/**
 * TblBitacoraBorradoDocumentos
 */
class TblBitacoraBorradoDocumentos
{
    /**
     * @var integer
     */
    private $idDocumentoBorrado;

    /**
     * @var string
     */
    private $descDocumento;

    /**
     * @var string
     */
    private $justificacionBorrado;

    /**
     * @var \DateTime
     */
    private $fechaBorrado;

    /**
     * @var \DateTime
     */
    private $horaBorrado;

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuario;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    private $idCorrespondenciaEnc;


    /**
     * Get idDocumentoBorrado
     *
     * @return integer
     */
    public function getIdDocumentoBorrado()
    {
        return $this->idDocumentoBorrado;
    }

    /**
     * Set descDocumento
     *
     * @param string $descDocumento
     *
     * @return TblBitacoraBorradoDocumentos
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
     * Set justificacionBorrado
     *
     * @param string $justificacionBorrado
     *
     * @return TblBitacoraBorradoDocumentos
     */
    public function setJustificacionBorrado($justificacionBorrado)
    {
        $this->justificacionBorrado = $justificacionBorrado;

        return $this;
    }

    /**
     * Get justificacionBorrado
     *
     * @return string
     */
    public function getJustificacionBorrado()
    {
        return $this->justificacionBorrado;
    }

    /**
     * Set fechaBorrado
     *
     * @param \DateTime $fechaBorrado
     *
     * @return TblBitacoraBorradoDocumentos
     */
    public function setFechaBorrado($fechaBorrado)
    {
        $this->fechaBorrado = $fechaBorrado;

        return $this;
    }

    /**
     * Get fechaBorrado
     *
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }

    /**
     * Set horaBorrado
     *
     * @param \DateTime $horaBorrado
     *
     * @return TblBitacoraBorradoDocumentos
     */
    public function setHoraBorrado($horaBorrado)
    {
        $this->horaBorrado = $horaBorrado;

        return $this;
    }

    /**
     * Get horaBorrado
     *
     * @return \DateTime
     */
    public function getHoraBorrado()
    {
        return $this->horaBorrado;
    }

    /**
     * Set idUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuario
     *
     * @return TblBitacoraBorradoDocumentos
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
     * @return TblBitacoraBorradoDocumentos
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
}

