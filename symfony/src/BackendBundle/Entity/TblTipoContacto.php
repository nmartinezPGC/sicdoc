<?php

namespace BackendBundle\Entity;

/**
 * TblTipoContacto
 */
class TblTipoContacto
{
    /**
     * @var integer
     */
    private $idTipoContacto;

    /**
     * @var string
     */
    private $descTipoContacto;

    /**
     * @var string
     */
    private $inicialesTipoContacto;

    /**
     * @var boolean
     */
    private $activo = true;


    /**
     * Get idTipoContacto
     *
     * @return integer
     */
    public function getIdTipoContacto()
    {
        return $this->idTipoContacto;
    }

    /**
     * Set descTipoContacto
     *
     * @param string $descTipoContacto
     *
     * @return TblTipoContacto
     */
    public function setDescTipoContacto($descTipoContacto)
    {
        $this->descTipoContacto = $descTipoContacto;

        return $this;
    }

    /**
     * Get descTipoContacto
     *
     * @return string
     */
    public function getDescTipoContacto()
    {
        return $this->descTipoContacto;
    }

    /**
     * Set inicialesTipoContacto
     *
     * @param string $inicialesTipoContacto
     *
     * @return TblTipoContacto
     */
    public function setInicialesTipoContacto($inicialesTipoContacto)
    {
        $this->inicialesTipoContacto = $inicialesTipoContacto;

        return $this;
    }

    /**
     * Get inicialesTipoContacto
     *
     * @return string
     */
    public function getInicialesTipoContacto()
    {
        return $this->inicialesTipoContacto;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return TblTipoContacto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }
}
