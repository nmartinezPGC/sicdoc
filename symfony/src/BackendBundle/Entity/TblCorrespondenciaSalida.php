<?php

namespace BackendBundle\Entity;

/**
 * TblCorrespondenciaSalida
 */
class TblCorrespondenciaSalida
{
    /**
     * @var integer
     */
    private $idCorrespondenciaSalida;

    /**
     * @var string
     */
    private $codCorrespondenciaSalida;

    /**
     * @var string
     */
    private $nombreDestinatario;

    /**
     * @var \DateTime
     */
    private $fechaRecepcion;

    /**
     * @var \DateTime
     */
    private $horaRecepcion;

    /**
     * @var \DateTime
     */
    private $fechaEntrega;

    /**
     * @var \DateTime
     */
    private $horaEntrega;

    /**
     * @var boolean
     */
    private $acuseDeEntrega;

    /**
     * @var \BackendBundle\Entity\TblDireccionesSreci
     */
    private $idDireccionSreci;

    /**
     * @var \BackendBundle\Entity\TblDepartamentosFuncionales
     */
    private $idDeptoFuncional;

    /**
     * @var \BackendBundle\Entity\TblFuncionarios
     */
    private $idFuncionarioAsignado;

    /**
     * @var \BackendBundle\Entity\TblInstituciones
     */
    private $idInstitucion;

    /**
     * @var \BackendBundle\Entity\TblFuncionarios
     */
    private $idFuncionarioRepartidor;


    /**
     * Get idCorrespondenciaSalida
     *
     * @return integer
     */
    public function getIdCorrespondenciaSalida()
    {
        return $this->idCorrespondenciaSalida;
    }

    /**
     * Set codCorrespondenciaSalida
     *
     * @param string $codCorrespondenciaSalida
     *
     * @return TblCorrespondenciaSalida
     */
    public function setCodCorrespondenciaSalida($codCorrespondenciaSalida)
    {
        $this->codCorrespondenciaSalida = $codCorrespondenciaSalida;

        return $this;
    }

    /**
     * Get codCorrespondenciaSalida
     *
     * @return string
     */
    public function getCodCorrespondenciaSalida()
    {
        return $this->codCorrespondenciaSalida;
    }

    /**
     * Set nombreDestinatario
     *
     * @param string $nombreDestinatario
     *
     * @return TblCorrespondenciaSalida
     */
    public function setNombreDestinatario($nombreDestinatario)
    {
        $this->nombreDestinatario = $nombreDestinatario;

        return $this;
    }

    /**
     * Get nombreDestinatario
     *
     * @return string
     */
    public function getNombreDestinatario()
    {
        return $this->nombreDestinatario;
    }

    /**
     * Set fechaRecepcion
     *
     * @param \DateTime $fechaRecepcion
     *
     * @return TblCorrespondenciaSalida
     */
    public function setFechaRecepcion($fechaRecepcion)
    {
        $this->fechaRecepcion = $fechaRecepcion;

        return $this;
    }

    /**
     * Get fechaRecepcion
     *
     * @return \DateTime
     */
    public function getFechaRecepcion()
    {
        return $this->fechaRecepcion;
    }

    /**
     * Set horaRecepcion
     *
     * @param \DateTime $horaRecepcion
     *
     * @return TblCorrespondenciaSalida
     */
    public function setHoraRecepcion($horaRecepcion)
    {
        $this->horaRecepcion = $horaRecepcion;

        return $this;
    }

    /**
     * Get horaRecepcion
     *
     * @return \DateTime
     */
    public function getHoraRecepcion()
    {
        return $this->horaRecepcion;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     *
     * @return TblCorrespondenciaSalida
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set horaEntrega
     *
     * @param \DateTime $horaEntrega
     *
     * @return TblCorrespondenciaSalida
     */
    public function setHoraEntrega($horaEntrega)
    {
        $this->horaEntrega = $horaEntrega;

        return $this;
    }

    /**
     * Get horaEntrega
     *
     * @return \DateTime
     */
    public function getHoraEntrega()
    {
        return $this->horaEntrega;
    }

    /**
     * Set acuseDeEntrega
     *
     * @param boolean $acuseDeEntrega
     *
     * @return TblCorrespondenciaSalida
     */
    public function setAcuseDeEntrega($acuseDeEntrega)
    {
        $this->acuseDeEntrega = $acuseDeEntrega;

        return $this;
    }

    /**
     * Get acuseDeEntrega
     *
     * @return boolean
     */
    public function getAcuseDeEntrega()
    {
        return $this->acuseDeEntrega;
    }

    /**
     * Set idDireccionSreci
     *
     * @param \BackendBundle\Entity\TblDireccionesSreci $idDireccionSreci
     *
     * @return TblCorrespondenciaSalida
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
     * Set idDeptoFuncional
     *
     * @param \BackendBundle\Entity\TblDepartamentosFuncionales $idDeptoFuncional
     *
     * @return TblCorrespondenciaSalida
     */
    public function setIdDeptoFuncional(\BackendBundle\Entity\TblDepartamentosFuncionales $idDeptoFuncional = null)
    {
        $this->idDeptoFuncional = $idDeptoFuncional;

        return $this;
    }

    /**
     * Get idDeptoFuncional
     *
     * @return \BackendBundle\Entity\TblDepartamentosFuncionales
     */
    public function getIdDeptoFuncional()
    {
        return $this->idDeptoFuncional;
    }

    /**
     * Set idFuncionarioAsignado
     *
     * @param \BackendBundle\Entity\TblFuncionarios $idFuncionarioAsignado
     *
     * @return TblCorrespondenciaSalida
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
     * Set idInstitucion
     *
     * @param \BackendBundle\Entity\TblInstituciones $idInstitucion
     *
     * @return TblCorrespondenciaSalida
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
     * Set idFuncionarioRepartidor
     *
     * @param \BackendBundle\Entity\TblFuncionarios $idFuncionarioRepartidor
     *
     * @return TblCorrespondenciaSalida
     */
    public function setIdFuncionarioRepartidor(\BackendBundle\Entity\TblFuncionarios $idFuncionarioRepartidor = null)
    {
        $this->idFuncionarioRepartidor = $idFuncionarioRepartidor;

        return $this;
    }

    /**
     * Get idFuncionarioRepartidor
     *
     * @return \BackendBundle\Entity\TblFuncionarios
     */
    public function getIdFuncionarioRepartidor()
    {
        return $this->idFuncionarioRepartidor;
    }
    /**
     * @var string
     */
    private $direccionDestinatario;


    /**
     * Set direccionDestinatario
     *
     * @param string $direccionDestinatario
     *
     * @return TblCorrespondenciaSalida
     */
    public function setDireccionDestinatario($direccionDestinatario)
    {
        $this->direccionDestinatario = $direccionDestinatario;

        return $this;
    }

    /**
     * Get direccionDestinatario
     *
     * @return string
     */
    public function getDireccionDestinatario()
    {
        return $this->direccionDestinatario;
    }
}
