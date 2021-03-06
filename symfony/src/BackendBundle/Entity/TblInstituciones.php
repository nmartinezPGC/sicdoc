<?php

namespace BackendBundle\Entity;

/**
 * TblInstituciones
 */
class TblInstituciones
{
    /**
     * @var string
     */
    private $codInstitucion;

    /**
     * @var integer
     */
    private $idInstitucion;

    /**
     * @var string
     */
    private $descInstitucion;

    /**
     * @var string
     */
    private $perfilInstitucion;

    /**
     * @var string
     */
    private $direccionInstitucion;

    /**
     * @var integer
     */
    private $telefonoInstitucion;

    /**
     * @var integer
     */
    private $celularInstitucion;

    /**
     * @var string
     */
    private $emailInstitucion;

    /**
     * @var \BackendBundle\Entity\TblPais
     */
    private $codPais;

    /**
     * @var \BackendBundle\Entity\TblTipoInstitucion
     */
    private $codTipoInstitucion;


    /**
     * Get codInstitucion
     *
     * @return string
     */
    public function getCodInstitucion()
    {
        return $this->codInstitucion;
    }

    /**
     * Set idInstitucion
     *
     * @param integer $idInstitucion
     *
     * @return TblInstituciones
     */
    public function setIdInstitucion($idInstitucion)
    {
        $this->idInstitucion = $idInstitucion;

        return $this;
    }

    /**
     * Get idInstitucion
     *
     * @return integer
     */
    public function getIdInstitucion()
    {
        return $this->idInstitucion;
    }

    /**
     * Set descInstitucion
     *
     * @param string $descInstitucion
     *
     * @return TblInstituciones
     */
    public function setDescInstitucion($descInstitucion)
    {
        $this->descInstitucion = $descInstitucion;

        return $this;
    }

    /**
     * Get descInstitucion
     *
     * @return string
     */
    public function getDescInstitucion()
    {
        return $this->descInstitucion;
    }

    /**
     * Set perfilInstitucion
     *
     * @param string $perfilInstitucion
     *
     * @return TblInstituciones
     */
    public function setPerfilInstitucion($perfilInstitucion)
    {
        $this->perfilInstitucion = $perfilInstitucion;

        return $this;
    }

    /**
     * Get perfilInstitucion
     *
     * @return string
     */
    public function getPerfilInstitucion()
    {
        return $this->perfilInstitucion;
    }

    /**
     * Set direccionInstitucion
     *
     * @param string $direccionInstitucion
     *
     * @return TblInstituciones
     */
    public function setDireccionInstitucion($direccionInstitucion)
    {
        $this->direccionInstitucion = $direccionInstitucion;

        return $this;
    }

    /**
     * Get direccionInstitucion
     *
     * @return string
     */
    public function getDireccionInstitucion()
    {
        return $this->direccionInstitucion;
    }

    /**
     * Set telefonoInstitucion
     *
     * @param integer $telefonoInstitucion
     *
     * @return TblInstituciones
     */
    public function setTelefonoInstitucion($telefonoInstitucion)
    {
        $this->telefonoInstitucion = $telefonoInstitucion;

        return $this;
    }

    /**
     * Get telefonoInstitucion
     *
     * @return integer
     */
    public function getTelefonoInstitucion()
    {
        return $this->telefonoInstitucion;
    }

    /**
     * Set celularInstitucion
     *
     * @param integer $celularInstitucion
     *
     * @return TblInstituciones
     */
    public function setCelularInstitucion($celularInstitucion)
    {
        $this->celularInstitucion = $celularInstitucion;

        return $this;
    }

    /**
     * Get celularInstitucion
     *
     * @return integer
     */
    public function getCelularInstitucion()
    {
        return $this->celularInstitucion;
    }

    /**
     * Set emailInstitucion
     *
     * @param string $emailInstitucion
     *
     * @return TblInstituciones
     */
    public function setEmailInstitucion($emailInstitucion)
    {
        $this->emailInstitucion = $emailInstitucion;

        return $this;
    }

