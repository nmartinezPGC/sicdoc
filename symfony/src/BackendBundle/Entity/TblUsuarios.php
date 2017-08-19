<?php

namespace BackendBundle\Entity;

/**
 * TblUsuarios
 */
class TblUsuarios
{
    /**
     * @var string
     */
    private $codUsuario;

    /**
     * @var integer
     */
    private $idUsuario;

    /**
     * @var string
     */
    private $nombre1Usuario;

    /**
     * @var string
     */
    private $nombre2Usuario;

    /**
     * @var string
     */
    private $apellido1Usuario;

    /**
     * @var string
     */
    private $apellido2Usuario;

    /**
     * @var string
     */
    private $emailUsuario;

    /**
     * @var string
     */
    private $inicialesUsuario;

    /**
     * @var \BackendBundle\Entity\TblDepartamentosFuncionales
     */
    private $codDeptoFuncional;

    /**
     * @var \BackendBundle\Entity\TblEstados
     */
    private $codEstado;

    /**
     * @var \BackendBundle\Entity\TblTiposFuncionarios
     */
    private $codTipoFuncionario;

    /**
     * @var \BackendBundle\Entity\TblTipoUsuario
     */
    private $codTipoUsuario;


    /**
     * Get codUsuario
     *
     * @return string
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }

    /**
     * Set idUsuario
     *
     * @param integer $idUsuario
     *
     * @return TblUsuarios
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return integer
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set nombre1Usuario
     *
     * @param string $nombre1Usuario
     *
     * @return TblUsuarios
     */
    public function setNombre1Usuario($nombre1Usuario)
    {
        $this->nombre1Usuario = $nombre1Usuario;

        return $this;
    }

    /**
     * Get nombre1Usuario
     *
     * @return string
     */
    public function getNombre1Usuario()
    {
        return $this->nombre1Usuario;
    }

    /**
     * Set nombre2Usuario
     *
     * @param string $nombre2Usuario
     *
     * @return TblUsuarios
     */
    public function setNombre2Usuario($nombre2Usuario)
    {
        $this->nombre2Usuario = $nombre2Usuario;

        return $this;
    }

    /**
     * Get nombre2Usuario
     *
     * @return string
     */
    public function getNombre2Usuario()
    {
        return $this->nombre2Usuario;
    }

    /**
     * Set apellido1Usuario
     *
     * @param string $apellido1Usuario
     *
     * @return TblUsuarios
     */
    public function setApellido1Usuario($apellido1Usuario)
    {
        $this->apellido1Usuario = $apellido1Usuario;

        return $this;
    }

    /**
     * Get apellido1Usuario
     *
     * @return string
     */
    public function getApellido1Usuario()
    {
        return $this->apellido1Usuario;
    }

    /**
     * Set apellido2Usuario
     *
     * @param string $apellido2Usuario
     *
     * @return TblUsuarios
     */
    public function setApellido2Usuario($apellido2Usuario)
    {
        $this->apellido2Usuario = $apellido2Usuario;

        return $this;
    }

    /**
     * Get apellido2Usuario
     *
     * @return string
     */
    public function getApellido2Usuario()
    {
        return $this->apellido2Usuario;
    }

    /**
     * Set emailUsuario
     *
     * @param string $emailUsuario
     *
     * @return TblUsuarios
     */
    public function setEmailUsuario($emailUsuario)
    {
        $this->emailUsuario = $emailUsuario;

        return $this;
    }

    /**
     * Get emailUsuario
     *
     * @return string
     */
    public function getEmailUsuario()
    {
        return $this->emailUsuario;
    }

    /**
     * Set inicialesUsuario
     *
     * @param string $inicialesUsuario
     *
     * @return TblUsuarios
     */
    public function setInicialesUsuario($inicialesUsuario)
    {
        $this->inicialesUsuario = $inicialesUsuario;

        return $this;
    }

    /**
     * Get inicialesUsuario
     *
     * @return string
     */
    public function getInicialesUsuario()
    {
        return $this->inicialesUsuario;
    }

    /**
     * Set codDeptoFuncional
     *
     * @param \BackendBundle\Entity\TblDepartamentosFuncionales $codDeptoFuncional
     *
     * @return TblUsuarios
     */
    public function setCodDeptoFuncional(\BackendBundle\Entity\TblDepartamentosFuncionales $codDeptoFuncional = null)
    {
        $this->codDeptoFuncional = $codDeptoFuncional;

        return $this;
    }

    /**
     * Get codDeptoFuncional
     *
     * @return \BackendBundle\Entity\TblDepartamentosFuncionales
     */
    public function getCodDeptoFuncional()
    {
        return $this->codDeptoFuncional;
    }

    /**
     * Set codEstado
     *
     * @param \BackendBundle\Entity\TblEstados $codEstado
     *
     * @return TblUsuarios
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
     * Set codTipoFuncionario
     *
     * @param \BackendBundle\Entity\TblTiposFuncionarios $codTipoFuncionario
     *
     * @return TblUsuarios
     */
    public function setCodTipoFuncionario(\BackendBundle\Entity\TblTiposFuncionarios $codTipoFuncionario = null)
    {
        $this->codTipoFuncionario = $codTipoFuncionario;

        return $this;
    }

    /**
     * Get codTipoFuncionario
     *
     * @return \BackendBundle\Entity\TblTiposFuncionarios
     */
    public function getCodTipoFuncionario()
    {
        return $this->codTipoFuncionario;
    }

    /**
     * Set codTipoUsuario
     *
     * @param \BackendBundle\Entity\TblTipoUsuario $codTipoUsuario
     *
     * @return TblUsuarios
     */
    public function setCodTipoUsuario(\BackendBundle\Entity\TblTipoUsuario $codTipoUsuario = null)
    {
        $this->codTipoUsuario = $codTipoUsuario;

        return $this;
    }

    /**
     * Get codTipoUsuario
     *
     * @return \BackendBundle\Entity\TblTipoUsuario
     */
    public function getCodTipoUsuario()
    {
        return $this->codTipoUsuario;
    }
}

