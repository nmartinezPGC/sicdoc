<?php

namespace BackendBundle\Entity;

/**
 * TblCorrespondenciaDet
 */
class TblCorrespondenciaDet
{
    /**
     * @var string
     */
    private $codCorrespondenciaDet;

    /**
     * @var integer
     */
    private $idCorrespondenciaDet;

    /**
     * @var string
     */
    private $descCorrespondenciaDet;

    /**
     * @var string
     */
    private $actividadRealizar;

    /**
     * @var \DateTime
     */
    private $fechaIngreso;

    /**
     * @var \DateTime
     */
    private $fechaSalida;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    private $codCorrespondenciaEnc;

    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $codEstado;

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $codUsuario;

    
    /**
     * Set codCorrespondenciaDet
     *
     * @param integer $codCorrespondenciaDet
     *
     * @return TblCorrespondenciaDet
     */
    public function setCodCorrespondenciaDet($codCorrespondenciaDet)
    {
        $this->codCorrespondenciaDet = $codCorrespondenciaDet;

        return $this;
    }

    /**
     * Get codCorrespondenciaDet
     *
     * @return string
     */
    public function getCodCorrespondenciaDet()
    {
        return $this->codCorrespondenciaDet;
    }

    /**
     * Set idCorrespondenciaDet
     *
     * @param integer $idCorrespondenciaDet
     *
     * @return TblCorrespondenciaDet
     */
    public function setIdCorrespondenciaDet($idCorrespondenciaDet)
    {
        $this->idCorrespondenciaDet = $idCorrespondenciaDet;

        return $this;
    }

    /**
     * Get idCorrespondenciaDet
     *
     * @return integer
     */
    public function getIdCorrespondenciaDet()
    {
        return $this->idCorrespondenciaDet;
    }

    /**
     * Set descCorrespondenciaDet
     *
     * @param string $descCorrespondenciaDet
     *
     * @return TblCorrespondenciaDet
     */
    public function setDescCorrespondenciaDet($descCorrespondenciaDet)
    {
        $this->descCorrespondenciaDet = $descCorrespondenciaDet;

        return $this;
    }

    /**
     * Get descCorrespondenciaDet
     *
     * @return string
     */
    public function getDescCorrespondenciaDet()
    {
        return $this->descCorrespondenciaDet;
    }

    /**
     * Set actividadRealizar
     *
     * @param string $actividadRealizar
     *
     * @return TblCorrespondenciaDet
     */
    public function setActividadRealizar($actividadRealizar)
    {
        $this->actividadRealizar = $actividadRealizar;

        return $this;
    }

    /**
     * Get actividadRealizar
     *
     * @return string
     */
    public function getActividadRealizar()
    {
        return $this->actividadRealizar;
    }

    /**
     * Set fechaIngreso
     *
     * @param \DateTime $fechaIngreso
     *
     * @return TblCorrespondenciaDet
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
     * Set fechaSalida
     *
     * @param \DateTime $fechaSalida
     *
     * @return TblCorrespondenciaDet
     */
    public function setFechaSalida($fechaSalida)
    {
        $this->fechaSalida = $fechaSalida;

        return $this;
    }

    /**
     * Get fechaSalida
     *
     * @return \DateTime
     */
    public function getFechaSalida()
    {
        return $this->fechaSalida;
    }

    /**
     * Set codCorrespondenciaEnc
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaEnc $codCorrespondenciaEnc
     *
     * @return TblCorrespondenciaDet
     */
    public function setCodCorrespondenciaEnc(\BackendBundle\Entity\TblCorrespondenciaEnc $codCorrespondenciaEnc = null)
    {
        $this->codCorrespondenciaEnc = $codCorrespondenciaEnc;

        return $this;
    }

    /**
     * Get codCorrespondenciaEnc
     *
     * @return \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    public function getCodCorrespondenciaEnc()
    {
        return $this->codCorrespondenciaEnc;
    }

    /**
     * Set codEstado
     *
     * @param \BackendBundle\Entity\TblEstados $codEstado
     *
     * @return TblCorrespondenciaDet
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
     * Set codUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $codUsuario
     *
     * @return TblCorrespondenciaDet
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
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuario;

    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $idEstado;

    /**
     * @var \BackendBundle\Entity\TblCorrespondenciaEnc
     */
    private $idCorrespondenciaEnc;


    /**
     * Set idUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuario
     *
     * @return TblCorrespondenciaDet
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
     * Set idEstado
     *
     * @param \BackendBundle\Entity\TblEstados $idEstado
     *
     * @return TblCorrespondenciaDet
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
     * Set idCorrespondenciaEnc
     *
     * @param \BackendBundle\Entity\TblCorrespondenciaEnc $idCorrespondenciaEnc
     *
     * @return TblCorrespondenciaDet
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
     * @var string
     */
    private $codReferenciaSreci;


    /**
     * Set codReferenciaSreci
     *
     * @param string $codReferenciaSreci
     *
     * @return TblCorrespondenciaDet
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