    /**
     * Get emailInstitucion
     *
     * @return string
     */
    public function getEmailInstitucion()
    {
        return $this->emailInstitucion;
    }

    /**
     * Set codPais
     *
     * @param \BackendBundle\Entity\TblPais $codPais
     *
     * @return TblInstituciones
     */
    public function setCodPais(\BackendBundle\Entity\TblPais $codPais = null)
    {
        $this->codPais = $codPais;

        return $this;
    }

    /**
     * Get codPais
     *
     * @return \BackendBundle\Entity\TblPais
     */
    public function getCodPais()
    {
        return $this->codPais;
    }

    /**
     * Set codTipoInstitucion
     *
     * @param \BackendBundle\Entity\TblTipoInstitucion $codTipoInstitucion
     *
     * @return TblInstituciones
     */
    public function setCodTipoInstitucion(\BackendBundle\Entity\TblTipoInstitucion $codTipoInstitucion = null)
    {
        $this->codTipoInstitucion = $codTipoInstitucion;

        return $this;
    }

    /**
     * Get codTipoInstitucion
     *
     * @return \BackendBundle\Entity\TblTipoInstitucion
     */
    public function getCodTipoInstitucion()
    {
        return $this->codTipoInstitucion;
    }
    /**
     * @var \BackendBundle\Entity\TblTipoInstitucion
     */
    private $idTipoInstitucion;

    /**
     * @var \BackendBundle\Entity\TblPais
     */
    private $idPais;


    /**
     * Set codInstitucion
     *
     * @param string $codInstitucion
     *
     * @return TblInstituciones
     */
    public function setCodInstitucion($codInstitucion)
    {
        $this->codInstitucion = $codInstitucion;

        return $this;
    }

    /**
     * Set idTipoInstitucion
     *
     * @param \BackendBundle\Entity\TblTipoInstitucion $idTipoInstitucion
     *
     * @return TblInstituciones
     */
    public function setIdTipoInstitucion(\BackendBundle\Entity\TblTipoInstitucion $idTipoInstitucion = null)
    {
        $this->idTipoInstitucion = $idTipoInstitucion;

        return $this;
    }

    /**
     * Get idTipoInstitucion
     *
     * @return \BackendBundle\Entity\TblTipoInstitucion
     */
    public function getIdTipoInstitucion()
    {
        return $this->idTipoInstitucion;
    }

    /**
     * Set idPais
     *
     * @param \BackendBundle\Entity\TblPais $idPais
     *
     * @return TblInstituciones
     */
    public function setIdPais(\BackendBundle\Entity\TblPais $idPais = null)
    {
        $this->idPais = $idPais;

        return $this;
    }

    /**
     * Get idPais
     *
     * @return \BackendBundle\Entity\TblPais
     */
    public function getIdPais()
    {
        return $this->idPais;
    }
    /**
     * @var \DateTime
     */
    private $fechaIngreso = '2999-12-31';

    /**
     * @var \BackendBundle\Entity\TblUsuarios
     */
    private $idUsuarioCreador;


    /**
     * Set fechaIngreso
     *
     * @param \DateTime $fechaIngreso
     *
     * @return TblInstituciones
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
     * Set idUsuarioCreador
     *
     * @param \BackendBundle\Entity\TblUsuarios $idUsuarioCreador
     *
     * @return TblInstituciones
     */
    public function setIdUsuarioCreador(\BackendBundle\Entity\TblUsuarios $idUsuarioCreador = null)
    {
        $this->idUsuarioCreador = $idUsuarioCreador;

        return $this;
    }

    /**
     * Get idUsuarioCreador
     *
     * @return \BackendBundle\Entity\TblUsuarios
     */
    public function getIdUsuarioCreador()
    {
        return $this->idUsuarioCreador;
    }
}
