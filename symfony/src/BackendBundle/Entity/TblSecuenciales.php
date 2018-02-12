<?php

namespace BackendBundle\Entity;

/**
 * TblSecuenciales
 */
class TblSecuenciales
{
    /**
     * @var integer
     */
    private $idSecuencial;

    /**
     * @var string
     */
    private $codSecuencial;

    /**
     * @var string
     */
    private $valor1;

    /**
     * @var integer
     */
    private $valor2;

    /**
     * @var string
     */
    private $tablaSecuencia;


    /**
     * Get idSecuencial
     *
     * @return integer
     */
    public function getIdSecuencial()
    {
        return $this->idSecuencial;
    }

    /**
     * Set codSecuencial
     *
     * @param string $codSecuencial
     *
     * @return TblSecuenciales
     */
    public function setCodSecuencial($codSecuencial)
    {
        $this->codSecuencial = $codSecuencial;

        return $this;
    }

    /**
     * Get codSecuencial
     *
     * @return string
     */
    public function getCodSecuencial()
    {
        return $this->codSecuencial;
    }

    /**
     * Set valor1
     *
     * @param string $valor1
     *
     * @return TblSecuenciales
     */
    public function setValor1($valor1)
    {
        $this->valor1 = $valor1;

        return $this;
    }

    /**
     * Get valor1
     *
     * @return string
     */
    public function getValor1()
    {
        return $this->valor1;
    }

    /**
     * Set valor2
     *
     * @param integer $valor2
     *
     * @return TblSecuenciales
     */
    public function setValor2($valor2)
    {
        $this->valor2 = $valor2;

        return $this;
    }

    /**
     * Get valor2
     *
     * @return integer
     */
    public function getValor2()
    {
        return $this->valor2;
    }

    /**
     * Set tablaSecuencia
     *
     * @param string $tablaSecuencia
     *
     * @return TblSecuenciales
     */
    public function setTablaSecuencia($tablaSecuencia)
    {
        $this->tablaSecuencia = $tablaSecuencia;

        return $this;
    }

    /**
     * Get tablaSecuencia
     *
     * @return string
     */
    public function getTablaSecuencia()
    {
        return $this->tablaSecuencia;
    }
    /**
     * @var \BackendBundle\Entity\TblTipoDocumento
     */
    private $idTipoDocumento;


    /**
     * Set idTipoDocumento
     *
     * @param \BackendBundle\Entity\TblTipoDocumento $idTipoDocumento
     *
     * @return TblSecuenciales
     */
    public function setIdTipoDocumento(\BackendBundle\Entity\TblTipoDocumento $idTipoDocumento = null)
    {
        $this->idTipoDocumento = $idTipoDocumento;

        return $this;
    }

    /**
     * Get idTipoDocumento
     *
     * @return \BackendBundle\Entity\TblTipoDocumento
     */
    public function getIdTipoDocumento()
    {
        return $this->idTipoDocumento;
    }
    /**
     * @var string
     */
    private $reservada;


    /**
     * Set reservada
     *
     * @param string $reservada
     *
     * @return TblSecuenciales
     */
    public function setReservada($reservada)
    {
        $this->reservada = $reservada;

        return $this;
    }

    /**
     * Get reservada
     *
     * @return string
     */
    public function getReservada()
    {
        return $this->reservada;
    }
    /**
     * @var string
     */
    private $actualizada;


    /**
     * Set actualizada
     *
     * @param string $actualizada
     *
     * @return TblSecuenciales
     */
    public function setActualizada($actualizada)
    {
        $this->actualizada = $actualizada;

        return $this;
    }

    /**
     * Get actualizada
     *
     * @return string
     */
    public function getActualizada()
    {
        return $this->actualizada;
    }
    /**
     * @var \BackendBundle\Entity\TblTipoUsuario
     */
    private $idTipoUsuario;

    /**
     * @var \BackendBundle\Entity\TblDepartamentosFuncionales
     */
    private $idDeptoFuncional;


    /**
     * Set idTipoUsuario
     *
     * @param \BackendBundle\Entity\TblTipoUsuario $idTipoUsuario
     *
     * @return TblSecuenciales
     */
    public function setIdTipoUsuario(\BackendBundle\Entity\TblTipoUsuario $idTipoUsuario = null)
    {
        $this->idTipoUsuario = $idTipoUsuario;

        return $this;
    }

    /**
     * Get idTipoUsuario
     *
     * @return \BackendBundle\Entity\TblTipoUsuario
     */
    public function getIdTipoUsuario()
    {
        return $this->idTipoUsuario;
    }

    /**
     * Set idDeptoFuncional
     *
     * @param \BackendBundle\Entity\TblDepartamentosFuncionales $idDeptoFuncional
     *
     * @return TblSecuenciales
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
     * @var \BackendBundle\Entity\TblDireccionesSreci
     */
    private $idDireccionSreci;


    /**
     * Set idDireccionSreci
     *
     * @param \BackendBundle\Entity\TblDireccionesSreci $idDireccionSreci
     *
     * @return TblSecuenciales
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
     * @var boolean
     */
    private $habilitada = false;


    /**
     * Set habilitada
     *
     * @param boolean $habilitada
     *
     * @return TblSecuenciales
     */
    public function setHabilitada($habilitada)
    {
        $this->habilitada = $habilitada;

        return $this;
    }

    /**
     * Get habilitada
     *
     * @return boolean
     */
    public function getHabilitada()
    {
        return $this->habilitada;
    }
    /**
     * @var boolean
     */
    private $despacho = false;

    /**
     * @var \BackendBundle\Entity\TblTiposFuncionarios
     */
    private $idTipoFuncionario;


    /**
     * Set despacho
     *
     * @param boolean $despacho
     *
     * @return TblSecuenciales
     */
    public function setDespacho($despacho)
    {
        $this->despacho = $despacho;

        return $this;
    }

    /**
     * Get despacho
     *
     * @return boolean
     */
    public function getDespacho()
    {
        return $this->despacho;
    }

    /**
     * Set idTipoFuncionario
     *
     * @param \BackendBundle\Entity\TblTiposFuncionarios $idTipoFuncionario
     *
     * @return TblSecuenciales
     */
    public function setIdTipoFuncionario(\BackendBundle\Entity\TblTiposFuncionarios $idTipoFuncionario = null)
    {
        $this->idTipoFuncionario = $idTipoFuncionario;

        return $this;
    }

    /**
     * Get idTipoFuncionario
     *
     * @return \BackendBundle\Entity\TblTiposFuncionarios
     */
    public function getIdTipoFuncionario()
    {
        return $this->idTipoFuncionario;
    }
}
