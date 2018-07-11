<?php

namespace BackendBundle\Entity;

/**
 * TblFuncionarios
 */
class TblFuncionarios
{
    /**
     * @var integer
     */
    private $idFuncionario;

    /**
     * @var string
     */
    private $codFuncionario;

    /**
     * @var string
     */
    private $nombre1Funcionario;

    /**
     * @var string
     */
    private $nombre2Funcionario;

    /**
     * @var string
     */
    private $apellido1Funcionario;

    /**
     * @var string
     */
    private $apellido2Funcionario;

    /**
     * @var string
     */
    private $emailFuncionario;

    /**
     * @var integer
     */
    private $telefonoFuncionario;

    /**
     * @var integer
     */
    private $celularFuncionario;

    /**
     * @var \BackendBundle\Entity\TblDepartamentosFuncionales
     */
    private $idDeptoFuncional;

    /**
     * @var \BackendBundle\Entity\TblTiposFuncionarios
     */
    private $idTipoFuncionario;


    /**
     * Get idFuncionario
     *
     * @return integer
     */
    public function getIdFuncionario()
    {
        return $this->idFuncionario;
    }

    /**
     * Set codFuncionario
     *
     * @param string $codFuncionario
     *
     * @return TblFuncionarios
     */
    public function setCodFuncionario($codFuncionario)
    {
        $this->codFuncionario = $codFuncionario;

        return $this;
    }

    /**
     * Get codFuncionario
     *
     * @return string
     */
    public function getCodFuncionario()
    {
        return $this->codFuncionario;
    }

    /**
     * Set nombre1Funcionario
     *
     * @param string $nombre1Funcionario
     *
     * @return TblFuncionarios
     */
    public function setNombre1Funcionario($nombre1Funcionario)
    {
        $this->nombre1Funcionario = $nombre1Funcionario;

        return $this;
    }

    /**
     * Get nombre1Funcionario
     *
     * @return string
     */
    public function getNombre1Funcionario()
    {
        return $this->nombre1Funcionario;
    }

    /**
     * Set nombre2Funcionario
     *
     * @param string $nombre2Funcionario
     *
     * @return TblFuncionarios
     */
    public function setNombre2Funcionario($nombre2Funcionario)
    {
        $this->nombre2Funcionario = $nombre2Funcionario;

        return $this;
    }

    /**
     * Get nombre2Funcionario
     *
     * @return string
     */
    public function getNombre2Funcionario()
    {
        return $this->nombre2Funcionario;
    }

    /**
     * Set apellido1Funcionario
     *
     * @param string $apellido1Funcionario
     *
     * @return TblFuncionarios
     */
    public function setApellido1Funcionario($apellido1Funcionario)
    {
        $this->apellido1Funcionario = $apellido1Funcionario;

        return $this;
    }

    /**
     * Get apellido1Funcionario
     *
     * @return string
     */
    public function getApellido1Funcionario()
    {
        return $this->apellido1Funcionario;
    }

    /**
     * Set apellido2Funcionario
     *
     * @param string $apellido2Funcionario
     *
     * @return TblFuncionarios
     */
    public function setApellido2Funcionario($apellido2Funcionario)
    {
        $this->apellido2Funcionario = $apellido2Funcionario;

        return $this;
    }

    /**
     * Get apellido2Funcionario
     *
     * @return string
     */
    public function getApellido2Funcionario()
    {
        return $this->apellido2Funcionario;
    }

    /**
     * Set emailFuncionario
     *
     * @param string $emailFuncionario
     *
     * @return TblFuncionarios
     */
    public function setEmailFuncionario($emailFuncionario)
    {
        $this->emailFuncionario = $emailFuncionario;

        return $this;
    }

    /**
     * Get emailFuncionario
     *
     * @return string
     */
    public function getEmailFuncionario()
    {
        return $this->emailFuncionario;
    }

    /**
     * Set telefonoFuncionario
     *
     * @param integer $telefonoFuncionario
     *
     * @return TblFuncionarios
     */
    public function setTelefonoFuncionario($telefonoFuncionario)
    {
        $this->telefonoFuncionario = $telefonoFuncionario;

        return $this;
    }

    /**
     * Get telefonoFuncionario
     *
     * @return integer
     */
    public function getTelefonoFuncionario()
    {
        return $this->telefonoFuncionario;
    }

    /**
     * Set celularFuncionario
     *
     * @param integer $celularFuncionario
     *
     * @return TblFuncionarios
     */
    public function setCelularFuncionario($celularFuncionario)
    {
        $this->celularFuncionario = $celularFuncionario;

        return $this;
    }

    /**
     * Get celularFuncionario
     *
     * @return integer
     */
    public function getCelularFuncionario()
    {
        return $this->celularFuncionario;
    }

    /**
     * Set idDeptoFuncional
     *
     * @param \BackendBundle\Entity\TblDepartamentosFuncionales $idDeptoFuncional
     *
     * @return TblFuncionarios
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
     * Set idTipoFuncionario
     *
     * @param \BackendBundle\Entity\TblTiposFuncionarios $idTipoFuncionario
     *
     * @return TblFuncionarios
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
    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuario;


    /**
     * Set idUsuario
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuario
     *
     * @return TblFuncionarios
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
     * @var \BackendBundle\Entity\TblEstados
     */
    private $idEstado;


    /**
     * Set idEstado
     *
     * @param \BackendBundle\Entity\TblEstados $idEstado
     *
     * @return TblFuncionarios
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
}
